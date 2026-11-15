<?php

namespace Database\Seeders;

use App\Models\JadwalPpdb;
use App\Models\Ppdb;
use Illuminate\Database\Seeder;

class JadwalPpdbSeeder extends Seeder
{
    public function run(): void
    {
        $ppdb = Ppdb::where('tahun_ajaran', '2027/2028')->firstOrFail();

        $jadwal = [
            ['tahapan' => 'Pendaftaran', 'tanggal_mulai' => '2027-03-01', 'tanggal_selesai' => '2027-06-30', 'urutan' => 1],
            ['tahapan' => 'Verifikasi', 'tanggal_mulai' => '2027-03-01', 'tanggal_selesai' => '2027-07-02', 'urutan' => 2],
            ['tahapan' => 'Seleksi', 'tanggal_mulai' => '2027-07-03', 'tanggal_selesai' => '2027-07-05', 'urutan' => 3],
            ['tahapan' => 'Pengumuman', 'tanggal_mulai' => '2027-07-07', 'tanggal_selesai' => '2027-07-07', 'urutan' => 4],
            ['tahapan' => 'Daftar Ulang', 'tanggal_mulai' => '2027-07-08', 'tanggal_selesai' => '2027-07-12', 'urutan' => 5],
        ];

        foreach ($jadwal as $item) {
            JadwalPpdb::updateOrCreate(
                [
                    'ppdb_id' => $ppdb->id,
                    'tahapan' => $item['tahapan'],
                ],
                $item + ['ppdb_id' => $ppdb->id]
            );
        }
    }
}
