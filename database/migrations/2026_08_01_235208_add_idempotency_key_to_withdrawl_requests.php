<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            // Dikirim dari frontend (misal UUID yang di-generate saat form dibuka).
            // Kalau user klik submit 2x, request kedua dengan idempotency_key
            // yang sama akan ditolak sistem, bukan bikin 2 pengajuan.
            $table->string('idempotency_key')->nullable()->unique()->after('reference_number');
        });
    }

    public function down(): void
    {
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->dropColumn('idempotency_key');
        });
    }
};
