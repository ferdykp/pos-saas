<?php

// app/Models/Inventory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\BelongsToTenant;

class Inventory extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'product_id',
        'quantity',
    ];

    // protected static function booted()
    // {
    //     static::addGlobalScope('tenant', function ($builder) {
    //         if (auth()->check()) {
    //             $builder->where('tenant_id', auth()->user()->tenant_id);
    //         }
    //     });
    // }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
