<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ShortUrl extends Model
{
    use HasFactory;

    public const PERIODS = [
        'today' => 'Today',
        'last_week' => 'Last Week',
        'this_month' => 'This Month',
        'last_month' => 'Last Month',
    ];

    protected $fillable = [
        'company_id',
        'user_id',
        'code',
        'long_url',
        'hits',
    ];

    protected function casts(): array
    {
        return [
            'hits' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

 
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return match (true) {
            $user->isSuperAdmin() => $query,
            $user->isAdmin() => $query->where('company_id', $user->company_id),
            $user->isMember() => $query->where('user_id', $user->id),
            default => $query->whereRaw('1 = 0'),
        };
    }

    public function scopePeriod(Builder $query, ?string $period): Builder
    {
        return match ($period) {
            'today' => $query->whereDate('created_at', today()),
            'last_week' => $query->whereBetween('created_at', [
                now()->subWeek()->startOfWeek(),
                now()->subWeek()->endOfWeek(),
            ]),
            'this_month' => $query->whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ]),
            'last_month' => $query->whereBetween('created_at', [
                now()->subMonthNoOverflow()->startOfMonth(),
                now()->subMonthNoOverflow()->endOfMonth(),
            ]),
            default => $query,
        };
    }

    public static function generateCode(int $length = 7): string
    {
        do {
            $code = Str::lower(Str::random($length));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    public function getShortLinkAttribute(): string
    {
        return route('short-urls.redirect', $this->code);
    }
}
