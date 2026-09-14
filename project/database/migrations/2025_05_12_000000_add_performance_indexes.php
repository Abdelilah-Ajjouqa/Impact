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
        Schema::table('posts', function (Blueprint $table) {
            $table->index(['status', 'created_at']);
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('media', function (Blueprint $table) {
            $table->index(['post_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('media', function (Blueprint $table) {
            $table->dropIndex(['post_id', 'type']);
        });
    }
};

