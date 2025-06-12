<form method="POST"
    action="{{ isset($approval) ? route('poll_approvals.update', ['poll_approval' => $approval->id, 'poll_id' => $approval->poll_id]) : route('poll_approvals.store', ['poll_id' => request('poll_id')]) }}">
    @csrf

    @isset($approval)
        @method('PUT')
    @endisset



    {{-- Base Poll (read-only) --}}
    <div class="mb-3 row">
        <label class="col-sm-2 col-form-label">Poll</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" value="{{ $poll->poll_type }}" readonly>
            <input type="hidden" name="poll_id" value="{{ $poll->id }}">
        </div>
    </div>

    @php
        // grab the single candidate from the pivot relation
        $candidate = $poll->candidates()->first();
    @endphp

    <div class="mb-3 row">
        <label class="col-sm-2 col-form-label">Official Name</label>
        <div class="col-sm-10">
            @if ($poll->poll_type === 'approval' && $candidate)
                {{-- read-only display of the only candidate --}}
                <input type="text" name="name" class="form-control" value="{{ $candidate->name }}"  readonly>
            @endif
        </div>
    </div>

    {{-- Date --}}
    <div class="mb-3 row">
        <label for="poll_date" class="col-sm-2 col-form-label">Date</label>
        <div class="col-sm-10">
            <input type="date" name="poll_date" id="poll_date" class="form-control"
                value="{{ old('poll_date', isset($approval) ? $approval->poll_date->format('Y-m-d') : '') }}" required>
        </div>
    </div>

    {{-- Pollster --}}
    <div class="mb-3 row">
        <label for="pollster" class="col-sm-2 col-form-label">Pollster</label>
        <div class="col-sm-10">
            <input type="text" name="pollster" id="pollster" class="form-control"
                value="{{ old('pollster', $approval->pollster ?? '') }}" required>
        </div>
    </div>

    {{-- Sample Size --}}
    <div class="mb-3 row">
        <label for="sample_size" class="col-sm-2 col-form-label">Sample Size</label>
        <div class="col-sm-10">
            <input type="number" name="sample_size" id="sample_size" class="form-control"
                value="{{ old('sample_size', $approval->sample_size ?? '') }}" required>
        </div>
    </div>

    {{-- Approve Rating --}}
    <div class="mb-3 row">
        <label for="approve_rating" class="col-sm-2 col-form-label">Approve Rating (%)</label>
        <div class="col-sm-10">
            <input type="number" step="0.01" name="approve_rating" id="approve_rating" class="form-control"
                value="{{ old('approve_rating', $approval->approve_rating ?? '') }}" required>
        </div>
    </div>

    {{-- Disapprove Rating --}}
    <div class="mb-3 row">
        <label for="disapprove_rating" class="col-sm-2 col-form-label">Disapprove Rating (%)</label>
        <div class="col-sm-10">
            <input type="number" step="0.01" name="disapprove_rating" id="disapprove_rating" class="form-control"
                value="{{ old('disapprove_rating', $approval->disapprove_rating ?? '') }}" required>
        </div>
    </div>

    {{-- Submit --}}
    <div class="row">
        <div class="col-sm-10 offset-sm-2">
            <button type="submit" class="btn btn-primary">
                {{ isset($approval) ? 'Update' : 'Create' }}
            </button>
            <button type="reset" class="btn btn-danger">Reset</button>
        </div>
    </div>
</form>
