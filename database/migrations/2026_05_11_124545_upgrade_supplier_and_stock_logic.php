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
        // 1. Lengkapi data di tabel suppliers
        Schema::table('suppliers', function (Blueprint $table) {
            $table->integer('term_of_payment')->default(0)->after('address'); // dalam hari
            $table->string('bank_name')->nullable()->after('term_of_payment');
            $table->string('bank_account_number')->nullable()->after('bank_name');
            $table->boolean('is_active')->default(true)->after('bank_account_number');
        });

        // 2. Hubungkan mutasi stok ke supplier dan harga beli
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->after('material_id')->constrained()->nullOnDelete();
            $table->decimal('purchase_price', 15, 2)->default(0)->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
