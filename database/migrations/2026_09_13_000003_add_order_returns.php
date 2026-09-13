<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_exports', fn (Blueprint $table) => $table->foreignId('tenant_id')->nullable()->constrained()->restrictOnDelete());
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('points_awarded')->default(0);
        });
        Schema::create('order_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->restrictOnDelete();
            $table->foreignId('order_id')->constrained()->restrictOnDelete();
            $table->foreignId('order_item_id')->constrained()->restrictOnDelete();
            $table->foreignId('cash_entry_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('amount', 15, 2);
            $table->boolean('restock');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_returns');
        Schema::table('report_exports', fn (Blueprint $table) => $table->dropConstrainedForeignId('tenant_id'));
        Schema::table('orders', fn (Blueprint $table) => $table->dropColumn('points_awarded'));
    }
};
