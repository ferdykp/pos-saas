<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();

            // FIX: Dibuat nullable() agar Transaksi Tanpa Nama (Guest) bisa disimpan
            $table->foreignId('customer_id')->nullable()->constrained()->cascadeOnDelete();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number');
            $table->string('table_number')->nullable(); // Opsional untuk nomor meja

            // Logika Nominal Keuangan
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0); // Sudah ada, dipertahankan
            $table->decimal('tax', 15, 2)->default(0);      // Sudah ada, dipertahankan
            $table->decimal('grand_total', 15, 2)->default(0);

            // BARU: Pencatatan Detail Pembayaran Tunai & Kembalian
            $table->string('payment_method')->default('cash'); // 'cash', 'qris', 'transfer', dll
            $table->decimal('paid_amount', 15, 2)->default(0); // Uang yang diterima
            $table->decimal('change_amount', 15, 2)->default(0); // Uang kembalian

            // Status Transaksi
            $table->enum('payment_status', ['paid', 'unpaid']);
            $table->enum('order_status', ['pending', 'completed', 'cancelled'])->default('completed');
            $table->text('note')->nullable();
            $table->timestamps();

            // Indexing untuk mempercepat pencarian data laporan
            $table->index('tenant_id');
            $table->index('invoice_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
