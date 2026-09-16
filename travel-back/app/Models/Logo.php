<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logo extends Model
{
    protected $table = 'logos';
    
    protected $fillable = ['title', 'text_logo', 'round_logo'];
}
