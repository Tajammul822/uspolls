{{-- resources/views/admin/election_poll_results/form.blade.php --}}
<form method="POST"
    action="{{ isset($result) ? route('election_polls_results.update', $result->id) : route('election_polls_results.store') }}">
    @csrf
    @isset($result)
        @method('PUT')
    @endisset


    {{-- Base Poll (read-only display) --}}
    <div class="mb-3 row">
        <label for="election_poll_id_display" class="col-sm-2 col-form-label">Base Election Poll</label>
        <div class="col-sm-10">
            <input type="text" id="election_poll_id_display" class="form-control"
                value="{{ $electionPoll->pollster_source }}" readonly>
            <input type="hidden" name="election_poll_id" value="{{ $electionPoll->id }}">
        </div>
    </div>

    <div class="mb-3 row">
        <label for="candidate_id" class="col-sm-2 col-form-label">Candidate</label>
        <div class="col-sm-10">
            <select name="candidate_id" id="candidate_id" class="form-select" required>
                <option value="">— Select Candidate —</option>
                @foreach ($candidates as $candidate)
                    <option value="{{ $candidate->id }}"
                        {{ old('candidate_id', $result->candidate_id ?? '') == $candidate->id ? 'selected' : '' }}>
                        {{ $candidate->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>


    <div class="mb-3 row">
        <label for="result_percentage" class="col-sm-2 col-form-label">Result (%)</label>
        <div class="col-sm-10">
            <input type="number" step="0.01" min="0" max="100" name="result_percentage"
                id="result_percentage" class="form-control"
                value="{{ old('result_percentage', $result->result_percentage ?? '') }}" required>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-10 offset-sm-2">
            <button type="submit" class="btn btn-primary">
                {{ isset($result) ? 'Update' : 'Create' }}
            </button>
        </div>
    </div>
</form>
