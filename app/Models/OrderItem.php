<?php

// app/Models/OrderItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_name', 'unit_factor',
        'requires_preparation', 'discount_amount',
        'order_id',
        'product_id',
        'variant_id',
        'product_name',
        'quantity',
        'reserved_stock',
        'note', 'addons', 'reserved_materials', 'unit_cost',
        'price',
        'subtotal',
    ];

    protected $casts = ['requires_preparation' => 'boolean', 'addons' => 'array', 'reserved_materials' => 'array'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
