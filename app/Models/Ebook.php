<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Ebook extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'price', 'file_path'
    ];

    // Expose automatiquement l'URL publique du PDF dans les réponses JSON
    protected $appends = ['file_url'];

    public function getFileUrlAttribute()
    {
        if (!$this->file_path) {
            return null;
        }
        // Si on a déjà une URL absolue en base, on la renvoie telle quelle.
        if (preg_match('~^https?://~i', $this->file_path)) {
            return $this->file_path;
        }
        // Sinon on construit l’URL publique via le disque "public"
        return Storage::disk('public')->url($this->file_path);
    }
}