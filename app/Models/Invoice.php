<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'subscription_id',
        'plan_id',
        'invoice_number',
        'amount',
        'status',
        'payment_method',
        'snap_token',
        'paid_at',
        'due_date',
    ];

    /**
     * Relasi ke Plan / Paket yang dibeli
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Relasi ke Subscription (Hanya terisi saat invoice sudah LUNAS)
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Relasi ke Tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
