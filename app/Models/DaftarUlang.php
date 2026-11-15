<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarUlang extends Model
{
    use HasFactory;

    protected $table = 'daftar_ulang';

    protected $fillable = [
        'ppdb_id',
        'pendaftaran_id',
        'tanggal_daftar_ulang',
        'status',
        'keterangan',
        'ukuran_seragam',
        'bukti_pembayaran',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_daftar_ulang' => 'date',
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
