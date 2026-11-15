<?php

namespace App\Enums;

enum StatusDokumen: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Verifikasi',
            self::APPROVED => 'Disetujui',
            self::REJECTED => 'Ditolak',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'text-bg-warning',
            self::APPROVED => 'text-bg-success',
            self::REJECTED => 'text-bg-danger',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
