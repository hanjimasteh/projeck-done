<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrangTua extends Model
{
    use HasFactory;

    protected $table = 'orang_tua';

    protected $fillable = [
        'calon_siswa_id',
        'hubungan',
        'nama',
        'pekerjaan',
        'no_hp',
        'alamat',
    ];

    public function calonSiswa()
    {
        return $this->belongsTo(CalonSiswa::class, 'calon_siswa_id');
    }
}
