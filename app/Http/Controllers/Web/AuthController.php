<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $lockName = 'failed-login-attempts-' . $request->email;

        if (RateLimiter::tooManyAttempts($lockName, 5)) {
            $availableAt = now()->addSeconds(RateLimiter::availableIn($lockName))
                ->diffForHumans(syntax: Carbon::DIFF_ABSOLUTE, parts: 7);

            return back()->withErrors([
                'email' => 'Too many failed attempts. Try again in ' . $availableAt
            ]);
        }

        if (!Auth::attempt($request->validated())) {
            RateLimiter::hit($lockName, 900);
            return back()->withErrors([
                'email' => 'Invalid credentials'
            ]);
        }

        RateLimiter::clear($lockName);

        $user = auth()->user()->load('role');

        if (!$user->is_active) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Your account has been suspended'
            ]);
        }

        return redirect()->intended('/dashboard');
    }

    public function logout()
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/login');
    }

    public function getProfile()
    {
        $user = auth()->user()->load('role');
        
        return view('profile.show', [
            'user' => $user
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        if ($request->has('profileImage')) {
            $path = $request->file('profileImage')->store('profile-images');
            auth()->user()->update(['profile_image' => $path, 'name' => $request->name]);
        } else {
            auth()->user()->update(['name' => $request->name]);
        }

        return back()->with('success', 'Your profile has been updated');
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        auth()->user()->update(['password' => bcrypt($request->newPassword)]);
        return back()->with('success', 'Your password has been updated');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }
}