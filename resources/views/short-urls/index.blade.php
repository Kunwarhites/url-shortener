@extends('layouts.app')

@section('title', 'Short URLs')

@section('content')
    @can('create', App\Models\ShortUrl::class)
        @include('short-urls._form')
    @endcan

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Generated Short URLs</span>
            @include('short-urls._filter', ['action' => route('short-urls.index')])
        </div>
        <div class="card-body p-0">
            @include('short-urls._table', ['shortUrls' => $shortUrls])
        </div>
        <div class="card-footer small">
            <div class="mb-2">Showing {{ $shortUrls->count() }} of total {{ $shortUrls->total() }}</div>
            {{ $shortUrls->links() }}
        </div>
    </div>
@endsection
