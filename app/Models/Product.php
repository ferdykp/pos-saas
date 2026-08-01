<?php

// app/Models/Product.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Concerns\BelongsToTenant;

class Product extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'category_id',
        'sku',
        'barcode',
        'product_name',
        'type',
        'image',
        'cost_price',
        'sell_price',
        'stock',
        'min_stock',
        'desc',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // protected static function booted()
    // {
    //     static::addGlobalScope('tenant', function ($builder) {
    //         if (auth()->check()) {
    //             // Hanya ambil produk milik tenant yang sedang aktif login
    //             $builder->where('tenant_id', auth()->user()->tenant_id);
    //         }
    //     });

    //     // Otomatis isi tenant_id saat membuat produk baru
    //     static::creating(function ($product) {
    //         if (auth()->check()) {
    //             $product->tenant_id = auth()->user()->tenant_id;
    //         }
    //     });
    // }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    // app/Models/Product.php

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }
    public function discounts(): BelongsToMany
    {
        // Parameter kedua adalah nama tabel pivot yang kita buat di migration
        return $this->belongsToMany(Discount::class, 'discount_product');
    }
}
