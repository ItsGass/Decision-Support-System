<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Motor;

class PenjualanAnalisis extends Model
{
    protected $table = 'penjualan_analisis';

    protected $fillable = [
        'motor_id',
        'jumlah',
        'percent',
        'dataset_name'
    ];

    public function motor()
    {
        return $this->belongsTo(Motor::class, 'motor_id');
    }
}