<?php

namespace App\Http\Requests\Calon;

use App\Models\Program;
use App\Services\KuotaService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'program_pilihan_1_id' => ['required', 'exists:program,id'],
            'program_pilihan_2_id' => [
                'nullable',
                'exists:program,id',
                'different:program_pilihan_1_id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'program_pilihan_1_id.required' => 'Pilihan 1 wajib dipilih.',
            'program_pilihan_1_id.exists' => 'Pilihan 1 yang dipilih tidak valid.',
            'program_pilihan_2_id.exists' => 'Pilih 2 yang dipilih tidak valid.',
            'program_pilihan_2_id.different' => 'Pilihan 2 tidak boleh sama dengan Pilihan 1.',
        ];
    }

    /**
     * Program yang kuotanya sudah penuh tidak boleh dipilih.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $kuota = app(KuotaService::class);

            foreach (['program_pilihan_1_id', 'program_pilihan_2_id'] as $field) {
                $programId = $this->input($field);

                if (! $programId) {
                    continue;
                }

                $program = Program::find($programId);

                if (! $program) {
                    continue;
                }

                if ($kuota->penuh($program)) {
                    $validator->errors()->add(
                        $field,
                        'Kuota program '.$program->nama.' ('.$program->kode.') sudah penuh. Silakan pilih program yang masih tersedia.'
                    );
                }
            }
        });
    }
}
