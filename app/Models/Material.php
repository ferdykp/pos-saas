<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\BelongsToTenant;

class Material extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = ['tenant_id', 'name', 'sku', 'unit', 'stock', 'min_stock'];

    // protected static function booted()
    // {
    //     static::addGlobalScope('tenant', function ($builder) {
    //         if (auth()->check()) {
    //             $builder->where('tenant_id', auth()->user()->tenant_id);
    //         }
    //     });
    // }
}
