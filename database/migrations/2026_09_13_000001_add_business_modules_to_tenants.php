<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', fn (Blueprint $table) => $table->json('business_modules')->nullable());
        Schema::table('orders', function (Blueprint $table) {
            $table->string('service_status')->nullable()->index();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assigned_user_id');
            $table->dropColumn('service_status');
        });
        Schema::table('tenants', fn (Blueprint $table) => $table->dropColumn('business_modules'));
    }
};
