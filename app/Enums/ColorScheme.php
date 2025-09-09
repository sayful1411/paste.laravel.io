<?php 

namespace App\Enums;

enum ColorScheme: string
{
    case GITHUB_DARK = 'github-dark';
    case MATERIAL_THEME_PALENIGHT = 'material-theme-palenight';
    case DRACULA = 'dracula';
    case NORD = 'nord';
    case LIVER_DARK = 'liver-dark';

    public static function fromInput(?string $value): self|null
    {
        return match ($value) {
            'github-dark' => self::GITHUB_DARK,
            'material-theme-palenight' => self::MATERIAL_THEME_PALENIGHT,
            'dracula' => self::DRACULA,
            'nord' => self::NORD,
            'liver-dark' => self::LIVER_DARK,
            default => null,
        };
    }
}