<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AcceptInvitationController extends Controller
{
    public function show(string $token): View
    {
        $invitation = Invitation::pending()->with('company')->where('token', $token)->firstOrFail();

        return view('invitations.accept', compact('invitation'));
    }

    public function store(Request $request, string $token): RedirectResponse
    {
        $invitation = Invitation::pending()->where('token', $token)->firstOrFail();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

       
        $user = DB::transaction(function () use ($invitation, $data) {
            $user = User::create([
                'company_id' => $invitation->company_id,
                'name' => $data['name'],
                'email' => $invitation->email,
                'role' => $invitation->role,
                'password' => $data['password'],
            ]);

            $invitation->update(['accepted_at' => now()]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('status', 'Welcome! Your account is ready.');
    }
}
