<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
  
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $period = $request->query('period');

        $shortUrls = collect();
        $shortUrlsTotal = 0;

        if ($user->can('viewAny', ShortUrl::class)) {
            $query = ShortUrl::visibleTo($user)->period($period);
            $shortUrlsTotal = (clone $query)->count();
            $shortUrls = $query->with(['user', 'company'])->latest()->take(5)->get();
        }

        $companies = collect();
        $companiesTotal = 0;

        if ($user->can('view-clients')) {
            $companiesTotal = Company::count();
            $companies = Company::withCount(['users', 'shortUrls'])
                ->withSum('shortUrls', 'hits')
                ->latest()
                ->take(5)
                ->get();
        }

        $members = collect();
        $membersTotal = 0;

        if ($user->can('view-team')) {
            $membersQuery = User::where('company_id', $user->company_id);
            $membersTotal = (clone $membersQuery)->count();
            $members = $membersQuery->withCount('shortUrls')
                ->withSum('shortUrls', 'hits')
                ->orderBy('name')
                ->take(5)
                ->get();
        }

        return view('dashboard', compact(
            'period',
            'shortUrls',
            'shortUrlsTotal',
            'companies',
            'companiesTotal',
            'members',
            'membersTotal',
        ));
    }
}
