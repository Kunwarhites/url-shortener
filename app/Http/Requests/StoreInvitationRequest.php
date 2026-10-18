<?php

namespace App\Http\Requests;

use App\Enums\Role;
use App\Models\Invitation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Invitation::class);
    }

    public function rules(): array
    {
        $user = $this->user();

        $allowedRoles = array_map(fn (Role $role) => $role->value, $user->invitableRoles());

        return [
            'company_name' => [Rule::requiredIf($user->isSuperAdmin()), 'nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
                Rule::unique('invitations', 'email')->whereNull('accepted_at'),
            ],
            'role' => ['required', Rule::in($allowedRoles)],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already registered or has a pending invitation.',
            'role.in' => 'You are not allowed to invite a user with this role.',
        ];
    }
}
