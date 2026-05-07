<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Stok;
use App\Models\PenjualanAnalisis;
use App\Models\Opini;

class Motor extends Model
{
    protected $table = 'motor';
    

    protected $fillable = ['nama', 'category'];

    public function stok()
    {
        return $this->hasOne(Stok::class, 'motor_id')->withDefault([
            'stok_sisa' => 0
        ]);
    }

    // 🔥 TAMBAHAN (WAJIB BUAT CLAUDE)
    public function penjualanAnalisis()
    {
        return $this->hasOne(PenjualanAnalisis::class, 'motor_id');
    }

    public function opini()
    {
        return $this->hasMany(Opini::class, 'motor_id');
    }

    public function trend()
    {
        return $this->hasMany(TrendAnalisis::class, 'motor_id');
    }
}