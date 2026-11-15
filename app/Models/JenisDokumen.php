<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisDokumen extends Model
{
    use HasFactory;

    protected $table = 'jenis_dokumen';

    protected $fillable = [
        'nama',
        'kategori',
        'format_file',
        'ukuran_maks_kb',
        'wajib',
    ];

    protected function casts(): array
    {
        return [
            'ukuran_maks_kb' => 'integer',
            'wajib' => 'boolean',
        ];
    }

    public function dokumen()
    {
        return $this->hasMany(Dokumen::class, 'jenis_dokumen_id');
    }
}
