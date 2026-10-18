<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\ShortUrl>
 */
class ShortUrlFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            // Keep the URL's company in sync with its creator's company.
            'company_id' => fn (array $attributes) => User::find($attributes['user_id'])->company_id,
            'code' => Str::lower(Str::random(7)),
            'long_url' => fake()->url(),
            'hits' => 0,
        ];
    }
}
