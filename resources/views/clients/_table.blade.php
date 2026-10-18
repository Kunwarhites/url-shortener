<div class="table-responsive">
    <table class="table table-striped mb-0 align-middle">
        <thead>
            <tr>
                <th>Client Name</th>
                <th>Users</th>
                <th>Total Generated URLs</th>
                <th>Total URL Hits</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($companies as $company)
                <tr>
                    <td>{{ $company->name }}</td>
                    <td>{{ $company->users_count }}</td>
                    <td>{{ $company->short_urls_count }}</td>
                    <td>{{ (int) $company->short_urls_sum_hits }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-3">No clients yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
