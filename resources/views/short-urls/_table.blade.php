<div class="table-responsive">
    <table class="table table-striped mb-0 align-middle">
        <thead>
            <tr>
                <th>Short URL</th>
                <th>Long URL</th>
                <th>Hits</th>
                @if (auth()->user()->isSuperAdmin())
                    <th>Company</th>
                @endif
                @unless (auth()->user()->isMember())
                    <th>Created By</th>
                @endunless
                <th>Created On</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($shortUrls as $shortUrl)
                <tr>
                    <td><a href="{{ $shortUrl->short_link }}" target="_blank">{{ $shortUrl->short_link }}</a></td>
                    <td class="text-truncate" style="max-width: 280px;" title="{{ $shortUrl->long_url }}">{{ $shortUrl->long_url }}</td>
                    <td>{{ $shortUrl->hits }}</td>
                    @if (auth()->user()->isSuperAdmin())
                        <td>{{ $shortUrl->company?->name }}</td>
                    @endif
                    @unless (auth()->user()->isMember())
                        <td>{{ $shortUrl->user?->name }}</td>
                    @endunless
                    <td>{{ $shortUrl->created_at->format('d M Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-3">No short URLs found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
