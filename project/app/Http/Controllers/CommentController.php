<?php

namespace App\Http\Controllers;

use App\Models\Comments;
use App\Models\Posts;
use App\Http\Requests\CommentRequest;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Models\Activity;

class CommentController extends Controller
{
    public function index(string $post_id)
    {
        try {
            $post = Posts::findOrFail($post_id);

            $comments = Comments::with(['user'])
                ->where('post_id', $post_id)
                ->latest()
                ->get();

            return view('comments.index', compact('comments', 'post'));
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error fetching comments: ' . $e->getMessage());
        }
    }

    public function store(CommentRequest $request, string $post_id)
    {
        try {
            Posts::findOrFail($post_id);

            $comment = Comments::create([
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
                'description' => 'On post: "' . Posts::find($post_id)?->title . '"',
            ]);

            return redirect()
                ->back()
                ->with('success', 'Comment added successfully.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to add comment: ' . $e->getMessage());
        }
    }

    public function update(CommentRequest $request, string $post_id, string $comment_id)
    {
        try {
            Posts::findOrFail($post_id);

            $comment = Comments::where('post_id', $post_id)
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
            return redirect()
                ->back()
                ->with('error', 'Failed to update comment: ' . $e->getMessage());
        }
    }

    public function destroy(string $post_id, string $comment_id)
    {
        try {
            Posts::findOrFail($post_id);

            $comment = Comments::where('post_id', $post_id)
                ->where('id', $comment_id)
                ->firstOrFail();

            if ($comment->user_id !== Auth::id()) {
                return redirect()
                    ->back()
                    ->with('error', 'Unauthorized to delete this comment.');
            }

            $comment->delete();

            return redirect()
                ->back()
                ->with('success', 'Comment deleted successfully.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete comment: ' . $e->getMessage());
        }
    }
}
