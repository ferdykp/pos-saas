<?php

// app/Models/ServiceOrder.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'order_id',
        'service_status',
        'estimated_finish',
        'finished_at',
        'notes',
    ];

    protected $casts = [
        'estimated_finish' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
