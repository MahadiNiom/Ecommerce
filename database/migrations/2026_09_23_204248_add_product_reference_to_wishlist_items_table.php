<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Allow a wishlist item to reference either a product variant or the product itself.
     */
    public function up(): void
    {
        Schema::table('wishlist_items', function (Blueprint $table) {
            $table->foreignId('product_variant_id')->nullable()->change();
            $table->foreignId('product_id')->nullable()->after('product_variant_id')->constrained('products')->cascadeOnDelete();
            $table->dropUnique('wishlist_items_user_id_product_variant_id_unique');
        });

        DB::statement('CREATE UNIQUE INDEX wishlist_items_variant_unique ON wishlist_items (user_id, product_variant_id) WHERE product_variant_id IS NOT NULL');
        DB::statement('CREATE UNIQUE INDEX wishlist_items_product_unique ON wishlist_items (user_id, product_id) WHERE product_id IS NOT NULL');
    }

    /**
     * Reverse the migrations. Only safe when no variant-less product lines exist.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX wishlist_items_variant_unique');
        DB::statement('DROP INDEX wishlist_items_product_unique');

        Schema::table('wishlist_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_id');
            $table->unique(['user_id', 'product_variant_id']);
        });
    }
};
