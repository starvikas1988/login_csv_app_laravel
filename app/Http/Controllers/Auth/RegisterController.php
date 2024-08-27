<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Hobby;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function create(){
        return view('auth.register');
    }

    public function store(Request $request){
        $request->validate([
            'username'=>'required | string|max:255',
            'mobile' => 'required|string|max:20',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'profile_image' => 'nullable|image',
            'hobbies' => 'nullable|string',
            'role' => 'required|in:user,admin',
        ]);

        $user = User::create([
            'username' => $request->username,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        if ($request->hasFile('profile_image')) {
             // Get the file from the request
            $file = $request->file('profile_image');

            // Generate a unique name for the file, keeping the original extension
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

              // Store the file in the 'profile_images' directory in the 'public' disk
            $filePath = $file->storeAs('profile_images', $filename, 'public');

            //$user->profile_image = $request->file('profile_image')->store('profile_images', 'public');

            // Save the file path in the user's profile_image field
            $user->profile_image = $filePath;
            $user->save();
        }

        if ($request->hobbies) {
            $hobbies = explode(',', $request->hobbies);
            foreach ($hobbies as $hobby) {
                Hobby::create([
                    'user_id' => $user->id,
                    'hobby' => trim($hobby),
                ]);
            }
        }

        Auth::login($user);

        return redirect()->route('admin.dashboard');
    }
}
