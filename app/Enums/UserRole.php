<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case COLLECTOR = 'collector';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::COLLECTOR => 'Collector',
        };
    }

    public static function labels(): array
    {
        return array_column(self::cases(), 'label');
    }

    public static function fromValue(string $value): ?self
    {
        return self::tryFrom($value);
    }
}
