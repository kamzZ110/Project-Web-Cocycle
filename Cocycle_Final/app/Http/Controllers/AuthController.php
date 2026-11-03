<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();
            $this->discardInaccessibleIntendedUrl($request, $user->role);

            if ($user->role === 'partner') {
                return redirect()->intended(route('partner.home'));
            }

            return redirect()->intended(route('customer.home'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    private function discardInaccessibleIntendedUrl(Request $request, string $role): void
    {
        $intended = $request->session()->get('url.intended');
        if (!is_string($intended) || $intended === '') return;

        try {
            $url = str_starts_with($intended, '/') && !str_starts_with($intended, '//')
                ? $request->getSchemeAndHttpHost().$intended
                : $intended;
            $target = Request::create($url, 'GET');
            if ($target->getSchemeAndHttpHost() !== $request->getSchemeAndHttpHost()) {
                $request->session()->forget('url.intended');
                return;
            }
            $route = app('router')->getRoutes()->match($target);
            $roles = array_values(array_filter($route->gatherMiddleware(), fn ($middleware) => is_string($middleware) && str_starts_with($middleware, 'role:')));
            if ($roles && !in_array('role:'.$role, $roles, true)) {
                $request->session()->forget('url.intended');
            }
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface|\InvalidArgumentException $exception) {
            $request->session()->forget('url.intended');
        }
    }

    public function showRegisterForm(Request $request)
    {
        $role = $request->query('role') === 'partner' ? 'partner' : 'customer';
        return view('auth.register', compact('role'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|string|email|max:100|unique:users',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'role_type' => 'required|in:customer,partner',
            'nama_toko' => 'required_if:role_type,partner|nullable|string|max:100',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $role = $request->input('role_type');
        $nama_toko = ($role === 'partner') ? $request->input('nama_toko') : null;

        $profilePicPath = 'assets/default_profile.png';
        if ($request->hasFile('profile_pic')) {
            // Store in public/photos to match legacy or storage/app/public?
            // Legacy uses "photos/" relative to script.
            // We'll put it in "photos" in public disk.
            $file = $request->file('profile_pic');
            $filename = uniqid('user_', true) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('photos'), $filename);
            $profilePicPath = 'photos/' . $filename;
        }

        DB::transaction(function () use ($request, $role, $nama_toko, $profilePicPath) {
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'role' => $role,
            'nama_toko' => $nama_toko,
            'profile_pic' => $profilePicPath,
        ]);
        if ($role === 'partner') {
            app(\App\Services\PartnerStarterCatalog::class)->initialize($user);
        }
        });

        // Redirect to login with success message
        return redirect()->route('login')->with('success', "Registration successful as {$role}! Please login.");
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
