<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;

class OrdersReportExport implements WithMultipleSheets
{
    use Exportable;

    protected $startDate;
    protected $endDate;
    protected $aiAnalysisText;

    public function __construct($startDate, $endDate, $aiAnalysisText)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->aiAnalysisText = $aiAnalysisText;
    }

    public function sheets(): array
    {
        return [
            // Kirim teks AI ke dalam FinancialSummarySheet
            new Sheets\FinancialSummarySheet($this->startDate, $this->endDate, $this->aiAnalysisText),
            new Sheets\TopProductsSheet($this->startDate, $this->endDate),
            new Sheets\ShiftLogsSheet($this->startDate, $this->endDate),
        ];
    }
}
