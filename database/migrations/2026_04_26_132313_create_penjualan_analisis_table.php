<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('penjualan_analisis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('motor_id')->constrained('motor')->cascadeOnDelete();
            $table->integer('jumlah');
            $table->float('percent');
            $table->string('dataset_name')->nullable();
            $table->timestamps(); // created_at = waktu snapshot
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan_analisis');
    }
};
