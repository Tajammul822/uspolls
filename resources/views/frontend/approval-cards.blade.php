<div class="card-grid">
    @foreach ($latestApprovals as $race)
        @foreach ($race['candidates'] as $name)
            <div class="approval-card">
                <div class="approval-card-body">
                    <h3 class="approval-title">
                        {{ $name }} Job Approval
                    </h3>
                    <a href="{{ route('approval.details', ['race_id' => $race['race_id']]) }}" class="approval-link">
                        &rarr;
                    </a>
                </div>
            </div>
        @endforeach
    @endforeach
</div>

<div class="pagination-wrapper mt-4 d-flex justify-content-end">
    {!! $latestApprovals->links() !!}
</div>