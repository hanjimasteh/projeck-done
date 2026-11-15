<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ppdb extends Model
{
    use HasFactory;

    protected $table = 'ppdb';

    protected $fillable = [
        'tahun_ajaran',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function pendaftaran()
    {
        return $this->hasMany(Pendaftaran::class, 'ppdb_id');
    }

    public function pengumuman()
    {
        return $this->hasMany(Pengumuman::class, 'ppdb_id');
    }

    public function daftarUlang()
    {
        return $this->hasMany(DaftarUlang::class, 'ppdb_id');
    }

    public function jadwalPpdb()
    {
        return $this->hasMany(JadwalPpdb::class, 'ppdb_id')->orderBy('urutan');
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', 'aktif');
    }
}
