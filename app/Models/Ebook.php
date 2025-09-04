<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ebook extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'price', 'file_path'
    ];
}
