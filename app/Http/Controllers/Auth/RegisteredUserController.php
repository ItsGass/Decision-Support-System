<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        
        // 🔥 VALIDASI INPUT
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'otp' => ['required']
        ]);

        // 🔥 VALIDASI OTP
        if ($request->otp != session('otp') || $request->email != session('otp_email')) {
    throw ValidationException::withMessages([
        'otp' => 'Kode verifikasi salah'
    ]);
}

        // 🔥 BUAT USER
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));


        // 🔥 OPTIONAL: hapus OTP biar ga dipakai ulang
        session()->forget(['otp', 'otp_email']);

        return redirect()->route('login')->with('success', 'Akun berhasil dibuat, silakan login');
    }
}