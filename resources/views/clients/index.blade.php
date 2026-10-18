@extends('layouts.app')

@section('title', 'Clients')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Clients</span>
            <a href="{{ route('invitations.create') }}" class="btn btn-sm btn-primary">Invite</a>
        </div>
        <div class="card-body p-0">
            @include('clients._table', ['companies' => $companies])
        </div>
        <div class="card-footer small">
            <div class="mb-2">Showing {{ $companies->count() }} of total {{ $companies->total() }}</div>
            {{ $companies->links() }}
        </div>
    </div>
@endsection
