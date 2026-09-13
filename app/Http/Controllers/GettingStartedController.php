<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shift;
use App\Models\Tenant;
use App\Support\BusinessProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GettingStartedController extends Controller
{
    public function index()
    {
        $hasMenu = Product::exists();
        $hasShift = Shift::where('user_id', auth()->id())->where('status', 'open')->exists();
        $hasSale = Order::where('payment_status', 'paid')->exists();

        return view('getting-started.index', compact('hasMenu', 'hasShift', 'hasSale'));
    }

    private function saveRows(array $rows): void
    {
        DB::transaction(function () use ($rows) {
            $tenant = Tenant::lockForUpdate()->findOrFail(auth()->user()->tenant_id);
            $plan = $tenant->currentPlan();
            if (! $plan || Product::count() + count($rows) > $plan->max_products) {
                throw ValidationException::withMessages(['file' => 'Jumlah katalog melebihi batas paket.']);
            }
            foreach ($rows as $row) {
                $category = Category::firstOrCreate(['tenant_id' => $tenant->id, 'name' => $row['category']], ['slug' => Str::slug($row['category']).'-'.Str::uuid()]);
                Product::create(['tenant_id' => $tenant->id, 'category_id' => $category->id, 'sku' => 'ITEM-'.Str::uuid(), 'product_name' => $row['name'], 'sell_price' => $row['price'], 'cost_price' => $row['cost'] ?? 0, 'type' => $row['type'] ?? 'product', 'stock' => ($row['type'] ?? 'product') === 'service' ? 0 : ($row['stock'] ?? 0), 'manage_stock' => ($row['type'] ?? 'product') !== 'service' && (bool) ($row['manage_stock'] ?? false), 'is_active' => true]);
            }
        });
    }

    public function sample()
    {
        DB::transaction(function () {
            Tenant::lockForUpdate()->findOrFail(auth()->user()->tenant_id);
            if (Product::exists()) {
                throw ValidationException::withMessages(['menu' => 'Contoh hanya dapat ditambahkan ke katalog kosong.']);
            }
            $this->saveRows(BusinessProfile::samples(auth()->user()->tenant->businessType()));
        });

        return back()->with('success', 'Empat contoh katalog ditambahkan. Sesuaikan harga sebelum mulai menjual.');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt,xlsx|max:2048']);
        $path = $request->file('file')->getRealPath();
        try {
            $type = IOFactory::identify($path);
        } catch (\Throwable $e) {
            throw ValidationException::withMessages(['file' => 'File tidak dapat dibaca. Gunakan template CSV atau XLSX yang valid.']);
        }
        abort_unless(in_array($type, ['Csv', 'Xlsx'], true), 422, 'Gunakan CSV atau XLSX.');
        $reader = IOFactory::createReader($type);
        $reader->setReadDataOnly(true);
        try {
            $info = $reader->listWorksheetInfo($path);
        } catch (\Throwable $e) {
            throw ValidationException::withMessages(['file' => 'Struktur file katalog tidak dapat dibaca.']);
        }
        if (empty($info[0]['worksheetName'])) {
            throw ValidationException::withMessages(['file' => 'File tidak memiliki lembar katalog.']);
        }
        if (($info[0]['totalRows'] ?? 0) > 501 || ($info[0]['totalColumns'] ?? 0) > 7) {
            throw ValidationException::withMessages(['file' => 'Maksimal 500 katalog dan 7 kolom per file.']);
        }
        $reader->setLoadSheetsOnly($info[0]['worksheetName']);
        try {
            $book = $reader->load($path);
        } catch (\Throwable $e) {
            throw ValidationException::withMessages(['file' => 'File katalog rusak atau tidak sesuai format.']);
        }
        $sheet = $book->getSheet(0)->toArray(null, false, false, false);
        $book->disconnectWorksheets();
        $header = array_map(fn ($v) => trim((string) $v), array_shift($sheet) ?? []);
        if (! in_array($header, [['name', 'category', 'price', 'cost', 'stock', 'manage_stock'], ['name', 'category', 'price', 'cost', 'stock', 'manage_stock', 'type']], true)) {
            throw ValidationException::withMessages(['file' => 'Header harus sesuai template: name,category,price,cost,stock,manage_stock,type.']);
        }
        $rows = [];
        foreach ($sheet as $index => $cells) {
            if (! array_filter($cells, fn ($v) => $v !== null && $v !== '')) {
                continue;
            }
            $row = array_combine($header, array_pad($cells, count($header), null));
            $validator = validator($row, ['name' => 'required|string|max:255', 'category' => 'required|string|max:100', 'price' => 'required|integer|min:0|max:100000000', 'cost' => 'nullable|integer|min:0|max:100000000', 'stock' => 'nullable|integer|min:0|max:1000000', 'manage_stock' => 'required|boolean', 'type' => 'sometimes|required|in:product,service']);
            if ($validator->fails()) {
                throw ValidationException::withMessages(['file' => 'Baris '.($index + 2).': '.$validator->errors()->first()]);
            }
            $rows[] = $validator->validated();
        }
        if (! $rows) {
            throw ValidationException::withMessages(['file' => 'File tidak berisi katalog.']);
        }
        $this->saveRows($rows);

        return back()->with('success', count($rows).' katalog berhasil diimpor.');
    }

    public function template()
    {
        return response("name,category,price,cost,stock,manage_stock,type\nBuku tulis,Barang,8000,5000,20,1,product\nJasa pemasangan,Layanan,25000,0,0,0,service\n", 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="template-katalog-growpos.csv"']);
    }

    public function export()
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['name', 'category', 'price', 'cost', 'stock', 'manage_stock', 'type'], ',', '"', '');
            foreach (Product::with('category')->lazyById(200) as $product) {
                $safe = fn ($value) => preg_match('/^[=+@\-\t\r]/', $value) ? "'".$value : $value;
                fputcsv($out, [$safe($product->product_name), $safe($product->category->name), (int) $product->sell_price, (int) $product->cost_price, $product->stock, (int) $product->manage_stock, $product->type], ',', '"', '');
            }
            fclose($out);
        }, 'katalog-growpos.csv', ['Content-Type' => 'text/csv']);
    }
}
