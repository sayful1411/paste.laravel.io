<?php 

namespace App\Services;

use App\Models\Paste;
use Ramsey\Uuid\Uuid;
use App\Enums\ExpiryOption;
use Carbon\CarbonImmutable;
use App\DTOs\CreatePasteDataDTO;
use Illuminate\Support\Facades\Hash;

class PasteService
{
    public function create(CreatePasteDataDTO $data): Paste
    {
        $expiresAt = $this->resolveExpiry($data->expiryOption, $data->customExpiry);

        $paste = new Paste();
        $paste->code = $data->code;
        $paste->hash = Uuid::uuid4()->toString();
        $paste->expires_at = $expiresAt;
        $paste->color_scheme = $data->colorScheme;
        $paste->password = $data->passwordPlain ? Hash::make($data->passwordPlain) : null;

        $paste->save();

        return $paste;

    }

    private function resolveExpiry(ExpiryOption $option, ?CarbonImmutable $custom): ?CarbonImmutable
    {
        return match ($option) {
            ExpiryOption::ONE_HOUR => CarbonImmutable::now()->addHour(),
            ExpiryOption::ONE_DAY => CarbonImmutable::now()->addDay(),
            ExpiryOption::ONE_WEEK => CarbonImmutable::now()->addWeek(),
            ExpiryOption::CUSTOM => $custom,
            default => null,
        };
    }

    
}