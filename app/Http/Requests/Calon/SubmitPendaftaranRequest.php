<?php

namespace App\Http\Requests\Calon;

use Illuminate\Foundation\Http\FormRequest;

class SubmitPendaftaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'pernyataan' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'pernyataan.accepted' => 'Anda harus menyatakan bahwa seluruh data yang diisi benar dan dapat dipertanggungjawabkan.',
        ];
    }
}
