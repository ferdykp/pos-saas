<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('base_unit', 20)->default('pcs');
            $table->boolean('allow_fraction')->default(false);
            $table->json('price_tiers')->nullable();
        });
        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name', 30);
            $table->decimal('factor', 15, 3);
            $table->unsignedBigInteger('price');
            $table->unique(['product_id', 'name']);
            $table->timestamps();
        });
        foreach (['products' => ['stock', 'min_stock'], 'materials' => ['stock', 'min_stock'], 'product_variants' => ['stock'], 'order_items' => ['quantity', 'reserved_stock'], 'order_returns' => ['quantity'], 'stock_movements' => ['quantity', 'before_stock', 'after_stock']] as $name => $columns) {
            Schema::table($name, function (Blueprint $table) use ($columns) {
                foreach ($columns as $column) {
                    $table->decimal($column, 18, 3)->default(0)->change();
                }
            });
        }
        Schema::table('order_items', function (Blueprint $table) {
            // Snapshots survive later package edits/deletion and drive refunds/restocking.
            $table->string('unit_name', 30)->default('pcs');
            $table->decimal('unit_factor', 15, 3)->default(1);
        });
    }

    public function down(): void
    {
        // Keep widened decimal quantity columns in place: narrowing them back to integers
        // could silently destroy fractional inventory/history during a rollback.
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['unit_name', 'unit_factor']);
        });
        Schema::dropIfExists('product_units');
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['base_unit', 'allow_fraction', 'price_tiers']);
        });
    }
};
