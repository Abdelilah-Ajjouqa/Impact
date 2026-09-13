<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Models\Activity;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function dashboard()
    {
        try {
            $user = Auth::user();
            if (!$user || $user->role !== 'admin') {
                return redirect()->back()->with('error', 'You cannot access this page.');
            }

            $activities = Activity::with('user')->latest()->take(10)->get();
            return view('admin.dashboard', compact('user', 'activities'));
        } catch (Exception $e) {
            Log::error('Admin dashboard error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load dashboard.');
        }
    }

    public function getAllActiveUsers()
    {
        try {
            $users = User::where('is_active', true)->get();

            return view('admin.users.active', compact('users'));
        } catch (Exception $e) {
            Log::error('Admin getAllActiveUsers error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error fetching active users.');
        }
    }

    public function getAllInactiveUsers()
    {
        try {
            $users = User::where('is_active', false)->get();

            return view('admin.users.inactive', compact('users'));
        } catch (Exception $e) {
            Log::error('Admin getAllInactiveUsers error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error fetching inactive users.');
        }
    }

    public function activateUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->is_active = true;
            $user->save();

            return redirect()->back()->with('success', 'User activated successfully.');
        } catch (Exception $e) {
            Log::error('Admin activateUser error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error activating user.');
        }
    }

    public function deactivateUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->is_active = false;
            $user->save();

            return redirect()->back()->with('success', 'User deactivated successfully.');
        } catch (Exception $e) {
            Log::error('Admin deactivateUser error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error deactivating user.');
        }
    }

    public function archivePost($id)
    {
        try {
            $post = Post::findOrFail($id);
            $post->status = Post::STATUS_ARCHIVED;
            $post->save();

            return redirect()
                ->route('posts.index')
                ->with('success', 'Post has been archived successfully and is now only visible to administrators.');
        } catch (Exception $e) {
            Log::error('Admin archivePost error: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Error archiving post.');
        }
    }

    public function getAllArchivePosts()
    {
        try {
            $posts = Post::where('status', Post::STATUS_ARCHIVED)->get();

            return view('admin.posts.archived', compact('posts'));
        } catch (Exception $e) {
            Log::error('Admin getAllArchivePosts error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error fetching archived posts.');
        }
    }

    public function restorePost($id)
    {
        try {
            $post = Post::findOrFail($id);
            if ($post->status !== Post::STATUS_ARCHIVED) {
                return redirect()->back()->with('error', 'Post is not archived.');
            }

            $post->status = Post::STATUS_PUBLIC;
            $post->save();

            return redirect()->back()->with('success', 'Post restored successfully.');
        } catch (Exception $e) {
            Log::error('Admin restorePost error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error restoring post.');
        }
    }

    public function deletePost($id)
    {
        try {
            $post = Post::findOrFail($id);
            $user = Auth::user();

            if ($user->role == 'admin') {
                if ($post->status !== Post::STATUS_ARCHIVED) {
                    return redirect()->back()->with('error', 'Admin can only delete archived posts.');
                }
            } else if ($post->user_id !== $user->id) {
                return redirect()->back()->with('error', 'Unauthorized to delete this post.');
            }

            $post->delete();
            return redirect()->back()->with('success', 'Post deleted successfully.');
        } catch (Exception $e) {
            Log::error('Admin deletePost error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error deleting post.');
        }
    }

    public function deleteComment($id)
    {
        try {
            $comment = Comment::findOrFail($id);
            $user = Auth::user();

            if ($user->role !== 'admin') {
                return redirect()->back()->with('error', 'Only admin can perform this action.');
            }

            $comment->delete();
            return redirect()->back()->with('success', 'Comment deleted successfully.');
        } catch (Exception $e) {
            Log::error('Admin deleteComment error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error deleting comment.');
        }
    }
}
