<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class WithdrawlRequest extends Model
{
    use BelongsToTenant;

    // WAJIB diset manual karena nama class (WithdrawlRequest, tanpa "a")
    // tidak cocok dengan nama tabel migration (withdrawal_requests, dengan "a")
    protected $table = 'withdrawal_requests';

    protected $fillable = [
        'tenant_id',
        'reference_number',
        'bank_name',
        'account_number',
        'account_name',
        'amount',
        'platform_fee',
        'status',
        'admin_note',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Total dana yang benar-benar diterima tenant setelah dipotong fee.
     */
    public function getNetAmountAttribute(): float
    {
        return (float) $this->amount - (float) $this->platform_fee;
    }
}
