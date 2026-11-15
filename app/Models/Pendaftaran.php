<?php

namespace App\Models;

use App\Enums\StatusPendaftaran;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendaftaran extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran';

    protected $fillable = [
        'ppdb_id',
        'calon_siswa_id',
        'nomor_pendaftaran',
        'tanggal_pendaftaran',
        'program_pilihan_1_id',
        'program_pilihan_2_id',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_pendaftaran' => 'datetime',
        'status' => StatusPendaftaran::class,
    ];

    public function ppdb()
    {
        return $this->belongsTo(Ppdb::class, 'ppdb_id');
    }

    public function calonSiswa()
    {
        return $this->belongsTo(CalonSiswa::class, 'calon_siswa_id');
    }

    public function dokumen()
    {
        return $this->hasMany(Dokumen::class, 'pendaftaran_id');
    }

    public function daftarUlang()
    {
        return $this->hasOne(DaftarUlang::class, 'pendaftaran_id');
    }

    public function programPilihan1()
    {
        return $this->belongsTo(Program::class, 'program_pilihan_1_id');
    }

    public function programPilihan2()
    {
        return $this->belongsTo(Program::class, 'program_pilihan_2_id');
    }

    public function seleksi()
    {
        return $this->hasOne(Seleksi::class, 'pendaftaran_id');
    }

    public function pengumuman()
    {
        return $this->hasOne(Pengumuman::class, 'pendaftaran_id');
    }
}