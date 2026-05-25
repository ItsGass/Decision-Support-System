<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    // Hapus $guarded, ganti jadi ini:
    protected $fillable = ['key', 'value', 'user_id']; 
}