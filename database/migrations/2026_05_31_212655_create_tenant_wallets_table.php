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
        Schema::create('tenant_wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->unique();
            $table->string('bank_name')->nullable();       // Contoh: BCA, Mandiri
            $table->string('account_number')->nullable();  // Nomor Rekening
            $table->string('account_name')->nullable();    // Nama di Rekening
            $table->integer('balance')->default(0);        // Total Saldo yang bisa ditarik
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_wallets');
    }
};
