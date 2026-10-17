<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Socialite;

class SocialiteController extends Controller
{
    public function login($provider)
    {
        if (!in_array($provider, ['github', 'google'])) {
            abort(404);
        }
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        if (!in_array($provider, ['github', 'google'])) {
            abort(404);
        }

        $socialiteUser = Socialite::driver($provider)->user();

        $user = User::updateOrCreate([
            'provider_id' => $socialiteUser->getId(),
        ], [
            'name' => $socialiteUser->getName(),
            'email' => $socialiteUser->getEmail(),
            'provider_name' => $provider,
        ]);

        // auth for user
        Auth::login($user, true);

        // redirect to dashboard
        return to_route('dashboard');

    }
}
