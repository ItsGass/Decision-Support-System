<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trend_analisis', function (Blueprint $table) {
            $table->id();

            // relasi ke motor
            $table->foreignId('motor_id')
                  ->constrained('motor')
                  ->cascadeOnDelete();

            // periode trend (contoh: "Q1 2026" / "Mei 2026")
            $table->string('periode');

            // skor dari Gemini (0.1 - 1.0)
            $table->decimal('skor_trend', 3, 2)->default(0.1);

            // optional: alasan dari AI
            $table->text('alasan_ai')->nullable();

            $table->timestamps();

            // biar gak double data per motor per periode
            $table->unique(['motor_id', 'periode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trend_analisis');
    }
};