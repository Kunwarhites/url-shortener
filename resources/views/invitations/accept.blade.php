@extends('layouts.app')

@section('title', 'Accept Invitation')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">Join {{ $invitation->company->name }}</div>
                <div class="card-body">
                    <p class="text-muted small">
                        You have been invited as <strong>{{ $invitation->role->label() }}</strong>
                        using <strong>{{ $invitation->email }}</strong>. Set your password to continue.
                    </p>

                    <form method="POST" action="{{ route('invitations.accept.store', $invitation->token) }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $invitation->name) }}"
                                   class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password"
                                   class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Create Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
