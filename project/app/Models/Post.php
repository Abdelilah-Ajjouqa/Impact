<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';

    protected $fillable = [
        'title',
        'description',
        'user_id',
        'status',
    ];

    // Post status constants (PSR-12 standard)
    const STATUS_PUBLIC = 'public';
    const STATUS_PRIVATE = 'private';
    const STATUS_ARCHIVED = 'archived';

    // Backwards-compatible aliases
    const is_public = self::STATUS_PUBLIC;
    const is_private = self::STATUS_PRIVATE;
    const is_archived = self::STATUS_ARCHIVED;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id');
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

