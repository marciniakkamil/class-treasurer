<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('collections', function (Blueprint $table): void {
            $table->index(['user_id', 'is_active', 'created_at'], 'collections_user_active_created_idx');
            $table->index('school_year', 'collections_school_year_idx');
        });
    }

    public function down(): void
    {
        Schema::table('collections', function (Blueprint $table): void {
            $table->dropIndex('collections_user_active_created_idx');
            $table->dropIndex('collections_school_year_idx');
        });
    }
};
