<?php

namespace Database\Seeders;

use App\Models\Persyaratan;
use Illuminate\Database\Seeder;

class PersyaratanSeeder extends Seeder
{
    public function run(): void
    {
        $persyaratan = [
            [
                'nama' => 'Kartu Keluarga',
                'deskripsi' => 'Unggah Kartu Keluarga yang masih berlaku dan terbaca dengan jelas.',
                'wajib' => true,
            ],
            [
                'nama' => 'Akta Kelahiran',
                'deskripsi' => 'Unggah akta kelahiran asli atau salinan legalisir.',
                'wajib' => true,
            ],
            [
                'nama' => 'Ijazah/Surat Keterangan Lulus',
                'deskripsi' => 'Unggah ijazah atau surat keterangan lulus dari sekolah asal.',
                'wajib' => false,
            ],
            [
                'nama' => 'Rapor',
                'deskripsi' => 'Unggah rapor semester terakhir sesuai ketentuan panitia.',
                'wajib' => false,
            ],
            [
                'nama' => 'Sertifikat Prestasi',
                'deskripsi' => 'Unggah sertifikat prestasi akademik atau non-akademik bila ada.',
                'wajib' => false,
            ],
            [
                'nama' => 'Pas Foto',
                'deskripsi' => 'Unggah pas foto terbaru dengan latar belakang merah atau biru.',
                'wajib' => true,
            ],
        ];

        foreach ($persyaratan as $item) {
            Persyaratan::updateOrCreate(
                [
                    'nama' => $item['nama'],
                ],
                $item
            );
        }
    }
}
