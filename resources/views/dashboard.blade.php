@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @can('create', App\Models\ShortUrl::class)
        @include('short-urls._form')
    @endcan

    @can('view-clients')
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Clients</span>
                <a href="{{ route('invitations.create') }}" class="btn btn-sm btn-primary">Invite</a>
            </div>
            <div class="card-body p-0">
                @include('clients._table', ['companies' => $companies])
            </div>
            <div class="card-footer small d-flex justify-content-between align-items-center">
                <span>Showing {{ $companies->count() }} of total {{ $companiesTotal }}</span>
                <a href="{{ route('clients.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
        </div>
    @endcan

    @can('viewAny', App\Models\ShortUrl::class)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Generated Short URLs</span>
                @include('short-urls._filter', ['action' => route('dashboard')])
            </div>
            <div class="card-body p-0">
                @include('short-urls._table', ['shortUrls' => $shortUrls])
            </div>
            <div class="card-footer small d-flex justify-content-between align-items-center">
                <span>Showing {{ $shortUrls->count() }} of total {{ $shortUrlsTotal }}</span>
                <a href="{{ route('short-urls.index', ['period' => $period]) }}" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
        </div>
    @endcan

    @can('view-team')
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Team Members</span>
                <a href="{{ route('invitations.create') }}" class="btn btn-sm btn-primary">Invite</a>
            </div>
            <div class="card-body p-0">
                @include('team._table', ['members' => $members])
            </div>
            <div class="card-footer small d-flex justify-content-between align-items-center">
                <span>Showing {{ $members->count() }} of total {{ $membersTotal }}</span>
                <a href="{{ route('team.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
        </div>
    @endcan

    @cannot('viewAny', App\Models\ShortUrl::class)
        @cannot('view-clients')
            <div class="alert alert-info">Your role does not have any actions available yet.</div>
        @endcannot
    @endcannot
@endsection
