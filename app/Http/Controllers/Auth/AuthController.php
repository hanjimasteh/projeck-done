<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [], [
            'login' => 'email atau username',
        ]);

        $throttleKey = Str::transliterate(Str::lower($credentials['login']).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'login' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $user = User::where($field, $credentials['login'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'login' => 'Email/username atau password yang Anda masukkan salah.',
            ]);
        }

        if ($user->status !== 'aktif') {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'login' => 'Akun Anda tidak aktif. Silakan hubungi panitia PPDB.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->route(
            $user->isAdmin() ? 'admin.dashboard' : 'calon.dashboard'
        );
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
        ], [], [
            'name' => 'nama lengkap',
            'username' => 'username',
            'email' => 'email',
            'password' => 'password',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => Role::PENDAFTAR,
                'status' => 'aktif',
            ]);

            $user->calonSiswa()->create([]);
        });

        return redirect()->route('login')->with(
            'success',
            'Akun berhasil didaftarkan. Silakan masuk menggunakan akun yang telah dibuat.'
        );
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda telah keluar dari akun.');
    }
}
