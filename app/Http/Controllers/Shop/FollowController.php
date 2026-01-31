<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    /**
     * Follow a user.
     */
    public function store(User $user)
    {
        $currentUser = Auth::user();

        if ($currentUser->id === $user->id) {
            return back()->with('error', 'You cannot follow yourself.');
        }

        if (!$currentUser->isFollowing($user)) {
            $currentUser->followings()->attach($user->id);
        }

        return back()->with('success', 'You are now following ' . $user->name);
    }

    /**
     * Unfollow a user.
     */
    public function destroy(User $user)
    {
        $currentUser = Auth::user();

        if ($currentUser->isFollowing($user)) {
            $currentUser->followings()->detach($user->id);
        }

        return back()->with('success', 'You have unfollowed ' . $user->name);
    }
}
