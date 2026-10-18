<?php

namespace Tests\Feature;

use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortUrlRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_short_url_is_publicly_resolvable_and_redirects_to_original_url(): void
    {
        $member = User::factory()->member()->create();

        $shortUrl = ShortUrl::factory()->create([
            'user_id' => $member->id,
            'company_id' => $member->company_id,
            'code' => 'pub1234',
            'long_url' => 'https://example.com/original',
        ]);

        $this->assertGuest();

        $this->get('/s/pub1234')->assertRedirect('https://example.com/original');

        $this->assertSame(1, $shortUrl->fresh()->hits);
    }

    public function test_unknown_code_returns_404(): void
    {
        $this->get('/s/doesnotexist')->assertNotFound();
    }
}
