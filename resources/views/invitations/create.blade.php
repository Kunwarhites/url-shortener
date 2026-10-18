@extends('layouts.app')

@section('title', auth()->user()->isSuperAdmin() ? 'Invite New Client' : 'Invite Team Member')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    {{ auth()->user()->isSuperAdmin() ? 'Invite New Client' : 'Invite New Team Member' }}
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('invitations.store') }}">
                        @csrf

                        @if (auth()->user()->isSuperAdmin())
                            <div class="mb-3">
                                <label for="company_name" class="form-label">Client (Company) Name</label>
                                <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}"
                                       class="form-control @error('company_name') is-invalid @enderror" required>
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ auth()->user()->isSuperAdmin() ? 'Admin Name' : 'Name' }}</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                   class="form-control @error('email') is-invalid @enderror" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->value }}" @selected(old('role') === $role->value)>{{ $role->label() }}</option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Send Invitation</button>
                        <a href="{{ route('dashboard') }}" class="btn btn-link">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
