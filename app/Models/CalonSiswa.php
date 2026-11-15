<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalonSiswa extends Model
{
    use HasFactory;

    protected $table = 'calon_siswa';

    protected $fillable = [
        'user_id',
        'nik',
        'nisn',
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'no_hp',
        'asal_sekolah',
        'foto',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orangTua()
    {
        return $this->hasMany(OrangTua::class, 'calon_siswa_id');
    }

    public function pendaftaran()
    {
        return $this->hasMany(Pendaftaran::class, 'calon_siswa_id');
    }
}
