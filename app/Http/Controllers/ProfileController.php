<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    } 

    public function profile()
    {
        $profile = Auth()->user();
        // return $profile;
        return view('admin.user.profile')->with('profile', $profile);
    }

    public function profileUpdate(Request $request, $id)
    {

        $user = User::findOrFail($id);
        if($request->hasFile('profile_image')){

            $file = $request -> file('profile_image');
            $path = public_path('/images/users').$user->profile_image;
            if(file::exists($path))
            {
                file::delete($path);
            }
            $ext = $file -> getClientOriginalExtension();
            $filename = time().'.'.$ext;
            $file -> move(public_path('/images/users'),$filename);
            $user -> profile_image = $filename;
        }

        $user -> name = $request->name;

        $status = $user->save();
        if ($status) {
            return redirect()->route('dashboard')->with('success', 'Amenity created successfully.');
        } else {
            return redirect()->back()->with('error', 'Please try again!');
        }
        
    }


    public function changePassword()
    {
        return view('admin.user.changePassword');
    }
    public function changPasswordStore(Request $request)
    {

        $request->validate([
            'current_password' => ['required', new MatchOldPassword], 
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);

        User::find(auth()->user()->id)->update(['password' => Hash::make($request->new_password)]);

        return redirect()->route('dashboard')->with('success', 'Password successfully changed');
    }
}

