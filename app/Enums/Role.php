<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'admin';
    case PENDAFTAR = 'pendaftar';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::PENDAFTAR => 'Calon Peserta Didik',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::ADMIN => 'text-bg-dark',
            self::PENDAFTAR => 'text-bg-primary',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
