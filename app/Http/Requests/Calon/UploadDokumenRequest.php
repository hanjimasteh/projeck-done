<?php

namespace App\Http\Requests\Calon;

use App\Models\JenisDokumen;
use App\Services\DokumenService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UploadDokumenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $jenis = $this->jenisDokumen();

        $rules = [
            'jenis_dokumen_id' => ['required', 'integer', 'exists:jenis_dokumen,id'],
            'file' => ['required', 'file'],
        ];

        if (! $jenis) {
            return $rules;
        }

        $service = app(DokumenService::class);

        $rules['file'][] = 'max:'.$jenis->ukuran_maks_kb;
        $rules['file'][] = 'mimetypes:'.implode(',', $service->allowedMimeTypes($jenis));

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $jenis = $this->jenisDokumen();
            $file = $this->file('file');

            if (! $jenis || ! $file || ! $file->isValid()) {
                return;
            }

            $service = app(DokumenService::class);
            $extension = strtolower($file->getClientOriginalExtension());

            if (! in_array($extension, $service->allowedExtensions($jenis), true)) {
                $validator->errors()->add(
                    'file',
                    'Ekstensi file tidak diizinkan. Format yang diperbolehkan: '.$service->formatLabel($jenis).'.'
                );
            }
        });
    }

    public function messages(): array
    {
        $jenis = $this->jenisDokumen();
        $service = app(DokumenService::class);

        return [
            'jenis_dokumen_id.required' => 'Jenis dokumen wajib dipilih.',
            'jenis_dokumen_id.exists' => 'Jenis dokumen tidak ditemukan.',
            'file.required' => 'Pilih file dokumen yang akan diunggah.',
            'file.file' => 'Berkas yang diunggah tidak valid.',
            'file.max' => $jenis
                ? 'Ukuran file tidak boleh melebihi '.$service->sizeLabel($jenis->ukuran_maks_kb).'.'
                : 'Ukuran file terlalu besar.',
            'file.mimetypes' => $jenis
                ? 'Format file harus salah satu dari: '.$service->formatLabel($jenis).'.'
                : 'Format file tidak diizinkan.',
        ];
    }

    private function jenisDokumen(): ?JenisDokumen
    {
        if (! $this->filled('jenis_dokumen_id')) {
            return null;
        }

        return JenisDokumen::find($this->input('jenis_dokumen_id'));
    }
}
