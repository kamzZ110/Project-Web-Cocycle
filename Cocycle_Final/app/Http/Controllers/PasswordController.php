<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Password, Hash};
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class PasswordController extends Controller
{
    public function request()
    {
        return view('auth.forgot-password');
    }

    public function email(Request $request)
    {
        $request->validate(['email' => 'required|email|max:100']);
        Password::sendResetLink($request->only('email'));
        return back()->with('status', 'Jika email terdaftar, tautan reset password telah dikirim.');
    }

    public function form(Request $request, string $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function reset(Request $request)
    {
        $request->validate(['token' => 'required', 'email' => 'required|email', 'password' => 'required|min:6|confirmed']);
        $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function ($user, $password) {
            $user->forceFill(['password' => Hash::make($password), 'remember_token' => Str::random(60)])->save();
            event(new PasswordReset($user));
        });
        return $status === Password::PasswordReset
            ? redirect()->route('login')->with('success', 'Password berhasil diubah. Silakan login.')
            : back()->withErrors(['email' => __($status)]);
    }
}
