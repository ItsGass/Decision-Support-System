<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opini', function (Blueprint $table) {
            $table->id();

            // relasi ke motor
            $table->foreignId('motor_id')
                ->nullable()
                ->constrained('motor') 
                ->nullOnDelete();

            // data utama
            $table->string('nama')->nullable();
            $table->date('tanggal')->nullable();
            $table->text('isi');

            // grouping kayak penjualan
            $table->string('dataset_name')->nullable();

            // 🔥 future AI / sentiment
            $table->string('sentiment')->nullable(); // positif / negatif / netral
            $table->float('score')->nullable();      // 0–1

            // sumber data
            $table->string('sumber')->nullable(); // pelanggan / mekanik / dll

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opini');
    }
};