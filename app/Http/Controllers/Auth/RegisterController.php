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
        'password' => [
            'required',
            'string',
            'min:8',
            'confirmed',
            'regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/'
        ],
    ], [
        'password.regex' => 'Password must be at least 8 characters and include at least one letter, one number, and one special character.',
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'role' => 0,
        'password' => Hash::make($request->password),
        'status' => 1,
    ]);

    Auth::login($user);
    session(['user' => $user->email]);

    return redirect()->route('dashboard')->with('success', 'Successfully registered');
}


   
}
