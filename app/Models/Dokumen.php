<?php

namespace App\Models;

use App\Enums\StatusDokumen;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    use HasFactory;

    protected $table = 'dokumen';

    protected $fillable = [
        'pendaftaran_id',
        'jenis_dokumen_id',
        'file',
        'status',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusDokumen::class,
        ];
    }

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id');
    }

    public function jenisDokumen()
    {
        return $this->belongsTo(JenisDokumen::class, 'jenis_dokumen_id');
    }
}
