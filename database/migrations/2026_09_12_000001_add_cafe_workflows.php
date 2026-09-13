<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->uuid('checkout_key')->nullable();
            $table->string('request_hash', 64)->nullable();
            $table->text('qr_url')->nullable();
            $table->string('kitchen_status')->nullable()->index();
            $table->timestamp('sold_at')->nullable()->index();
            $table->unique(['tenant_id', 'checkout_key']);
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->text('note')->nullable();
            $table->json('addons')->nullable();
            $table->json('reserved_materials')->nullable();
            $table->decimal('unit_cost', 15, 2)->nullable();
        });
        Schema::create('product_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('price')->default(0);
            $table->unsignedInteger('cost')->nullable();
            $table->timestamps();
        });
        Schema::create('product_material', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->primary(['product_id', 'material_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_material');
        Schema::dropIfExists('product_addons');
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('variant_id');
            $table->dropColumn(['note', 'addons', 'reserved_materials', 'unit_cost']);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['tenant_id', 'checkout_key']);
            $table->dropColumn(['checkout_key', 'request_hash', 'qr_url', 'kitchen_status', 'sold_at']);
        });
    }
};
