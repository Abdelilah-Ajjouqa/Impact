<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LikeController extends Controller
{
    /**
     * Toggle like/unlike for a post.
     */
    public function toggle(Request $request, int|string $id)
    {
        try {
            $post = Post::findOrFail($id);
            $userId = Auth::id();

            $existingLike = Like::where('user_id', $userId)
                ->where('post_id', $post->id)
                ->first();

            if ($existingLike) {
                $existingLike->delete();
                $liked = false;
                $message = 'Post unliked.';
            } else {
                Like::create([
                    'user_id' => $userId,
                    'post_id' => $post->id,
                ]);
                $liked = true;
                $message = 'Post liked.';
            }

            $likesCount = $post->likes()->count();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'liked' => $liked,
                    'likes_count' => $likesCount,
                    'message' => $message,
                ]);
            }

            return back()->with('success', $message);
        } catch (Exception $e) {
            Log::error('Like toggle error: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Failed to process like request.'], 500);
            }

            return back()->withErrors(['error' => 'Failed to process like request.']);
        }
    }
}

