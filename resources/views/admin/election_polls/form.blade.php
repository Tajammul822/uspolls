@php
    // Guarantee this exists even on “create”
    $election_poll = $election_poll ?? null;
@endphp

<form method="POST"
    action="{{ isset($election_poll)
        ? route('election_polls.update', [
            'election_poll' => $election_poll->id,
            'poll_id' => $election_poll->poll_id,
        ])
        : route('election_polls.store', [
            'poll_id' => request('poll_id'),
        ]) }}">
    @csrf

    @isset($election_poll)
        @method('PUT')
    @endisset

    {{-- 1) Base Poll --}}
    <div class="mb-3 row">
        <label class="col-sm-2 col-form-label">Base Poll</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" value="{{ $poll->poll_type }}" disabled>
            <input type="hidden" name="poll_id" value="{{ $poll->id }}">
        </div>
    </div>

    {{-- 2) Poll Date --}}
    <div class="mb-3 row">
        <label for="poll_date" class="col-sm-2 col-form-label">Date</label>
        <div class="col-sm-10">
            <input type="date" name="poll_date" id="poll_date" class="form-control"
                value="{{ old('poll_date', optional($election_poll?->poll_date)?->format('Y-m-d')) }}" required>
        </div>
    </div>

    {{-- 3) Source --}}
    <div class="mb-3 row">
        <label for="pollster_source" class="col-sm-2 col-form-label">Source</label>
        <div class="col-sm-10">
            <input type="text" name="pollster_source" id="pollster_source" class="form-control"
                value="{{ old('pollster_source', $election_poll->pollster_source ?? '') }}" required>
        </div>
    </div>

    {{-- 4) Sample Size --}}
    <div class="mb-3 row">
        <label for="sample_size" class="col-sm-2 col-form-label">Sample Size</label>
        <div class="col-sm-10">
            <input type="number" name="sample_size" id="sample_size" class="form-control"
                value="{{ old('sample_size', $election_poll->sample_size ?? '') }}" required>
        </div>
    </div>

    {{-- 5) Results by Candidate --}}
    <h5 class="mt-4 mb-2">Results by Candidate</h5>
    @foreach ($poll->candidates as $candidate)
        <div class="mb-3 row">
            <div class="col-sm-4">
                {{-- Hidden ID so we know which candidate --}}
                <input type="hidden" name="candidate_ids[]" value="{{ $candidate->id }}">
                {{-- Display the name read-only --}}
                <input type="text" class="form-control-plaintext" value="{{ $candidate->name }}" readonly>
            </div>

            <label for="result_{{ $candidate->id }}" class="col-sm-2 col-form-label">Result (%)</label>
            <div class="col-sm-4">
                <input type="number" step="0.01" min="0" max="100" name="results[{{ $candidate->id }}]"
                    id="result_{{ $candidate->id }}" class="form-control"
                    value="{{ old(
                        'results.' . $candidate->id,
                        optional($election_poll?->results->firstWhere('candidate_id', $candidate->id))?->result_percentage,
                    ) }}"
                    placeholder="0.00" required>
            </div>
        </div>
    @endforeach

    {{-- 6) Submit --}}
    <div class="row mt-4">
        <div class="col-sm-10 offset-sm-2">
            <button type="submit" class="btn btn-primary">
                {{ isset($election_poll) ? 'Update' : 'Create' }}
            </button>
        </div>
    </div>
</form>
