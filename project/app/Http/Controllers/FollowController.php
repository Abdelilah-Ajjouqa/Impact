<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FollowController extends Controller
{
    /**
     * Toggle follow/unfollow for a user.
     */
    public function toggle(Request $request, int|string $id)
    {
        try {
            $userToFollow = User::findOrFail($id);
            $authUser = Auth::user();

            if ($authUser->id === $userToFollow->id) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['error' => 'You cannot follow yourself.'], 422);
                }
                return back()->withErrors(['error' => 'You cannot follow yourself.']);
            }

            if ($authUser->isFollowing($userToFollow)) {
                $authUser->following()->detach($userToFollow->id);
                $isFollowing = false;
                $message = 'Unfollowed successfully.';
            } else {
                $authUser->following()->attach($userToFollow->id);
                $isFollowing = true;
                $message = 'Followed successfully.';
            }

            $followersCount = $userToFollow->followers()->count();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'following' => $isFollowing,
                    'followers_count' => $followersCount,
                    'message' => $message,
                ]);
            }

            return back()->with('success', $message);
        } catch (Exception $e) {
            Log::error('Follow toggle error: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Failed to process follow request.'], 500);
            }

            return back()->withErrors(['error' => 'Failed to process follow request.']);
        }
    }
}

