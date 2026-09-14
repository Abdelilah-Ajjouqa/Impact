<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'avatar',
        'cover',
        'role',
        'is_active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getRole(string $role)
    {
        return $this->role === $role;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function savedPosts()
    {
        return $this->hasMany(SavedPost::class);
    }

    public function albums()
    {
        return $this->hasMany(Album::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')->withTimestamps();
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')->withTimestamps();
    }

    public function isFollowing(int|User $user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;
        return $this->following()->where('following_id', $userId)->exists();
    }

    public function isFollowedBy(int|User $user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;
        return $this->followers()->where('follower_id', $userId)->exists();
    }

    public function getAvatarUrlAttribute(): string
    {
        if (!$this->avatar) {
            return 'https://placehold.co/150';
        }
        if (str_starts_with($this->avatar, 'http')) {
            return $this->avatar;
        }
        if (str_starts_with($this->avatar, '/storage/')) {
            return asset(ltrim($this->avatar, '/'));
        }
        return asset('storage/' . $this->avatar);
    }

    public function getCoverUrlAttribute(): ?string
    {
        if (!$this->cover) {
            return null;
        }
        if (str_starts_with($this->cover, 'http')) {
            return $this->cover;
        }
        if (str_starts_with($this->cover, '/storage/')) {
            return asset(ltrim($this->cover, '/'));
        }
        return asset('storage/' . $this->cover);
    }
}