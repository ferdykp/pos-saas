<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('reference_number')->unique(); // Kode pengajuan: WDR-20260531-XXXX

            // Informasi Bank Tujuan saat ditarik (mengunci data track record historis)
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('account_name');

            // Nominal Penarikan dana
            $table->decimal('amount', 15, 2);
            $table->decimal('platform_fee', 15, 2)->default(0); // Jika ada tambahan biaya transfer antar bank

            // Status Pengajuan
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_note')->nullable(); // Alasan jika ditolak atau bukti transfer jika diterima
            $table->timestamp('processed_at')->nullable(); // Waktu disetujui / ditolak admin
            $table->timestamps();

            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawal_requests');
    }
};
