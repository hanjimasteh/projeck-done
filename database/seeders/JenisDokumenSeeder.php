<?php

namespace Database\Seeders;

use App\Models\JenisDokumen;
use Illuminate\Database\Seeder;

class JenisDokumenSeeder extends Seeder
{
    public function run(): void
    {
        $jenisDokumen = [
            [
                'nama' => 'Kartu Keluarga',
                'kategori' => 'Identitas',
                'format_file' => 'pdf,jpg,png',
                'ukuran_maks_kb' => 2048,
                'wajib' => true,
            ],
            [
                'nama' => 'Akta Kelahiran',
                'kategori' => 'Identitas',
                'format_file' => 'pdf,jpg,png',
                'ukuran_maks_kb' => 2048,
                'wajib' => true,
            ],
            [
                'nama' => 'Ijazah/Surat Keterangan Lulus',
                'kategori' => 'Akademik',
                'format_file' => 'pdf,jpg,png',
                'ukuran_maks_kb' => 2048,
                'wajib' => false,
            ],
            [
                'nama' => 'Rapor',
                'kategori' => 'Akademik',
                'format_file' => 'pdf,jpg,png',
                'ukuran_maks_kb' => 2048,
                'wajib' => false,
            ],
            [
                'nama' => 'Sertifikat Prestasi',
                'kategori' => 'Prestasi',
                'format_file' => 'pdf,jpg,png',
                'ukuran_maks_kb' => 2048,
                'wajib' => false,
            ],
            [
                'nama' => 'Pas Foto',
                'kategori' => 'Identitas',
                'format_file' => 'jpg,png',
                'ukuran_maks_kb' => 2048,
                'wajib' => true,
            ],
        ];

        foreach ($jenisDokumen as $item) {
            JenisDokumen::updateOrCreate(['nama' => $item['nama']], $item);
        }
    }
}
