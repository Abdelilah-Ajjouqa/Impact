<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\SavedPost;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class SavedPostController extends Controller
{
    public function save($postId)
    {
        try {
            $post = Post::findOrFail($postId);
            Gate::authorize('view', $post);

            $existingSave = SavedPost::where('user_id', Auth::id())
                ->where('post_id', $postId)
                ->first();
            if ($existingSave) {
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Post already saved',
                        'saved' => true
                    ]);
                }
                return redirect()->back()->with('info', 'Post already saved.');
            }
            SavedPost::create([
                'user_id' => Auth::id(),
                'post_id' => $postId
            ]);
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Post saved successfully',
                    'saved' => true
                ]);
            }
            return redirect()->back()->with('success', 'Post saved successfully.');
        } catch (Exception $e) {
            Log::error('SavedPost save error: ' . $e->getMessage());
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unable to save post.'
                ], 500);
            }
            return redirect()->back()->with('error', 'Unable to save post.');
        }
    }

    public function unsave($postId)
    {
        try {
            $savedPost = SavedPost::where('user_id', Auth::id())
                ->where('post_id', $postId)
                ->first();
            if (!$savedPost) {
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Post not found in saved items',
                        'saved' => false
                    ], 404);
                }
                return redirect()->back()->with('error', 'Post not found in saved items.');
            }
            $savedPost->delete();
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Post unsaved successfully',
                    'saved' => false
                ]);
            }
            return redirect()->back()->with('success', 'Post unsaved successfully.');
        } catch (Exception $e) {
            Log::error('SavedPost unsave error: ' . $e->getMessage());
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unable to unsave post.'
                ], 500);
            }
            return redirect()->back()->with('error', 'Unable to unsave post.');
        }
    }

    public function getSavedPosts($userId)
    {
        try {
            if (Auth::id() != $userId && (!Auth::check() || Auth::user()->role !== 'admin')) {
                abort(403, 'Unauthorized access to saved posts.');
            }

            $user = User::findOrFail($userId);
            $savedPosts = SavedPost::with(['post.mediaContent', 'user'])
                ->where('user_id', $userId)
                ->latest()
                ->get();
            return view('saved-posts.index', compact('savedPosts', 'user'));
        } catch (Exception $e) {
            Log::error('SavedPost getSavedPosts error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error retrieving saved posts.');
        }
    }
}
