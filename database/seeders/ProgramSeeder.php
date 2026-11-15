<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $program = [
            ['kode' => 'PPLG', 'nama' => 'Pengembangan Perangkat Lunak dan Gim', 'kuota' => 72],
            ['kode' => 'TJKT', 'nama' => 'Teknik Jaringan Komputer dan Telekomunikasi', 'kuota' => 72],
            ['kode' => 'DKV', 'nama' => 'Desain Komunikasi Visual', 'kuota' => 36],
            ['kode' => 'AKL', 'nama' => 'Akuntansi dan Keuangan Lembaga', 'kuota' => 36],
        ];

        foreach ($program as $item) {
            Program::updateOrCreate(['kode' => $item['kode']], $item);
        }
    }
}
