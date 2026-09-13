<?php

namespace App\Services;

use App\Models\CashEntry;
use Carbon\CarbonInterface;

class CashPeriodSummary
{
    public function forTenant(int $tenantId, CarbonInterface $start, CarbonInterface $end): array
    {
        $amounts = CashEntry::withoutGlobalScopes()->where('tenant_id', $tenantId)->whereBetween('created_at', [$start, $end])
            ->select('kind')->selectRaw('SUM(amount) as total')->groupBy('kind')->pluck('total', 'kind');

        return [
            'receipts' => (float) ($amounts['sale'] ?? 0) + (float) ($amounts['payment'] ?? 0),
            'refunds' => -(float) ($amounts['refund'] ?? 0),
            'expenses' => -(float) ($amounts['expense'] ?? 0),
            'net' => (float) $amounts->sum(),
        ];
    }
}
