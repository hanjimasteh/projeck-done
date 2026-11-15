<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformasiPpdb extends Model
{
    use HasFactory;

    protected $table = 'informasi_ppdb';

    protected $fillable = [
        'ppdb_id',
        'judul',
        'isi_konten',
        'status',
        'tanggal',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public const STATUS_DRAFT = 'draft';

    public const STATUS_TERBIT = 'terbit';

    public function ppdb()
    {
        return $this->belongsTo(Ppdb::class, 'ppdb_id');
    }

    public function scopeTerbit(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_TERBIT);
    }

    public function sudahTerbit(): bool
    {
        return $this->status === self::STATUS_TERBIT;
    }

    public function statusLabel(): string
    {
        return $this->sudahTerbit() ? 'Terbit' : 'Draf';
    }

    public function statusBadgeClass(): string
    {
        return $this->sudahTerbit() ? 'text-bg-success' : 'text-bg-secondary';
    }
}
