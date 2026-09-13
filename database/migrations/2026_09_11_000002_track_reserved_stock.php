<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Existing catalogs must explicitly enable tracking; no historical stock is inferred.
            $table->boolean('manage_stock')->default(false);
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedInteger('reserved_stock')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('order_items', fn (Blueprint $table) => $table->dropColumn('reserved_stock'));
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn('manage_stock'));
    }
};
