<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Http\Requests\CommentRequest;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Activity;

class CommentController extends Controller
{
    public function index(string $post_id)
    {
        try {
            $post = Post::findOrFail($post_id);

            $comments = Comment::with(['user'])
                ->where('post_id', $post_id)
                ->latest()
                ->get();

            return view('comments.index', compact('comments', 'post'));
        } catch (Exception $e) {
            Log::error('Comment index error: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Unable to fetch comments.');
        }
    }

    public function store(CommentRequest $request, string $post_id)
    {
        try {
            $post = Post::findOrFail($post_id);

            $comment = Comment::create([
                'content' => $request->validated()['content'],
                'user_id' => Auth::id(),
                'post_id' => $post_id
            ]);

            // Log activity
            Activity::create([
                'user_id' => Auth::id(),
                'action' => 'commented',
                'item_type' => 'comment',
                'item_id' => $comment->id,
                'description' => 'On post: "' . $post->title . '"',
            ]);

            return redirect()
                ->back()
                ->with('success', 'Comment added successfully.');
        } catch (Exception $e) {
            Log::error('Comment store error: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Failed to add comment. Please try again.');
        }
    }

    public function update(CommentRequest $request, string $post_id, string $comment_id)
    {
        try {
            Post::findOrFail($post_id);

            $comment = Comment::where('post_id', $post_id)
                ->where('id', $comment_id)
                ->firstOrFail();

            if ($comment->user_id !== Auth::id()) {
                return redirect()
                    ->back()
                    ->with('error', 'Unauthorized to update this comment.');
            }

            $comment->update($request->validated());

            return redirect()
                ->back()
                ->with('success', 'Comment updated successfully.');
        } catch (Exception $e) {
            Log::error('Comment update error: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Failed to update comment. Please try again.');
        }
    }

    public function destroy(string $post_id, string $comment_id)
    {
        try {
            Post::findOrFail($post_id);

            $comment = Comment::where('post_id', $post_id)
                ->where('id', $comment_id)
                ->firstOrFail();

            if ($comment->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
                return redirect()
                    ->back()
                    ->with('error', 'Unauthorized to delete this comment.');
            }

            $comment->delete();

            return redirect()
                ->back()
                ->with('success', 'Comment deleted successfully.');
        } catch (Exception $e) {
            Log::error('Comment destroy error: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Failed to delete comment.');
        }
    }
}
