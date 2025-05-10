<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Album;
use App\Models\Posts;
use App\Models\SavedPost;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount('posts')->get();

        if ($users->isEmpty()) {
            return view('users.index')
                ->with('warning', 'No users found');
        }

        return view('users.index', compact('users'));
    }

    public function show(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $posts = Posts::where('user_id', $id)
                ->where(function ($query) {
                    $query->where('status', 'public')
                        ->orWhere(function ($q) {
                            if (Auth::check()) {
                                $q->where('user_id', Auth::id());
                            }
                        });
                })
                ->get();

            $savedPosts = collect(); // Empty collection for guests
            if (Auth::check()) {
                $savedPosts = SavedPost::with(['post.mediaContent', 'post.user'])
                    ->where('user_id', $id)
                    ->get();
            }

            return view('users.show', compact('user', 'posts', 'savedPosts'));
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'User not found: ' .  $e->getMessage());
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    public function update(UserRequest $request, string $id)
    {
        try {
            $user = User::findOrFail($id);
            $data = $request->validated();


            if ($request->hasFile('avatar')) {
                $avatarPath = '/storage/' . $request->file('avatar')->store('avatars');
                $data['avatar'] = $avatarPath;
            }

            if ($request->hasFile('cover')) {
                $coverPath = '/storage/' . $request->file('cover')->store('covers');
                $data['cover'] = $coverPath;
            }

            $user->update($data);

            return redirect()
                ->route('users.show', $user->id)
                ->with('success', 'Profile updated successfully');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return redirect()
                ->route('users.index')
                ->with('success', 'User deleted successfully');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    public function getPosts(Request $request, string $id)
    {
        try {
            if (Auth::id() != $id) {
                return redirect()->back()->with('error', 'Unauthorized');
            }

            $albumId = $request->query('album_id');

            $posts = Posts::where('user_id', $id)
                ->with('mediaContent')
                ->when($albumId, function ($query, $albumId) {
                    return $query->whereDoesntHave('albums', function ($q, $albumId) {
                        $q->where('albums.id', $albumId);
                    });
                }, $albumId)
                ->latest()
                ->get();

            if ($request->ajax()) {
                return response()->json(['posts' => $posts]);
            }

            return view('users.posts', compact('posts'));
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function getAlbums(Request $request, string $id)
    {
        try {
            if (Auth::id() != $id) {
                return redirect()->back()->with('error', 'Unauthorized');
            }

            $postId = $request->query('post_id');

            $albums = Album::where('user_id', $id)
                ->withCount('posts')
                ->latest()
                ->get();

            if ($postId) {
                $postInAlbums = DB::table('album_post')
                    ->select('album_id')
                    ->where('post_id', $postId)
                    ->get();

                $albumIds = [];
                foreach ($postInAlbums as $item) {
                    $albumIds[] = $item->album_id;
                }

                foreach ($albums as $album) {
                    $album->has_post = in_array($album->id, $albumIds);
                }
            }

            if ($request->ajax()) {
                return response()->json(['albums' => $albums]);
            }

            return view('users.albums', compact('albums', 'postId'));
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}
