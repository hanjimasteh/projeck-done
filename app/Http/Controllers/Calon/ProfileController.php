<?php

namespace App\Http\Controllers\Calon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('calon.profil', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ], [], [
            'name' => 'nama lengkap',
            'email' => 'email',
            'current_password' => 'password saat ini',
            'password' => 'password baru',
        ]);

        DB::transaction(function () use ($user, $validated) {
            $user->name = $validated['name'];
            $user->email = $validated['email'];

            if (! empty($validated['password'])) {
                $user->password = $validated['password'];
            }

            $user->save();

            $calonSiswa = $user->calonSiswa()->firstOrCreate([]);
            $calonSiswa->update(['nama_lengkap' => $validated['name']]);
        });

        return redirect()->route('calon.profil.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}
