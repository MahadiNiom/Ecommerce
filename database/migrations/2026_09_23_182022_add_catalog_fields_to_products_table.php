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
        Schema::table('products', function (Blueprint $table): void {
            $table->foreignId('category_id')->nullable()->after('name')->constrained()->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->after('category_id')->constrained()->nullOnDelete();
            $table->text('description')->nullable()->after('brand_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('category_id');
            $table->dropConstrainedForeignId('brand_id');
            $table->dropColumn('description');
        });
    }
};
