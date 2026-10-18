<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Mail\InvitationMail;
use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
    }

    public function test_super_admin_can_invite_admin_into_a_new_company(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)
            ->post(route('invitations.store'), [
                'company_name' => 'Acme Corp',
                'name' => 'Acme Admin',
                'email' => 'admin@acme.test',
                'role' => Role::Admin->value,
            ])
            ->assertRedirect(route('dashboard'));

        $company = Company::where('name', 'Acme Corp')->first();
        $this->assertNotNull($company);

        $this->assertDatabaseHas('invitations', [
            'company_id' => $company->id,
            'email' => 'admin@acme.test',
            'role' => Role::Admin->value,
        ]);

        Mail::assertSent(InvitationMail::class);
    }

    public function test_super_admin_cannot_invite_a_member(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)
            ->post(route('invitations.store'), [
                'company_name' => 'Acme Corp',
                'name' => 'Someone',
                'email' => 'member@acme.test',
                'role' => Role::Member->value,
            ])
            ->assertSessionHasErrors('role');

        $this->assertDatabaseCount('invitations', 0);
        $this->assertDatabaseCount('companies', 0);
    }

    public function test_admin_can_invite_admin_and_member_into_own_company(): void
    {
        $admin = User::factory()->admin()->create();

        foreach ([Role::Admin, Role::Member] as $i => $role) {
            $this->actingAs($admin)
                ->post(route('invitations.store'), [
                    'name' => "User {$i}",
                    'email' => "user{$i}@acme.test",
                    'role' => $role->value,
                    'company_name' => 'Ignored Company',
                ])
                ->assertRedirect(route('dashboard'));

            $this->assertDatabaseHas('invitations', [
                'email' => "user{$i}@acme.test",
                'company_id' => $admin->company_id,
                'role' => $role->value,
            ]);
        }

        $this->assertDatabaseMissing('companies', ['name' => 'Ignored Company']);
    }

    public function test_member_cannot_invite(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)
            ->post(route('invitations.store'), [
                'name' => 'X',
                'email' => 'x@acme.test',
                'role' => Role::Member->value,
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('invitations', 0);
    }

    public function test_invitee_can_accept_and_gets_role_and_company_from_invitation(): void
    {
        $admin = User::factory()->admin()->create();

        $invitation = Invitation::create([
            'company_id' => $admin->company_id,
            'invited_by' => $admin->id,
            'name' => 'New Member',
            'email' => 'new@acme.test',
            'role' => Role::Member,
            'token' => 'test-token-123',
        ]);

        $this->get(route('invitations.accept', $invitation->token))->assertOk();

        $this->post(route('invitations.accept.store', $invitation->token), [
            'name' => 'New Member',
            'password' => 'secret-pass',
            'password_confirmation' => 'secret-pass',
            'role' => Role::Admin->value,
        ])->assertRedirect(route('dashboard'));

        $user = User::where('email', 'new@acme.test')->first();

        $this->assertAuthenticatedAs($user);
        $this->assertSame(Role::Member, $user->role);
        $this->assertSame($admin->company_id, $user->company_id);
        $this->assertNotNull($invitation->fresh()->accepted_at);
    }

    public function test_accepted_invitation_cannot_be_reused(): void
    {
        $admin = User::factory()->admin()->create();

        Invitation::create([
            'company_id' => $admin->company_id,
            'invited_by' => $admin->id,
            'name' => 'Used',
            'email' => 'used@acme.test',
            'role' => Role::Member,
            'token' => 'used-token',
            'accepted_at' => now(),
        ]);

        $this->get(route('invitations.accept', 'used-token'))->assertNotFound();
    }
}
