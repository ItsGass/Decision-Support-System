<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'penjualan';

    protected $fillable = [
        'motor_id',
        'tanggal',
        'jumlah',
        'dataset_name',
        'user_id'
    ];
    public function motor()
    {
        return $this->belongsTo(Motor::class);
    }
}