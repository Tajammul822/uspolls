<div class="card-grid">
    @foreach ($latestApprovals as $race)
        @foreach ($race['candidates'] as $imagePath => $candidateName)
            <div class="approval-card">
                <div class="approval-card-body">
                    <h3 class="approval-title">
                        @if ($imagePath)
                            <img src="{{ asset($imagePath) }}" alt="Candidate Image" class="rounded-circle"
                                style="width: 50px; height: 50px; background-color: #e2e2e2;">
                        @else
                            <img src="{{ asset('images/default-avatar.jpg') }}" alt="Default Image" class="rounded-circle"
                                style="width: 50px; height: 50px; background-color: #e2e2e2;">
                        @endif

                        {{ $candidateName }} Job Approval
                    </h3>
                    <div>
                        <a href="{{ route('approval.details', ['race_id' => $race['race_id']]) }}"
                            class="approval-link">
                            &rarr;
                        </a>
                    </div>

                </div>
            </div>
        @endforeach
    @endforeach
</div>

<div class="pagination-wrapper mt-4 d-flex justify-content-end">
    {!! $latestApprovals->links() !!}
</div>
