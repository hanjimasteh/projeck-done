<?php

namespace Database\Seeders;

use App\Enums\StatusPendaftaran;
use App\Models\CalonSiswa;
use App\Models\Pendaftaran;
use App\Models\Ppdb;
use App\Models\Program;
use Illuminate\Database\Seeder;

class DemoPendaftaranSeeder extends Seeder
{
    public function run(): void
    {
        $ppdb = Ppdb::where('tahun_ajaran', '2027/2028')->firstOrFail();
        $siswa = CalonSiswa::whereHas('user', fn ($query) => $query->where('username', 'pendaftar'))->firstOrFail();
        $pplg = Program::where('kode', 'PPLG')->firstOrFail();
        $tjkt = Program::where('kode', 'TJKT')->firstOrFail();

        Pendaftaran::updateOrCreate(
            [
                'ppdb_id' => $ppdb->id,
                'calon_siswa_id' => $siswa->id,
            ],
            [
                'nomor_pendaftaran' => 'PPDB-2027-0001',
                'tanggal_pendaftaran' => '2027-03-01 08:00:00',
                'program_pilihan_1_id' => $pplg->id,
                'program_pilihan_2_id' => $tjkt->id,
                'status' => StatusPendaftaran::SUBMITTED,
                'keterangan' => null,
            ]
        );
    }
}
