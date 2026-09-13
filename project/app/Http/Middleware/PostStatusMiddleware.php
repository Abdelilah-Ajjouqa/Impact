<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Post;
use Illuminate\Http\Request;

class PostStatusMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $postId = $request->route('id') ?? $request->route('post_id');
        if (!$postId) {
            return $next($request);
        }

        $post = Post::findOrFail($postId);
        $user = $request->user();

        switch ($post->status) {
            case Post::STATUS_ARCHIVED:
                if ($user && $user->role === 'admin') {
                    return $next($request);
                }
                return redirect()
                    ->route('posts.index')
                    ->with('error', 'This post has been archived and is only accessible by administrators.');

            case Post::STATUS_PRIVATE:
                if ($user && ($user->id === $post->user_id || $user->role === 'admin')) {
                    return $next($request);
                }
                return redirect()
                    ->route('posts.index')
                    ->with('error', 'You do not have access to this post.');

            case Post::STATUS_PUBLIC:
            default:
                return $next($request);
        }
    }
}
