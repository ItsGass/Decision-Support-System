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
    Schema::table('penjualan', function (Blueprint $table) {
        $table->string('dataset_name')->nullable();
    });

    Schema::table('stok', function (Blueprint $table) {
        $table->string('snapshot_name')->nullable();
    });

    
}

public function down()
{
    Schema::table('penjualan', function (Blueprint $table) {
        $table->dropColumn('dataset_name');
    });

    Schema::table('stok', function (Blueprint $table) {
        $table->dropColumn('snapshot_name');
    });

}
};
