<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function loginUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);


        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            session(['user' => Auth::user()->email]);

            $user = Auth::user();
            if ($user->user_type === 'super_admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Welcome, Super Admin!');
            } 
        

            // Default redirection for other users
            return redirect()->route('dashboard')->with('success', 'Successfully logged in');
        } else {
            return back()->withInput()->withErrors(['login_error' => 'Invalid email or password.']);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        Session::flush(); // Optional: clear all session data
        return redirect()->route('home')->with('success', 'Successfully logged out.');
    }

 
}
