<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\RedirectResponse;

class RedirectController extends Controller
{
    public function __invoke(string $code): RedirectResponse
    {
        $shortUrl = ShortUrl::where('code', $code)->firstOrFail();

        $shortUrl->increment('hits');

        return redirect()->away($shortUrl->long_url);
    }
}
