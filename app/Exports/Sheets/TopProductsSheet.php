<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TopProductsSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function title(): string
    {
        return 'Produk Terlaris';
    }

    public function collection()
    {
        // Query yang sama persis dengan yang ada di ReportController Anda
        return DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
            ->where('orders.payment_status', 'paid')
            ->groupBy('products.id', 'products.product_name')
            ->orderBy('total_qty', 'desc')
            ->select(
                'products.product_name',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.subtotal) as total_sales')
            )
            ->take(5)
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama Produk',
            'Jumlah Terjual',
            'Total Omzet Produk'
        ];
    }

    public function map($row): array
    {
        return [
            $row->product_name,
            $row->total_qty . 'x',
            'Rp ' . number_format($row->total_sales, 0, ',', '.'),
        ];
    }
}
