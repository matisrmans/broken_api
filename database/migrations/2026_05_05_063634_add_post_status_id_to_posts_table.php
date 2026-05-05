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
            // Note: In a real scenario, we'd ensure status 2 is 'draft'.
            // Here we assume the seeder is run and 'draft' is ID 2.
            $table->foreignId('post_status_id')->default(2)->constrained('post_statuses')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['post_status_id']);
            $table->dropColumn('post_status_id');
        });
    }
};
