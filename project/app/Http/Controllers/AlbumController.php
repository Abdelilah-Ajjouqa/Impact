<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Storage;

class AlbumController extends Controller
{
    public function index()
    {
        $albums = Album::where('user_id', Auth::id())
            ->withCount('posts')
            ->latest()
            ->get();

        return view('albums.index', compact('albums'));
    }

    public function create()
    {
        return view('albums.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_private' => 'boolean',
                'cover_image' => 'nullable|image|max:2048',
            ]);

            $album = new Album([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'is_private' => $validated['is_private'] ?? false,
                'user_id' => Auth::id(),
            ]);

            if ($request->hasFile('cover_image')) {
                $path = $request->file('cover_image')->store('album_covers', 'public');
                $album->cover_image = $path;
            }

            $album->save();

            return redirect()
                ->route('albums.show', $album->id)
                ->with('success', 'Album created successfully!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create album: ' . $e->getMessage());
        }
    }

    public function show(string $id)
    {
        $album = Album::with(['posts.mediaContent', 'posts.user'])
            ->findOrFail($id);

        // Check if user can view this album
        if ($album->is_private && $album->user_id !== Auth::id()) {
            return redirect()
                ->route('albums.index')
                ->with('error', 'You do not have permission to view this album.');
        }

        return view('albums.show', compact('album'));
    }

    public function edit(string $id)
    {
        $album = Album::findOrFail($id);

        // Check if user can edit this album
        if ($album->user_id !== Auth::id()) {
            return redirect()
                ->route('albums.index')
                ->with('error', 'You do not have permission to edit this album.');
        }

        return view('albums.edit', compact('album'));
    }

    public function update(Request $request, string $id)
    {
        try {
            $album = Album::findOrFail($id);

            // Check if user can update this album
            if ($album->user_id !== Auth::id()) {
                return redirect()
                    ->route('albums.index')
                    ->with('error', 'You do not have permission to update this album.');
            }

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_private' => 'boolean',
                'cover_image' => 'nullable|image|max:2048',
            ]);

            $album->title = $validated['title'];
            $album->description = $validated['description'] ?? null;
            $album->is_private = $validated['is_private'] ?? false;

            if ($request->hasFile('cover_image')) {
                // Delete old cover image if exists
                if ($album->cover_image) {
                    Storage::disk('public')->delete($album->cover_image);
                }

                $path = $request->file('cover_image')->store('album_covers', 'public');
                $album->cover_image = $path;
            }

            $album->save();

            return redirect()
                ->route('albums.show', $album->id)
                ->with('success', 'Album updated successfully!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update album: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $album = Album::findOrFail($id);

            // Check if user can delete this album
            if ($album->user_id !== Auth::id()) {
                return redirect()
                    ->route('albums.index')
                    ->with('error', 'You do not have permission to delete this album.');
            }

            // Delete cover image if exists
            if ($album->cover_image) {
                Storage::disk('public')->delete($album->cover_image);
            }

            $album->delete();

            return redirect()
                ->route('albums.index')
                ->with('success', 'Album deleted successfully!');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete album: ' . $e->getMessage());
        }
    }

    // Add a post to an album
    public function addPost(Request $request, $id)
    {
        try {
            $album = Album::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $request->validate([
                'post_id' => 'required|exists:posts,id'
            ]);

            $postId = $request->input('post_id');

            // Check if post already exists in album
            if ($album->posts()->where('post_id', $postId)->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Post already exists in this album'
                ]);
            }

            // Add post to album (removed user ownership check for posts)
            $album->posts()->attach($postId);

            return response()->json([
                'status' => 'success',
                'message' => 'Post added to album successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Album not found or you do not have permission to modify it: ' . $e->getMessage()
            ], 404);
        }
    }

    public function removePost(Request $request, string $albumId, string $postId)
    {
        try {
            $album = Album::findOrFail($albumId);

            // Check if user owns this album
            if ($album->user_id !== Auth::id()) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'You do not have permission to modify this album.'
                    ], 403);
                }

                return redirect()
                    ->back()
                    ->with('error', 'You do not have permission to modify this album.');
            }

            // Remove post from album
            $album->posts()->detach($postId);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Post removed from album successfully.'
                ]);
            }

            return redirect()
                ->back()
                ->with('success', 'Post removed from album successfully!');
        } catch (Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to remove post from album: ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Failed to remove post from album: ' . $e->getMessage());
        }
    }
}
