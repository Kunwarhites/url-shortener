<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_super_admin(): void
    {
        $this->seed(SuperAdminSeeder::class);

        $user = User::where('email', 'superadmin@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame(Role::SuperAdmin, $user->role);
        $this->assertNull($user->company_id);
    }

    public function test_seeder_can_run_twice_without_duplicates(): void
    {
        $this->seed(SuperAdminSeeder::class);
        $this->seed(SuperAdminSeeder::class);

        $this->assertSame(1, User::where('email', 'superadmin@example.com')->count());
    }

    public function test_user_can_login_and_logout(): void
    {
        $user = User::factory()->member()->create();

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_wrong_password_is_rejected(): void
    {
        $user = User::factory()->member()->create();

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_dashboard_requires_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_each_role_can_open_the_dashboard(): void
    {
        foreach ([User::factory()->superAdmin()->create(), User::factory()->admin()->create(), User::factory()->member()->create()] as $user) {
            $this->actingAs($user)->get(route('dashboard'))->assertOk();
        }
    }
}
