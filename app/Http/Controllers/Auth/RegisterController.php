<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function registerForm()
    {
        return view('auth.register');
    }

    public function registerUser(Request $request)
    {

        $request->validate([
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => 1, 
            'password' => Hash::make($request->password),
            'status' => 1,
        ]);

        Auth::login($user);

        session(['user' => $user->email]);

        return redirect()->route('home')->with('success', 'Successfully registered');
    }

   
}
