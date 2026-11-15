<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\CalonSiswa;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator PPDB',
                'email' => 'admin@ppdb.test',
                'password' => 'password',
                'role' => Role::ADMIN,
                'status' => 'aktif',
            ]
        );

        $pendaftar = User::updateOrCreate(
            ['username' => 'pendaftar'],
            [
                'name' => 'Calon Pendaftar Demo',
                'email' => 'pendaftar@ppdb.test',
                'password' => 'password',
                'role' => Role::PENDAFTAR,
                'status' => 'aktif',
            ]
        );

        CalonSiswa::updateOrCreate(
            ['user_id' => $pendaftar->id],
            [
                'nik' => '3273010101090001',
                'nisn' => '1234567890',
                'nama_lengkap' => 'Calon Pendaftar Demo',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '2009-01-01',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Contoh No. 1, Bandung',
                'no_hp' => '081234567890',
                'asal_sekolah' => 'SMP Negeri 1 Bandung',
                'status' => 'aktif',
            ]
        );
    }
}
