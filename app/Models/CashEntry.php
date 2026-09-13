<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class CashEntry extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'shift_id', 'user_id', 'order_id', 'operation_key', 'kind', 'amount', 'reason'];

    protected $casts = ['amount' => 'decimal:2'];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
