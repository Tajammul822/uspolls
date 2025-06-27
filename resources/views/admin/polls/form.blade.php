@php
    // Guarantee this exists even on “create”
    $poll = $poll ?? null;
@endphp

<form method="POST"
    action="{{ isset($poll)
        ? route('polls.update', [
            'poll' => $poll->id,
            'race_id' => $poll->race_id,
        ])
        : route('polls.store', [
            'race_id' => request('race_id'),
        ]) }}">
    @csrf

    @isset($poll)
        @method('PUT')
    @endisset

    {{-- 1) Base Race --}}
    <div class="mb-3 row">
        <label class="col-sm-2 col-form-label">Race</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" value="{{ $race->race }}" disabled>
            <input type="hidden" name="race_id" value="{{ $race->id }}">
        </div>
    </div>

    {{-- 2) Poll Date --}}
    <div class="mb-3 row">
        <label for="poll_date" class="col-sm-2 col-form-label">Date</label>
        <div class="col-sm-10">
            <input type="date" name="poll_date" id="poll_date" class="form-control"
                value="{{ old('poll_date', optional($poll?->poll_date)?->format('Y-m-d')) }}" required>
        </div>
    </div>

    {{-- Pollster --}}
    @php
        $current = $poll ?? $race;
    @endphp

    <div class="mb-3 row">
        <label for="pollster_id" class="col-sm-2 col-form-label">Pollster</label>
        <div class="col-sm-10">
            <select name="pollster_id" id="pollster_id" class="form-select">
                <option value="">Select Pollster</option>
                @foreach ($pollsters as $pollster)
                    <option value="{{ $pollster->id }}"
                        {{ old('pollster_id', $current->pollster_id ?? '') == $pollster->id ? 'selected' : '' }}>
                        {{ $pollster->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- 4) Sample Size --}}
    <div class="mb-3 row">
        <label for="sample_size" class="col-sm-2 col-form-label">Sample Size</label>
        <div class="col-sm-10">
            <input type="number" name="sample_size" id="sample_size" class="form-control"
                value="{{ old('sample_size', $poll->sample_size ?? '') }}" required>
        </div>
    </div>


    {{-- 5) Results by Candidate --}}
    <h5 class="mt-4 mb-2">Results by Candidate</h5>
    @foreach ($race->candidates as $candidate)
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
                        optional($poll?->results->firstWhere('candidate_id', $candidate->id))?->result_percentage,
                    ) }}"
                    placeholder="0.00" >
            </div>
        </div>
    @endforeach

    {{-- 6) Submit --}}
    <div class="row mt-4">
        <div class="col-sm-10 offset-sm-2">
            <button type="submit" class="btn btn-primary">
                {{ isset($poll) ? 'Update' : 'Create' }}
            </button>
        </div>
    </div>
</form>
