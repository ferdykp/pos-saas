<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('cash_tracked')->default(false);
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable();
        });
        Schema::create('cash_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->restrictOnDelete();
            $table->foreignId('shift_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->restrictOnDelete();
            $table->uuid('operation_key');
            $table->string('kind');
            $table->decimal('amount', 15, 2);
            $table->string('reason', 500);
            $table->timestamps();
            $table->unique(['tenant_id', 'operation_key']);
            $table->index(['tenant_id', 'shift_id', 'kind']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_entries');
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cancelled_by');
            $table->dropColumn(['cash_tracked', 'cancellation_reason', 'cancelled_at']);
        });
    }
};
