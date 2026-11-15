<?php

namespace App\Services;

use App\Enums\StatusPendaftaran;
use App\Models\Pendaftaran;
use App\Models\Pengumuman;
use App\Models\Program;
use App\Models\Seleksi;
use Illuminate\Support\Facades\DB;

/**
 * Menentukan penerimaan pendaftar berdasarkan kuota per program/jurusan.
 *
 * Kuota adalah satu-satunya penentu: begitu berkas diverifikasi panitia,
 * pendaftar dialokasikan ke program pilihan 1 (atau pilihan 2 sebagai
 * cadangan) selama masih tersedia kursi.
 */
class KuotaService
{
    /** Pendaftaran yang sudah pernah dialokasikan ke suatu program. */
    public function sudahDialokasikan(Pendaftaran $pendaftaran): bool
    {
        return $pendaftaran->seleksi()->whereNotNull('program_diterima_id')->exists();
    }

    /** Jumlah kursi yang sudah terisi pada program. */
    public function terpakai(Program $program): int
    {
        return (int) $program->seleksi()->whereNotNull('program_diterima_id')->count();
    }

    /** Sisa kursi yang masih tersedia pada program. */
    public function sisa(Program $program): int
    {
        return max(0, (int) $program->kuota - $this->terpakai($program));
    }

    /** Apakah kursi pada program sudah habis. */
    public function penuh(Program $program): bool
    {
        return $this->sisa($program) <= 0;
    }

    /**
     * Peta sisa kuota seluruh program.
     *
     * @return array<int, int>
     */
    public function petaSisaKuota(): array
    {
        $terpakai = Seleksi::query()
            ->whereNotNull('program_diterima_id')
            ->select('program_diterima_id')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('program_diterima_id')
            ->pluck('total', 'program_diteramina_id')
            ->map(fn ($total) => (int) $total)
            ->all();

        return Program::query()
            ->get()
            ->mapWithKeys(fn (Program $program) => [
                $program->id => max(0, (int) $program->kuota - ($terpakai[$program->id] ?? 0)),
            ])
            ->all();
    }

    /**
     * Alokasikan kursi untuk pendaftar yang berkasnya sudah diverifikasi.
     *
     * Prioritas program pilihan 1, Falls back ke pilihan 2 bila kursi
     * pilihan 1 sudah habis. Bila keduanya penuh, pendaftar ditandai
     * KUOTA_PENUH agar dapat memilih program lain.
     */
    public function alokasikan(Pendaftaran $pendaftaran): ?Program
    {
        return DB::transaction(function () use ($pendaftaran) {
            $pendaftaran = Pendaftaran::whereKey($pendaftaran->id)->lockForUpdate()->first();

            if (! $pendaftaran) {
                return null;
            }

            $sudah = $pendaftaran->seleksi()->whereNotNull('program_diterima_id')->first();

            if ($sudah) {
                return $sudah->programDiterima;
            }

            foreach ($this->kandidatProgram($pendaftaran) as $program) {
                $program = Program::whereKey($program->id)->lockForUpdate()->first();

                if (! $program || $this->sisa($program) <= 0) {
                    continue;
                }

                $this->tandaiDiterima($pendaftaran, $program);

                return $program;
            }

            $this->tandaiKuotaPenuh($pendaftaran);

            return null;
        });
    }

    /**
     * Lepaskan alokasi kursi (misal berkas ditolak atau ditarik kembali),
     * supaya kursi dapat diisi pendaftar lain.
     */
    public function lepas(Pendaftaran $pendaftaran): void
    {
        DB::transaction(function () use ($pendaftaran) {
            $pendaftaran = Pendaftaran::whereKey($pendaftaran->id)->lockForUpdate()->first();

            if (! $pendaftaran) {
                return;
            }

            $pendaftaran->seleksi()->delete();
            $pendaftaran->pengumuman()->delete();
        });
    }

    /**
     * Daftar program kandidat pendaftar (pilihan 1 lalu pilihan 2).
     *
     * @return \Illuminate\Support\Collection<int, Program>
     */
    private function kandidatProgram(Pendaftaran $pendaftaran)
    {
        $ids = collect([
            $pendaftaran->program_pilihan_1_id,
            $pendaftaran->program_pilihan_2_id,
        ])->filter()->unique()->values();

        return Program::whereIn('id', $ids)->get();
    }

    private function tandaiDiterima(Pendaftaran $pendaftaran, Program $program): void
    {
        Seleksi::updateOrCreate(
            ['pendaftaran_id' => $pendaftaran->id],
            [
                'program_diterima_id' => $program->id,
                'status' => 'diterima',
                'keterangan' => 'Diterima pada program '.$program->nama.' sesuai kuota yang tersedia.',
            ]
        );

        Pengumuman::updateOrCreate(
            ['pendaftaran_id' => $pendaftaran->id],
            [
                'ppdb_id' => $pendaftaran->ppdb_id,
                'hasil' => Pengumuman::HASIL_DITERIMA,
                'keterangan' => 'Diterima pada program '.$program->nama.' ('.$program->kode.') sesuai kuota yang tersedia.',
                'dipublikasikan' => true,
                'tanggal_pengumuman' => now(),
            ]
        );

        $pendaftaran->update([
            'status' => StatusPendaftaran::SELECTED,
            'keterangan' => 'Diterima pada program '.$program->nama.' ('.$program->kode.').',
        ]);
    }

    private function tandaiKuotaPenuh(Pendaftaran $pendaftaran): void
    {
        $namaProgram = $pendaftaran->programPilihan1?->nama
            ?? $pendaftaran->programPilihan2?->nama
            ?? 'program yang dipilih';

        $pendaftaran->update([
            'status' => StatusPendaftaran::KUOTA_PENUH,
            'keterangan' => 'Kuota '.$namaProgram.' sudah penuh. Silakan pilih program yang masih tersedia.',
        ]);
    }
}
