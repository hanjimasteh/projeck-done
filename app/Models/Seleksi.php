<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seleksi extends Model
{
    use HasFactory;

    protected $table = 'seleksi';

    protected $fillable = [
        'pendaftaran_id',
        'program_diterima_id',
        'nilai',
        'peringkat',
        'status',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'nilai' => 'decimal:2',
            'peringkat' => 'integer',
        ];
    }

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id');
    }

    public function programDiterima()
    {
        return $this->belongsTo(Program::class, 'program_diterima_id');
    }
}
