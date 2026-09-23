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
        Schema::table('categories', function (Blueprint $table): void {
            $table->foreignId('parent_id')->nullable()->after('name')->constrained('categories')->nullOnDelete();
            $table->dropUnique('categories_name_unique');
            $table->unique(['name', 'parent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->dropUnique(['name', 'parent_id']);
            $table->unique('name');
            $table->dropConstrainedForeignId('parent_id');
        });
    }
};
