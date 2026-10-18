<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortUrlVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private User $adminA;
    private User $memberA1;
    private User $memberA2;
    private User $memberB;

    protected function setUp(): void
    {
        parent::setUp();

        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();

        $this->adminA = User::factory()->admin()->for($companyA)->create();
        $this->memberA1 = User::factory()->member()->for($companyA)->create();
        $this->memberA2 = User::factory()->member()->for($companyA)->create();
        $this->memberB = User::factory()->member()->for($companyB)->create();

        // Codes are unusual strings so assertSee/assertDontSee cannot match other page text.
        $this->makeUrl($this->adminA, 'qadmina');
        $this->makeUrl($this->memberA1, 'qmembra1');
        $this->makeUrl($this->memberA2, 'qmembra2');
        $this->makeUrl($this->memberB, 'qmembrb');
    }

    private function makeUrl(User $user, string $code): ShortUrl
    {
        return ShortUrl::factory()->create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'code' => $code,
        ]);
    }

    public function test_admin_sees_only_short_urls_of_their_own_company(): void
    {
        $this->actingAs($this->adminA)
            ->get(route('short-urls.index'))
            ->assertOk()
            ->assertSee('qadmina')
            ->assertSee('qmembra1')
            ->assertSee('qmembra2')
            ->assertDontSee('qmembrb');
    }

    public function test_member_sees_only_short_urls_created_by_themselves(): void
    {
        $this->actingAs($this->memberA1)
            ->get(route('short-urls.index'))
            ->assertOk()
            ->assertSee('qmembra1')
            ->assertDontSee('qadmina')
            ->assertDontSee('qmembra2')
            ->assertDontSee('qmembrb');
    }

    public function test_super_admin_sees_short_urls_of_every_company(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)
            ->get(route('short-urls.index'))
            ->assertOk()
            ->assertSee('qadmina')
            ->assertSee('qmembra1')
            ->assertSee('qmembra2')
            ->assertSee('qmembrb');
    }

    public function test_csv_export_follows_the_same_visibility_rule(): void
    {
        $content = $this->actingAs($this->memberA1)
            ->get(route('short-urls.export'))
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString('qmembra1', $content);
        $this->assertStringNotContainsString('qmembra2', $content);
        $this->assertStringNotContainsString('qmembrb', $content);
    }

    public function test_period_filter_limits_the_list(): void
    {
        ShortUrl::where('code', 'qmembra1')->update(['created_at' => now()->subMonths(3)]);

        $this->actingAs($this->adminA)
            ->get(route('short-urls.index', ['period' => 'this_month']))
            ->assertOk()
            ->assertSee('qadmina')
            ->assertDontSee('qmembra1');
    }
}
