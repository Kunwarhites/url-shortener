<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvitationRequest;
use App\Mail\InvitationMail;
use App\Models\Company;
use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function create(Request $request): View
    {
        return view('invitations.create', [
            'roles' => $request->user()->invitableRoles(),
        ]);
    }

    public function store(StoreInvitationRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // Company + invitation are created together; if one fails, neither is saved.
        $invitation = DB::transaction(function () use ($user, $data) {
            $companyId = $user->isSuperAdmin()
                ? Company::create(['name' => $data['company_name']])->id
                : $user->company_id;

            return Invitation::create([
                'company_id' => $companyId,
                'invited_by' => $user->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => $data['role'],
                'token' => Str::random(48),
            ]);
        });

        Mail::to($invitation->email)->send(new InvitationMail($invitation));

        return redirect()
            ->route('dashboard')
            ->with('status', "Invitation sent to {$invitation->email}.")
            ->with('invite_link', route('invitations.accept', $invitation->token));
    }
}
