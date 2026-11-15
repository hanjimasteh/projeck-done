<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory;

    public const HASIL_DITERIMA = 'diterima';

    public const HASIL_TIDAK_DITERIMA = 'tidak_diterima';

    protected $table = 'pengumuman';

    protected $fillable = [
        'ppdb_id',
        'pendaftaran_id',
        'tanggal_pengumuman',
        'hasil',
        'keterangan',
        'dipublikasikan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengumuman' => 'datetime',
            'dipublikasikan' => 'boolean',
        ];
    }

    public function ppdb()
    {
        return $this->belongsTo(Ppdb::class, 'ppdb_id');
    }

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id');
    }
}
