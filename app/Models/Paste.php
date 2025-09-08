<?php

namespace App\Models;

use App\Enums\ColorScheme;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use App\Enums\ExpiryOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Paste extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pastes';

    protected $casts = [
        'expiry' => ExpiryOption::class,  
        'color_scheme' => ColorScheme::class,
        'custom_expiry' => 'immutable_datetime', 
    ];

    public static function fromRequest(Request $request): self
    {
        return static::createNew(new static, $request);
    }

    public static function fromFork(self $fork, Request $request): self
    {
        $paste = new static;
        $paste->parent_id = $fork->id;

        return static::createNew($paste, $request);
    }

    private static function createNew(self $paste, Request $request): self
    {
        $paste->code = $request->get('code');
        $paste->hash = Uuid::uuid4()->toString();
        $paste->expires_at = self::parseExpiry($request);
        $paste->color_scheme = $request->input('color_scheme');
        $paste->password = self::hashPasswordIfProvided($request);

        $paste->save();

        return $paste;
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'hash';
    }

    private static function parseExpiry(Request $request): ?Carbon
    {
        return match ($request->input('expiry')) {
            '1_hour' => now()->addHour(),
            '1_day' => now()->addDay(),
            '1_week' => now()->addWeek(),
            'custom' => $request->filled('custom_expiry') 
                ? Carbon::parse($request->input('custom_expiry')) 
                : null,
            default => null,
        };
    }

    private static function hashPasswordIfProvided(Request $request): ?string
    {
        return $request->filled('password') 
            ? Hash::make($request->password) 
            : null;
    }
}
