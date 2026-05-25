<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Motor;

class Opini extends Model
{
    protected $table = 'opini';

    protected $fillable = [
        'motor_id',
        'nama',
        'isi',
        'tanggal',
        'dataset_name',
        'sentiment',
        'score',
        'sumber',
        'analysis_name',
        'user_id'
    ];

    // 🔥 OPTIONAL TAPI BAGUS (BIAR GAK ANEH)
    protected $casts = [
        'score' => 'integer',
        'tanggal' => 'date'
    ];

    // 🔗 RELASI KE MOTOR (SUDAH BENAR, CUMA DIPERJELAS)
    public function motor()
    {
        return $this->belongsTo(Motor::class, 'motor_id');
    }
}