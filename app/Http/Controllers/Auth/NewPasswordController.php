<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
{
    $request->validate([
        'email' => ['required', 'email'],
        'owner_pin' => ['required'],
        'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
    ]);

    // 1. Cari user berdasarkan email
    $user = \App\Models\User::where('email', $request->email)->first();

    // 2. Validasi: Apakah user ada dan PIN cocok?
    // Pastikan OWNER_RESET_PIN di .env sama persis dengan input
    if (!$user || $request->owner_pin !== env('OWNER_RESET_PIN')) {
        return back()
            ->withInput($request->only('email'))
            ->withErrors(['owner_pin' => 'Email atau PIN Owner salah!']);
    }

    // 3. Update Password secara manual
    $user->forceFill([
        'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        'remember_token' => \Illuminate\Support\Str::random(60),
    ])->save();

    event(new \Illuminate\Auth\Events\PasswordReset($user));

    return redirect()->route('login')->with('status', 'Password berhasil diperbarui!');
}
}
