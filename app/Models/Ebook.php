<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Ebook extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'price', 'file_path'
    ];

    // Ajoute automatiquement file_url au JSON
    protected $appends = ['file_url'];

    public function getFileUrlAttribute()
    {
        if (!$this->file_path) return null;
        if (preg_match('~^https?://~i', $this->file_path)) {
            return $this->file_path;
        }
        return Storage::disk('public')->url($this->file_path);
    }
}
