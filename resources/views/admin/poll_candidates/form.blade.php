{{-- resources/views/poll_candidates/form.blade.php --}}
<form method="POST"
    action="{{ isset($entry) ? route('poll_candidates.update', $entry->id) : route('poll_candidates.store') }}">
    @csrf

    {{-- If editing, spoof PUT --}}
    @isset($entry)
        @method('PUT')
    @endisset

    {{-- ----- Poll ----- --}}
    <div class="mb-3 row">
        <label for="poll_id" class="col-sm-2 col-form-label">Poll</label>
        <div class="col-sm-10">
            <select id="poll_id" name="poll_id" class="form-select" required>
                <option value="">Select Poll</option>
                @foreach ($polls as $poll)
                    <option value="{{ $poll->id }}"
                        {{ old('poll_id', $entry->poll_id ?? '') == $poll->id ? 'selected' : '' }}>
                        {{ $poll->title }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- ----- Candidate ----- --}}
    <div class="mb-3 row">
        <label for="candidate_id" class="col-sm-2 col-form-label">Candidate</label>
        <div class="col-sm-10">
            <select id="candidate_id" name="candidate_id" class="form-select" required>
                <option value="">Select Candidate</option>
                @foreach ($candidates as $candidate)
                    <option value="{{ $candidate->id }}"
                        {{ old('candidate_id', $entry->candidate_id ?? '') == $candidate->id ? 'selected' : '' }}>
                        {{ $candidate->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- ----- Support Percentage ----- --}}
    <div class="mb-3 row">
        <label for="support_percentage" class="col-sm-2 col-form-label">Support (%)</label>
        <div class="col-sm-10">
            <input type="number" step="0.01" id="support_percentage" name="support_percentage" class="form-control"
                placeholder="e.g. 45.50" value="{{ old('support_percentage', $entry->support_percentage ?? '') }}"
                required>
        </div>
    </div>

    {{-- ----- Submit/Reset Buttons ----- --}}
    <div class="row">
        <div class="col-sm-10 offset-sm-2">
            <button type="submit" class="btn btn-primary">
                {{ isset($entry) ? 'Update Entry' : 'Create Entry' }}
            </button>
            <button type="reset" class="btn btn-danger">Cancel</button>
        </div>
    </div>
</form>
