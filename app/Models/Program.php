<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $table = 'program';

    protected $fillable = [
        'kode',
        'nama',
        'kuota',
    ];

    protected function casts(): array
    {
        return [
            'kuota' => 'integer',
        ];
    }

    public function pendaftaranPilihan1()
    {
        return $this->hasMany(Pendaftaran::class, 'program_pilihan_1_id');
    }

    public function pendaftaranPilihan2()
    {
        return $this->hasMany(Pendaftaran::class, 'program_pilihan_2_id');
    }

    public function seleksi()
    {
        return $this->hasMany(Seleksi::class, 'program_diterima_id');
    }
}
