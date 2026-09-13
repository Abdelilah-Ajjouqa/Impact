<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['mediaContent', 'tags', 'user'])
            ->where('status', Post::STATUS_PUBLIC)
            ->latest()
            ->paginate(15);

        $savedPostIds = Auth::check()
            ? Auth::user()->savedPosts()->pluck('post_id')->flip()
            : collect();

        return view('posts.index', compact('posts', 'savedPostIds'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(PostRequest $request)
    {
        try {
            $validate = $request->validated();

            $post = Post::create([
                'title' => $validate['title'],
                'description' => $validate['description'] ?? null,
                'user_id' => $request->user()->id,
                'status' => $validate['status'] ?? Post::STATUS_PUBLIC,
            ]);

            // Sync Tags
            if (isset($validate['tags'])) {
                $this->syncTags($post, $validate['tags']);
            }

            // Handle Media
            if ($request->hasFile('media')) {
                $files = is_array($request->file('media')) ? $request->file('media') : [$request->file('media')];
                foreach ($files as $file) {
                    $filePath = $file->store('media', 'public');
                    $post->mediaContent()->create([
                        'user_id' => $post->user_id,
                        'path' => $filePath,
                        'type' => $file->getClientMimeType(),
                    ]);
                }
            }

            Activity::create([
                'user_id' => $request->user()->id,
                'action' => 'created',
                'item_type' => 'post',
                'item_id' => $post->id,
                'description' => 'New post: "' . $post->title . '"',
            ]);

            return redirect()
                ->route('posts.index')
                ->with('success', 'Post created successfully!');
        } catch (Exception $e) {
            Log::error('Post create error: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create post. Please try again.']);
        }
    }

    public function show(string $id)
    {
        $post = Post::with([
            'mediaContent',
            'tags',
            'user' => function ($query) {
                $query->withCount('followers');
            },
            'comments.user'
        ])->findOrFail($id);
        Gate::authorize('view', $post);

        $relatedPosts = Post::whereHas('tags', function ($query) use ($post) {
            $query->whereIn('tags.id', $post->tags->pluck('id'));
        })
            ->where('id', '!=', $post->id)
            ->where('status', Post::STATUS_PUBLIC)
            ->with(['mediaContent', 'user', 'tags'])
            ->inRandomOrder()
            ->take(6)
            ->get();

        return view('posts.show', compact('post', 'relatedPosts'));
    }

    public function edit(string $id)
    {
        $post = Post::with(['mediaContent', 'tags'])->findOrFail($id);
        Gate::authorize('update', $post);

        return view('posts.edit', compact('post'));
    }

    public function update(PostRequest $request, string $id)
    {
        try {
            $post = Post::findOrFail($id);
            Gate::authorize('update', $post);

            $validate = $request->validated();

            if ($request->hasFile('media')) {
                // delete old media
                foreach ($post->mediaContent as $media) {
                    Storage::disk('public')->delete($media->path);
                    $media->delete();
                }

                // store the new media
                $files = is_array($request->file('media')) ? $request->file('media') : [$request->file('media')];
                foreach ($files as $file) {
                    $filePath = $file->store('media', 'public');
                    $post->mediaContent()->create([
                        'user_id' => $post->user_id,
                        'path' => $filePath,
                        'type' => $file->getClientMimeType(),
                    ]);
                }
            }

            $post->update($validate);

            // Sync Tags
            if (isset($validate['tags'])) {
                $this->syncTags($post, $validate['tags']);
            }

            return redirect()
                ->route('posts.show', $post->id)
                ->with('success', 'Post updated successfully.');
        } catch (Exception $e) {
            Log::error('Post update error: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Update failed. Please try again.']);
        }
    }


    public function destroy(string $id)
    {
        try {
            $post = Post::findOrFail($id);
            Gate::authorize('delete', $post);

            foreach ($post->mediaContent as $media) {
                Storage::disk('public')->delete($media->path);
                $media->delete();
            }
            $post->delete();

            // Log activity
            Activity::create([
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'item_type' => 'post',
                'item_id' => $post->id,
                'description' => 'Post: "' . $post->title . '"',
            ]);

            return redirect()
                ->route('posts.index')
                ->with('success', 'Post deleted successfully.');
        } catch (Exception $e) {
            Log::error('Post delete error: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to delete post.']);
        }
    }


    public function getMedia(string $id)
    {
        $post = Post::findOrFail($id);
        $media = $post->mediaContent;

        return redirect()->route('posts.show', $post->id)
            ->with('success', 'Media loaded successfully!');
    }

    public function uploadMedia(Request $request, $id)
    {
        try {
            $request->validate([
                'media' => 'required|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,mkv|max:10240',
            ]);

            $post = Post::findOrFail($id);
            Gate::authorize('update', $post);

            if ($request->hasFile('media')) {
                $filePath = $request->file('media')->store('media', 'public');

                $post->mediaContent()->create([
                    'user_id' => $post->user_id,
                    'path' => $filePath,
                    'type' => $request->file('media')->getClientMimeType(),
                ]);

                return redirect()
                    ->route('posts.show', $post->id);
            }
        } catch (Exception $e) {
            Log::error('Upload media error: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to upload media. Please try again.']);
        }
    }


    private function syncTags(Post $post, string $tagsString)
    {
        $tagNames = array_map('trim', explode(' ', $tagsString));

        $tagIds = [];
        foreach ($tagNames as $tagName) {
            if (!empty($tagName)) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $tagIds[] = $tag->id;
            }
        }
        $post->tags()->sync($tagIds);
    }
}
