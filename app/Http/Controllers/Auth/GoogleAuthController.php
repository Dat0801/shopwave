<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->user();
        $googleId = $googleUser->getId();
        $email = $googleUser->getEmail();

        if (! $googleId || ! $email) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Google account did not return an email address.']);
        }

        $user = User::query()->where('google_id', $googleId)->first();

        if (! $user) {
            $userByEmail = User::query()->where('email', $email)->first();

            if ($userByEmail) {
                if ($userByEmail->google_id && $userByEmail->google_id !== $googleId) {
                    return redirect()
                        ->route('login')
                        ->withErrors(['email' => 'This email is already linked to another Google account.']);
                }

                $userByEmail->forceFill([
                    'google_id' => $googleId,
                    'avatar' => $userByEmail->avatar ?: $googleUser->getAvatar(),
                    'email_verified_at' => $userByEmail->email_verified_at ?? now(),
                ])->save();

                $user = $userByEmail;
            } else {
                $user = User::query()->create([
                    'name' => $googleUser->getName() ?: Str::before($email, '@'),
                    'email' => $email,
                    'google_id' => $googleId,
                    'password' => Str::random(32),
                    'avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                ]);
            }
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route('shop.index', absolute: false));
    }
}
