<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    public function getData()
    {
        $user = Auth::user();
        return response()->json([
            'status' => 'success',
            'username' => $user->username,
            'email' => $user->email,
            'phone' => $user->phone,
            'fullname' => $user->fullname,
            'address' => $user->address,
            'profile_pic' => $user->profile_pic
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:50', \Illuminate\Validation\Rule::unique('users')->ignore(Auth::id())],
            'phone' => 'required|string|max:20',
            'fullname' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $user->username = $request->username;
        $user->phone = $request->phone;
        $user->fullname = $request->fullname;
        $user->address = $request->address;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'username' => $user->username
        ]);
    }

    public function updatePicture(Request $request)
    {
        $request->validate([
            'profile_pic' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $user = Auth::user();

        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Simpan di public/assets/profile_pics agar sesuai dengan path legacy
            $file->move(public_path('assets/profile_pics'), $filename);
            
            $user->profile_pic = 'assets/profile_pics/' . $filename;
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Profile picture updated successfully',
                'profile_pic' => asset($user->profile_pic)
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'No file uploaded'
        ], 400);
    }
}
