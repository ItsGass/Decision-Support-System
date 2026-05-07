<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('prompts', function (Blueprint $table) {
        $table->id();
        $table->string('nama_prompt'); // Misal: "Prompt Default Kimi", "Prompt Galak"
        $table->longText('isi_prompt'); // Isi teks promptnya
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prompts');
    }
};
