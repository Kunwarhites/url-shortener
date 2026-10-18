<div class="table-responsive">
    <table class="table table-striped mb-0 align-middle">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Total Generated URLs</th>
                <th>Total URL Hits</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($members as $member)
                <tr>
                    <td>{{ $member->name }}</td>
                    <td>{{ $member->email }}</td>
                    <td>{{ $member->role->label() }}</td>
                    <td>{{ $member->short_urls_count }}</td>
                    <td>{{ (int) $member->short_urls_sum_hits }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-3">No team members yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
