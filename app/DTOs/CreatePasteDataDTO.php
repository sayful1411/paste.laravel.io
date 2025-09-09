<?php

namespace App\DTOs;

use App\Enums\ColorScheme;
use App\Enums\ExpiryOption;
use Carbon\CarbonImmutable;

class CreatePasteDataDTO
{
    public function __construct(
        public readonly string $code,
        public readonly ?ColorScheme $colorScheme = null,
        public readonly ExpiryOption $expiryOption = ExpiryOption::NEVER,
        public readonly ?CarbonImmutable $customExpiry = null,
        public readonly ?string $passwordPlain = null,
    ) {}  
    
}