<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', fn (Blueprint $table) => $table->boolean('requires_preparation')->nullable());
        Schema::table('order_items', function (Blueprint $table) {
            $table->boolean('requires_preparation')->nullable();
            $table->decimal('discount_amount', 15, 2)->nullable();
        });
        Schema::table('orders', fn (Blueprint $table) => $table->timestamp('service_due_at')->nullable()->index());
    }

    public function down(): void
    {
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn('requires_preparation'));
        Schema::table('order_items', fn (Blueprint $table) => $table->dropColumn(['requires_preparation', 'discount_amount']));
        Schema::table('orders', fn (Blueprint $table) => $table->dropColumn('service_due_at'));
    }
};
