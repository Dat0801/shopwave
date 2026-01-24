<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('avatar')) {
            if ($request->user()->avatar) {
                // Only delete if it's a local file (not a URL)
                if (!filter_var($request->user()->avatar, FILTER_VALIDATE_URL)) {
                    Storage::disk('public')->delete($request->user()->avatar);
                }
            }
            $path = $request->file('avatar')->storeOnCloudinary('shopwave/avatars')->getSecurePath();
            $validated['avatar'] = $path;
        }

        // Handle avatar removal if requested (e.g. passing null or specific flag)
        // For now, let's assume if 'avatar' is passed as null explicitly, we remove it.
        // But the validation rules say 'nullable|image', so if it's null it might be ignored or passed as null.
        // However, HTML file input doesn't send null if empty.
        // We can handle a separate field 'remove_avatar' if needed, but let's stick to simple upload for now.

        $request->user()->fill($validated);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
