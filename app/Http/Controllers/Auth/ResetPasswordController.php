<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;

class ResetPasswordController extends Controller
{
    //  public function showResetForm(Request $request, $token = null)
    // {


    //     dd($token);
    //     $email = $request->query('email');
    //     return view('auth.passwords.reset')->with([
    //         'token' => $token,
    //         'email' => $email,
    //     ]);
    // }
    
    public function showResetForm(Request $request, $token)
    {

        return view('auth.passwords.reset', ['token' => $token, 'email' => $request->email]);
    }


    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:6',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (! $record || Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            return back()->withErrors(['email' => 'This password reset token is invalid or expired.']);
        }

        // Update password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete reset record
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Your password has been reset!');
    }
}
