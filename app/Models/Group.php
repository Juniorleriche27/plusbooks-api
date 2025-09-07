<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    protected $fillable = ['owner_id','name','description','is_public'];

    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): BelongsToMany {
        return $this->belongsToMany(User::class, 'group_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function posts() {
        return $this->hasMany(Post::class);
    }
}
