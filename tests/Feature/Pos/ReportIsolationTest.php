<?php

namespace Tests\Feature\Pos;

use App\Exports\Sheets\ShiftLogsSheet;
use App\Exports\Sheets\TopProductsSheet;
use App\Jobs\ExportReportJob;
use App\Models\ReportExport;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Storage;

class ReportIsolationTest extends PosTestCase
{
    public function test_queued_export_keeps_original_tenant_and_is_private_without_auth(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $user = $this->shop();
        $other = $this->shop();
        $product = $this->product($user, ['product_name' => 'Barang toko A']);
        $otherProduct = $this->product($other, ['product_name' => 'Rahasia toko B']);
        $this->shift($user);
        $this->shift($other);
        $this->actingAs($user)->postJson('/pos', $this->checkout($product))->assertOk();
        $this->actingAs($other)->postJson('/pos', $this->checkout($otherProduct))->assertOk();
        $export = ReportExport::create(['tenant_id' => $user->tenant_id, 'user_id' => $user->id, 'report_type' => 'Test', 'start_date' => today(), 'end_date' => today(), 'status' => 'pending']);
        auth()->forgetGuards();
        $sheet = new TopProductsSheet(today()->toDateString(), today()->toDateString(), $user->tenant_id);
        $this->assertEquals(['Barang toko A'], $sheet->collection()->pluck('product_name')->all());
        $this->assertCount(1, (new ShiftLogsSheet(today()->toDateString(), today()->toDateString(), $user->tenant_id))->collection());
        (new ExportReportJob(today()->toDateString(), today()->toDateString(), $export->id))->handle(app(GeminiService::class));
        $this->assertEquals('completed', $export->fresh()->status);
        Storage::disk('local')->assertExists($export->fresh()->file_path);
        Storage::disk('public')->assertMissing($export->fresh()->file_path);
        $this->actingAs($other)->get('/reports/exports/download/'.$export->id)->assertNotFound();
        $this->actingAs($user)->get('/reports/exports/download/'.$export->id)->assertOk();
    }
}
