<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('motor', function (Blueprint $table) {
            $table->enum('category', ['fast_moving', 'premium', 'slow_moving'])
                  ->default('fast_moving')
                  ->after('nama'); // sesuaikan posisi kolom
        });
    }

    public function down(): void
    {
        Schema::table('motor', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};