<?php

namespace App\Enums;

enum StatusPendaftaran: string
{
    case PENDING = 'pending';
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case VERIFIED = 'verified';
    case REJECTED = 'rejected';
    case KUOTA_PENUH = 'kuota_penuh';
    case SELECTED = 'selected';
    case NOT_SELECTED = 'not_selected';
    case REGISTERED_ULANG = 'registered_ulang';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu',
            self::DRAFT => 'Draft',
            self::SUBMITTED => 'Diajukan',
            self::VERIFIED => 'Terverifikasi',
            self::REJECTED => 'Ditolak / Perlu Revisi',
            self::KUOTA_PENUH => 'Kuota Penuh',
            self::SELECTED => 'Diterima',
            self::NOT_SELECTED => 'Tidak Diterima',
            self::REGISTERED_ULANG => 'Daftar Ulang Selesai',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'text-bg-secondary',
            self::DRAFT => 'text-bg-secondary',
            self::SUBMITTED => 'text-bg-primary',
            self::VERIFIED => 'text-bg-success',
            self::REJECTED => 'text-bg-danger',
            self::KUOTA_PENUH => 'text-bg-warning',
            self::SELECTED => 'text-bg-success',
            self::NOT_SELECTED => 'text-bg-danger',
            self::REGISTERED_ULANG => 'text-bg-dark',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
