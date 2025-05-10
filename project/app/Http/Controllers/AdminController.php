<?php

namespace App\Http\Controllers;

use App\Models\Comments;
use App\Models\Posts;
use App\Models\User;
use App\Models\Activity;
use Exception;
use Illuminate\Support\Facades\Auth;

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
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function getAllActiveUsers()
    {
        try {
            $users = User::where('is_active', true)->get();

            return view('admin.users.active', compact('users'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error fetching active users: ' . $e->getMessage());
        }
    }

    public function getAllInactiveUsers()
    {
        try {
            $users = User::where('is_active', false)->get();

            return view('admin.users.inactive', compact('users'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error fetching inactive users: ' . $e->getMessage());
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
            return redirect()->back()->with('error', 'Error activating user: ' . $e->getMessage());
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
            return redirect()->back()->with('error', 'Error deactivating user: ' . $e->getMessage());
        }
    }

    public function archivePost($id)
    {
        try {
            $post = Posts::findOrFail($id);
            $post->status = Posts::is_archived;
            $post->save();

            return redirect()
                ->route('posts.index')
                ->with('success', 'Post has been archived successfully and is now only visible to administrators.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error archiving post: ' . $e->getMessage());
        }
    }

    public function getAllArchivePosts()
    {
        try {
            $posts = Posts::where('status', Posts::is_archived)->get();

            return view('admin.posts.archived', compact('posts'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error fetching archived posts: ' . $e->getMessage());
        }
    }

    public function restorePost($id)
    {
        try {
            $post = Posts::findOrFail($id);
            if ($post->status !== Posts::is_archived) {
                return redirect()->back()->with('error', 'Post is not archived.');
            }

            $post->status = Posts::is_public;
            $post->save();

            return redirect()->back()->with('success', 'Post restored successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error restoring post: ' . $e->getMessage());
        }
    }

    public function deletePost($id)
    {
        try {
            $post = Posts::findOrFail($id);
            $user = Auth::user();

            if ($user->role == 'admin') {
                if ($post->status !== Posts::is_archived) {
                    return redirect()->back()->with('error', 'Admin can only delete archived posts.');
                }
            } else if ($post->user_id !== $user->id) {
                return redirect()->back()->with('error', 'Unauthorized to delete this post.');
            }

            $post->delete();
            return redirect()->back()->with('success', 'Post deleted successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error deleting post: ' . $e->getMessage());
        }
    }

    public function deleteComment($id)
    {
        try {
            $comment = Comments::findOrFail($id);
            $user = Auth::user();

            if ($user->role !== 'admin') {
                return redirect()->back()->with('error', 'Only admin can perform this action.');
            }

            $comment->delete();
            return redirect()->back()->with('success', 'Comment deleted successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error deleting comment: ' . $e->getMessage());
        }
    }
}
