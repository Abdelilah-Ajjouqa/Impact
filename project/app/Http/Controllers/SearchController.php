<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = trim($request->input('q', ''));

        if ($query === '') {
            return redirect()->back();
        }

        $posts = Post::where('status', '!=', Post::STATUS_ARCHIVED)
            ->where(function ($statusQuery) {
                $statusQuery->where('status', Post::STATUS_PUBLIC);
                if (Auth::check()) {
                    $statusQuery->orWhere(function ($privateQuery) {
                        $privateQuery->where('status', Post::STATUS_PRIVATE)
                            ->where('user_id', Auth::id());
                    });
                }
            })
            ->where(function ($searchQuery) use ($query) {
                $searchQuery->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhereHas('tags', function ($tagQuery) use ($query) {
                        $tagQuery->where('name', 'like', "%{$query}%");
                    });
            })
            ->with(['mediaContent', 'user', 'tags'])
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('search.results', compact('posts', 'query'));
    }
}
