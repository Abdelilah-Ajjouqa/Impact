<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'media',
        'user_id',
        'status'
    ];

    // Post status constants
    const is_public = 'public';
    const is_private = 'private';
    const is_archived = 'archived';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comments::class, 'post_id');
    }

    public function mediaContent()
    {
        return $this->hasMany(Media::class, 'post_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag', 'post_id', 'tag_id');
    }

    public function savedPosts()
    {
        return $this->hasMany(SavedPost::class, 'post_id');
    }

    public function albums()
    {
        return $this->belongsToMany(Album::class, 'album_post', 'post_id', 'album_id')
            ->withTimestamps();
    }
}
