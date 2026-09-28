<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->timestamp('stock_released_at')->nullable()->after('status');
        });

        Schema::table('order_items', function (Blueprint $table): void {
            $table->foreignId('product_id')->nullable()->after('product_variant_id')->constrained()->nullOnDelete();
            $table->boolean('stock_deducted')->default(false)->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('product_id');
            $table->dropColumn('stock_deducted');
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn('stock_released_at');
        });
    }
};
