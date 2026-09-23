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
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropConstrainedForeignId('variant_id');
            $table->dropConstrainedForeignId('variant_option_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * SQLite cannot re-add a NOT NULL column without a default, so the
     * columns are restored as nullable as an approximate rollback.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->foreignId('variant_id')->nullable();
            $table->foreignId('variant_option_id')->nullable();
        });
    }
};
