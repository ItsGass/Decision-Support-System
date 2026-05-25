<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

    class Prompt extends Model
{
    protected $fillable = ['nama_prompt', 'isi_prompt', 'user_id'];
}
