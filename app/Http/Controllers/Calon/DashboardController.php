<?php

namespace App\Http\Controllers\Calon;

use App\Enums\StatusPendaftaran;
use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Services\PendaftaranService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private readonly PendaftaranService $flow) {}

    public function index(Request $request)
    {
        $siswa = $this->flow->calonSiswa($request->user());
        $pendaftaran = $this->flow->pendaftaranFor($siswa);

        if ($pendaftaran) {
            $pendaftaran->load(['programPilihan1', 'programPilihan2', 'ppdb']);
        }

        return view('calon.dashboard', [
            'siswa' => $siswa,
            'pendaftaran' => $pendaftaran,
            'nextRoute' => $this->flow->nextStepRoute($siswa, $pendaftaran),
            'canContinue' => $this->flow->isEditable($pendaftaran),
            'isOpen' => $this->flow->isOpen(),
            'locked' => $this->flow->isLocked($pendaftaran),
            'closedMessage' => $this->flow->closedMessage(),
            'progress' => $this->progress($pendaftaran),
        ]);
    }

    private function progress(?Pendaftaran $pendaftaran): array
    {
        $labels = [
            'Pengisian Formulir',
            'Pengajuan',
            'Verifikasi',
            'Hasil Seleksi',
            'Daftar Ulang',
        ];

        $current = match ($pendaftaran?->status) {
            null, StatusPendaftaran::DRAFT, StatusPendaftaran::REJECTED => 0,
            StatusPendaftaran::SUBMITTED => 1,
            StatusPendaftaran::VERIFIED => 2,
            StatusPendaftaran::SELECTED, StatusPendaftaran::NOT_SELECTED => 3,
            StatusPendaftaran::REGISTERED_ULANG => 5,
            default => 0,
        };

        $rejected = $pendaftaran?->status === StatusPendaftaran::REJECTED;

        return collect($labels)->map(function (string $label, int $index) use ($current, $rejected) {
            $state = match (true) {
                $index < $current => 'done',
                $index === $current && $rejected => 'rejected',
                $index === $current => 'current',
                default => 'upcoming',
            };

            return [
                'label' => $label,
                'state' => $state,
            ];
        })->all();
    }
}
