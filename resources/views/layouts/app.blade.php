<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') | {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ auth()->check() ? route('dashboard') : route('login') }}">{{ config('app.name') }}</a>

        @auth
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    @can('viewAny', App\Models\ShortUrl::class)
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('short-urls.*') ? 'active' : '' }}" href="{{ route('short-urls.index') }}">Short URLs</a>
                        </li>
                    @endcan
                    @can('view-clients')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}" href="{{ route('clients.index') }}">Clients</a>
                        </li>
                    @endcan
                    @can('view-team')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('team.*') ? 'active' : '' }}" href="{{ route('team.index') }}">Team Members</a>
                        </li>
                    @endcan
                </ul>

                <span class="navbar-text me-3">
                    {{ auth()->user()->name }}
                    <span class="badge bg-secondary">{{ auth()->user()->role->label() }}</span>
                    @if (auth()->user()->company)
                        &middot; {{ auth()->user()->company->name }}
                    @endif
                </span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                </form>
            </div>
        @endauth
    </div>
</nav>

<main class="container pb-5">
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
            @if (session('invite_link'))
                <div class="small mt-1">Invite link: <a href="{{ session('invite_link') }}">{{ session('invite_link') }}</a></div>
            @endif
        </div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
