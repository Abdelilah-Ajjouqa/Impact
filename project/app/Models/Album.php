<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $fillable = [
        'title',
        'description',
        'cover_image',
        'user_id',
        'is_private'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function posts()
    {
        return $this->belongsToMany(Posts::class, 'album_post', 'album_id', 'post_id')
            ->withTimestamps();
    }
}
