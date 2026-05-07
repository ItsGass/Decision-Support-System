<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrendAnalisis extends Model
{
    protected $table = 'trend_analisis';

    protected $fillable = [
        'motor_id',
        'periode',
        'skor_trend',
        'alasan_ai'
    ];

    // relasi ke motor
    public function motor()
    {
        return $this->belongsTo(Motor::class, 'motor_id');
    }
}