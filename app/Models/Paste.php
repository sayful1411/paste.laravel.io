<?php

namespace App\Models;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
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

        switch ($request->input('expiry')) {
            case '1_hour':
                $paste->expires_at = now()->addHour();
                break;
            case '1_day':
                $paste->expires_at = now()->addDay();
                break;
            case '1_week':
                $paste->expires_at = now()->addWeek();
                break;
            case 'custom':
                $customExpiry = $request->input('custom_expiry');
                $paste->expires_at = $customExpiry ? Carbon::parse($customExpiry) : null;
                break;
            case 'never':
            default:
                $paste->expires_at = null;
                break;
        }

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
}
