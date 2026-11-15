<?php

namespace App\Http\Requests\Calon;

use Illuminate\Foundation\Http\FormRequest;

class OrangTuaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'ayah' => ['nullable', 'array'],
            'ayah.nama' => ['nullable', 'string', 'max:255', 'required_without_all:ibu.nama,wali.nama'],
            'ayah.pekerjaan' => ['nullable', 'string', 'max:255'],
            'ayah.no_hp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'ayah.alamat' => ['nullable', 'string', 'max:1000'],

            'ibu' => ['nullable', 'array'],
            'ibu.nama' => ['nullable', 'string', 'max:255', 'required_without_all:ayah.nama,wali.nama'],
            'ibu.pekerjaan' => ['nullable', 'string', 'max:255'],
            'ibu.no_hp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'ibu.alamat' => ['nullable', 'string', 'max:1000'],

            'wali' => ['nullable', 'array'],
            'wali.nama' => ['nullable', 'string', 'max:255', 'required_without_all:ayah.nama,ibu.nama'],
            'wali.pekerjaan' => ['nullable', 'string', 'max:255'],
            'wali.no_hp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'wali.alamat' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'ayah.nama.required_without_all' => 'Minimal salah satu data ayah, ibu, atau wali harus diisi.',
            'ibu.nama.required_without_all' => 'Minimal salah satu data ayah, ibu, atau wali harus diisi.',
            'wali.nama.required_without_all' => 'Minimal salah satu data ayah, ibu, atau wali harus diisi.',
            'ayah.nama.max' => 'Nama ayah maksimal 255 karakter.',
            'ibu.nama.max' => 'Nama ibu maksimal 255 karakter.',
            'wali.nama.max' => 'Nama wali maksimal 255 karakter.',
            'ayah.pekerjaan.max' => 'Pekerjaan ayah maksimal 255 karakter.',
            'ibu.pekerjaan.max' => 'Pekerjaan ibu maksimal 255 karakter.',
            'wali.pekerjaan.max' => 'Pekerjaan wali maksimal 255 karakter.',
            'ayah.no_hp.max' => 'Nomor HP ayah maksimal 20 karakter.',
            'ibu.no_hp.max' => 'Nomor HP ibu maksimal 20 karakter.',
            'wali.no_hp.max' => 'Nomor HP wali maksimal 20 karakter.',
            'ayah.no_hp.regex' => 'Nomor HP ayah hanya boleh berisi angka, spasi, tanda plus, atau tanda minus.',
            'ibu.no_hp.regex' => 'Nomor HP ibu hanya boleh berisi angka, spasi, tanda plus, atau tanda minus.',
            'wali.no_hp.regex' => 'Nomor HP wali hanya boleh berisi angka, spasi, tanda plus, atau tanda minus.',
        ];
    }
}
