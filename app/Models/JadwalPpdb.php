<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPpdb extends Model
{
    use HasFactory;

    protected $table = 'jadwal_ppdb';

    protected $fillable = [
        'ppdb_id',
        'tahapan',
        'tanggal_mulai',
        'tanggal_selesai',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'urutan' => 'integer',
        ];
    }

    public function ppdb()
    {
        return $this->belongsTo(Ppdb::class, 'ppdb_id');
    }

    public function sedangBerlangsung(): bool
    {
        $today = now()->startOfDay();

        return $today->between(
            $this->tanggal_mulai->copy()->startOfDay(),
            $this->tanggal_selesai->copy()->endOfDay()
        );
    }

    public function sudahSelesai(): bool
    {
        return $this->tanggal_selesai->copy()->endOfDay()->isPast();
    }

    public function statusLabel(): string
    {
        if ($this->sedangBerlangsung()) {
            return 'Sedang Berlangsung';
        }

        return $this->sudahSelesai() ? 'Selesai' : 'Akan Datang';
    }

    public function statusBadgeClass(): string
    {
        if ($this->sedangBerlangsung()) {
            return 'success';
        }

        return $this->sudahSelesai() ? 'secondary' : 'info';
    }
}
