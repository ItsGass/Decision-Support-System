<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Motor;

class Stok extends Model
{
    protected $table = 'stok';

    protected $fillable = [
        'motor_id',
        'stok_sisa',
        'snapshot_name'
    ];

    protected $casts = [
        'stok_sisa' => 'integer' // 🔥 FIX (tadi salah 'jumlah')
    ];

    public function motor()
    {
        return $this->belongsTo(Motor::class, 'motor_id');
    }
}