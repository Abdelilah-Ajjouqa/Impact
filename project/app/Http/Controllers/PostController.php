<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Posts;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $post = Posts::with('mediaContent', 'tags')
            ->where('status', '!=', Posts::is_archived)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('posts.index', compact('post'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(PostRequest $request)
    {
        try {
            $validate = $request->validated();

            $post = Posts::create([
                'title' => $validate['title'],
                'description' => $validate['description'] ?? null,
                'user_id' => $request->user()->id,
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
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(string $id)
    {
        $post = Posts::with('mediaContent', 'tags')->findOrFail($id);

        $relatedPosts = Posts::whereHas('tags', function ($query) use ($post) {
            $query->whereIn('tags.id', $post->tags->pluck('id'));
        })
            ->where('id', '!=', $post->id)
            ->where('status', '!=', Posts::is_archived)
            ->with(['mediaContent', 'user', 'tags'])
            ->inRandomOrder()
            // ->limit(6)
            ->get();

        return view('posts.show', compact('post', 'relatedPosts'));
    }

    public function edit(string $id)
    {
        $post = Posts::with('mediaContent', 'tags')->findOrFail($id);
        return view('posts.edit', compact('post'));
    }

    public function update(PostRequest $request, string $id)
    {
        try {
            $validate = $request->validated();
            $post = Posts::findOrFail($id);

            if ($request->hasFile('media')) {
                // delete old media
                foreach ($post->mediaContent as $media) {
                    Storage::delete($media->path);
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
            return redirect()
                ->back()
                ->withErrors(['error' => 'Update failed: ' . $e->getMessage()]);
        }
    }


    public function destroy(string $id)
    {
        $post = Posts::findOrFail($id);

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
    }


    public function getMedia(string $id)
    {
        $post = Posts::findOrFail($id);
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

            $post = Posts::findOrFail($id);

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
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }


    private function syncTags(Posts $post, string $tagsString)
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
