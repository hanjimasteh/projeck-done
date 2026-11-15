<?php

namespace Database\Seeders;

use App\Models\Ppdb;
use Illuminate\Database\Seeder;

class PpdbSeeder extends Seeder
{
    public function run(): void
    {
        Ppdb::updateOrCreate(
            ['tahun_ajaran' => '2026/2027'],
            [
                'tanggal_mulai' => '2026-03-01',
                'tanggal_selesai' => '2026-06-30',
                'status' => 'selesai',
            ]
        );

        Ppdb::updateOrCreate(
            ['tahun_ajaran' => '2027/2028'],
            [
                'tanggal_mulai' => '2027-03-01',
                'tanggal_selesai' => '2027-06-30',
                'status' => 'aktif',
            ]
        );
    }
}
