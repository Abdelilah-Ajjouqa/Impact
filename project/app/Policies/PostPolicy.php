<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Determine whether the user can view the post.
     */
    public function view(?User $user, Post $post): bool
    {
        if ($post->status === Post::STATUS_ARCHIVED) {
            return $user && $user->role === 'admin';
        }

        if ($post->status === Post::STATUS_PRIVATE) {
            return $user && ($user->id === $post->user_id || $user->role === 'admin');
        }

        return true;
    }

    /**
     * Determine whether the user can update the post.
     */
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id || $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the post.
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id || $user->role === 'admin';
    }
}

