<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class TenantWallet extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'bank_name',
        'account_number',
        'account_name',
        'balance',
    ];

    protected $casts = [
        'balance' => 'integer',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function withdrawalRequests()
    {
        return $this->hasMany(WithdrawlRequest::class, 'tenant_id', 'tenant_id');
    }

    /**
     * Helper: apakah saldo cukup untuk jumlah tertentu.
     */
    public function hasSufficientBalance(int $amount): bool
    {
        return $this->balance >= $amount;
    }
}
