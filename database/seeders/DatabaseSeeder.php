<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PpdbSeeder::class,
            ProgramSeeder::class,
            JadwalPpdbSeeder::class,
            JenisDokumenSeeder::class,
            PersyaratanSeeder::class,
            UserSeeder::class,
            DemoPendaftaranSeeder::class,
        ]);
    }
}
