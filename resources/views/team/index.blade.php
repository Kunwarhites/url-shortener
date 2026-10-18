@extends('layouts.app')

@section('title', 'Team Members')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Team Members</span>
            <a href="{{ route('invitations.create') }}" class="btn btn-sm btn-primary">Invite</a>
        </div>
        <div class="card-body p-0">
            @include('team._table', ['members' => $members])
        </div>
        <div class="card-footer small">
            <div class="mb-2">Showing {{ $members->count() }} of total {{ $members->total() }}</div>
            {{ $members->links() }}
        </div>
    </div>
@endsection
