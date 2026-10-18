<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(Request $request): View
    {
        $members = User::where('company_id', $request->user()->company_id)
            ->withCount('shortUrls')
            ->withSum('shortUrls', 'hits')
            ->orderBy('name')
            ->paginate(10);

        return view('team.index', compact('members'));
    }
}
