<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(): View
    {
        $companies = Company::withCount(['users', 'shortUrls'])
            ->withSum('shortUrls', 'hits')
            ->latest()
            ->paginate(10);

        return view('clients.index', compact('companies'));
    }
}
