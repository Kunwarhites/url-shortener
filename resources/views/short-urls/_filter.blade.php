<form method="GET" action="{{ $action }}" class="d-flex gap-2">
    <select name="period" class="form-select form-select-sm" onchange="this.form.submit()">
        <option value="">All Time</option>
        @foreach (App\Models\ShortUrl::PERIODS as $value => $label)
            <option value="{{ $value }}" @selected(request('period') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <a href="{{ route('short-urls.export', ['period' => request('period')]) }}" class="btn btn-sm btn-success text-nowrap">Download</a>
</form>
