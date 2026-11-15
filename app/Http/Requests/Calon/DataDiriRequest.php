<?php

namespace App\Http\Requests\Calon;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DataDiriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $calonSiswaId = $this->user()?->calonSiswa()->value('id');

        return [
            'nik' => [
                'required',
                'digits:16',
                Rule::unique('calon_siswa', 'nik')->ignore($calonSiswaId),
            ],
            'nisn' => ['required', 'digits:10'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'tempat_lahir' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'alamat' => ['required', 'string', 'max:1000'],
            'no_hp' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'asal_sekolah' => ['required', 'string', 'max:255'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus terdiri dari 16 digit angka.',
            'nik.unique' => 'NIK sudah digunakan oleh pendaftar lain.',
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.digits' => 'NISN harus terdiri dari 10 digit angka.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nama_lengkap.max' => 'Nama lengkap maksimal 255 karakter.',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid.',
            'alamat.required' => 'Alamat wajib diisi.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.max' => 'Nomor HP maksimal 20 karakter.',
            'no_hp.regex' => 'Nomor HP hanya boleh berisi angka, spasi, tanda plus, atau tanda minus.',
            'asal_sekolah.required' => 'Asal sekolah wajib diisi.',
            'foto.image' => 'Foto harus berupa gambar.',
            'foto.mimes' => 'Foto harus berformat JPG, JPEG, atau PNG.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ];
    }
}
