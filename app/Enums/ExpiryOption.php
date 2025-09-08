<?php 

namespace App\Enums;

enum ExpiryOption: string
{
    case ONE_HOUR = '1_hour';
    case ONE_DAY = '1_day';
    case ONE_WEEK = '1_week';
    case NEVER = 'never';
    case CUSTOM = 'custom';

    public static function fromInput(?string $value): self
    {
        return match ($value) {
            '1_hour' => self::ONE_HOUR,
            '1_day' => self::ONE_DAY,
            '1_week' => self::ONE_WEEK,
            'custom' => self::CUSTOM,
            default => self::NEVER,
        };
    }
}