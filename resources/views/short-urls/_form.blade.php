<div class="card mb-4">
    <div class="card-header">Generate Short URL</div>
    <div class="card-body">
        <form method="POST" action="{{ route('short-urls.store') }}" class="row g-2">
            @csrf
            <div class="col-md-10">
                <input type="url" name="long_url" value="{{ old('long_url') }}"
                       class="form-control @error('long_url') is-invalid @enderror"
                       placeholder="e.g. https://example.com/some/very/long/page" required>
                @error('long_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary">Generate</button>
            </div>
        </form>
    </div>
</div>
