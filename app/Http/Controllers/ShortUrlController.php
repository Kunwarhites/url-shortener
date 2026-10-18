<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShortUrlRequest;
use App\Models\ShortUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ShortUrlController extends Controller
{
    public function index(Request $request): View
    {
        $period = $request->query('period');

        $shortUrls = ShortUrl::visibleTo($request->user())
            ->period($period)
            ->with(['user', 'company'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('short-urls.index', compact('shortUrls', 'period'));
    }

    public function store(StoreShortUrlRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->shortUrls()->create([
            'company_id' => $user->company_id,
            'long_url' => $request->validated('long_url'),
            'code' => ShortUrl::generateCode(),
        ]);

        return back()->with('status', 'Short URL generated successfully.');
    }

    public function export(Request $request): StreamedResponse
    {
        $user = $request->user();
        $period = $request->query('period');

        $query = ShortUrl::visibleTo($user)
            ->period($period)
            ->with(['user', 'company'])
            ->latest('id');

        $fileName = 'short-urls-'.($period ?: 'all').'-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');

            fputcsv($out, ['Short URL', 'Long URL', 'Hits', 'Company', 'Created By', 'Created On']);

            foreach ($query->lazy() as $shortUrl) {
                fputcsv($out, [
                    $shortUrl->short_link,
                    $shortUrl->long_url,
                    $shortUrl->hits,
                    $shortUrl->company?->name,
                    $shortUrl->user?->name,
                    $shortUrl->created_at->format('d M Y'),
                ]);
            }

            fclose($out);
        }, $fileName, ['Content-Type' => 'text/csv']);
    }
}
