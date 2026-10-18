<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortUrlCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_short_url(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('short-urls.store'), ['long_url' => 'https://example.com/admin-page'])
            ->assertRedirect();

        $this->assertDatabaseHas('short_urls', [
            'user_id' => $admin->id,
            'company_id' => $admin->company_id,
            'long_url' => 'https://example.com/admin-page',
        ]);
    }

    public function test_member_can_create_short_url(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)
            ->post(route('short-urls.store'), ['long_url' => 'https://example.com/member-page'])
            ->assertRedirect();

        $this->assertDatabaseHas('short_urls', [
            'user_id' => $member->id,
            'company_id' => $member->company_id,
        ]);
    }

    public function test_super_admin_cannot_create_short_url(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)
            ->post(route('short-urls.store'), ['long_url' => 'https://example.com/nope'])
            ->assertForbidden();

        $this->assertDatabaseCount('short_urls', 0);
    }

    public function test_guest_cannot_create_short_url(): void
    {
        $this->post(route('short-urls.store'), ['long_url' => 'https://example.com'])
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('short_urls', 0);
    }

    public function test_long_url_must_be_a_valid_url(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)
            ->post(route('short-urls.store'), ['long_url' => 'not-a-url'])
            ->assertSessionHasErrors('long_url');

        $this->assertDatabaseCount('short_urls', 0);
    }

    public function test_generated_short_url_is_saved_under_creator_company(): void
    {
        $company = Company::factory()->create();
        $member = User::factory()->member()->for($company)->create();

        $this->actingAs($member)
            ->post(route('short-urls.store'), ['long_url' => 'https://example.com']);

        $this->assertSame($company->id, $member->shortUrls()->first()->company_id);
    }
}
