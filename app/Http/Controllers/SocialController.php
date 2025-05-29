<?php

namespace App\Http\Controllers;

use App\Models\HostelOwner;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class SocialController extends Controller
{

    //Google Login
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    //Google callback  
    public function redirectToGoogleCallback()
    {
        $GoogleUser = Socialite::driver('google')->user();

        // Check if user already exists by email
        $user = User::where('email', $GoogleUser->email)->first();
        
        if ($user) {
            // If user exists but doesn't have a google_id, update it
            if (!$user->google_id) {
                $user->update([
                    'google_id' => $GoogleUser->id
                ]);
            }
        } else {
            // Create a new user if not found
            $user = User::create([
                'name' => $GoogleUser->name,
                'email' => $GoogleUser->email,
                'google_id' => $GoogleUser->id,
                'password' => Hash::make(12345), // You may change this logic
            ]);
        }

        // Log in the user
        Auth::login($user);

        return redirect('/dashboard');
    }

    public function redirectToTwitter()
    {
        return Socialite::driver('twitter')->redirect();
    }

    public function redirectToTwitterCallback()
    {
        // Use `stateless()` to avoid state issues
        $TwitterUser = Socialite::driver('twitter')->user();

        // Check if user already exists by email
        $user = User::where('email', $TwitterUser->email)->first();

        if ($user) {
            // If user exists but doesn't have a twitter_id, update it
            if (!$user->twitter_id) {
                $user->update([
                    'twitter_id' => $TwitterUser->id
                ]);
            }
        } else {
            // Create a new user if not found
            $user = User::create([
                'name' => $TwitterUser->name,
                'email' => $TwitterUser->email,
                'twitter_id' => $TwitterUser->id,
                'password' => Hash::make(12345), // Change this logic if needed
            ]);
        }

        Auth::login($user);

        return redirect('/dashboard');
    }


    // //Github Login
    // public function redirectToGithub()
    // {
    //     return Socialite::driver('github')->stateless()->redirect();
    // }

    // //github callback  
    // public function handleGithubCallback()
    // {

    //     $user = Socialite::driver('github')->stateless()->user();

    //     $this->_registerorLoginUser($user);
    //     return redirect()->route('home');
    // }

}
