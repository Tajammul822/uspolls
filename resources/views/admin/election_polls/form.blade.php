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

    {{-- Base Poll (read-only display) --}}
    <div class="mb-3 row">
        <label for="poll_id_display" class="col-sm-2 col-form-label">Base Poll</label>
        <div class="col-sm-10">
            {{-- Show the single poll’s type --}}
            <input type="text" id="poll_id_display" class="form-control"
                value="{{ $poll->poll_type }}" readonly>

            {{-- Hidden carries the foreign key --}}
            <input type="hidden" name="poll_id" value="{{ $poll->id }}">
        </div>
    </div>

    {{-- Poll Date --}}
    <div class="mb-3 row">
        <label for="poll_date" class="col-sm-2 col-form-label">Date</label>
        <div class="col-sm-10">
            <input type="date" name="poll_date" id="poll_date" class="form-control"
                value="{{ old('poll_date', isset($election_poll) ? $election_poll->poll_date->format('Y-m-d') : '') }}" required>
        </div>
    </div>


    {{-- Pollster / Source --}}
    <div class="mb-3 row">
        <label for="pollster_source" class="col-sm-2 col-form-label">Source</label>
        <div class="col-sm-10">
            <input type="text" name="pollster_source" id="pollster_source" class="form-control"
                value="{{ old('pollster_source', $election_poll->pollster_source ?? '') }}" required>
        </div>
    </div>

    {{-- Sample Size --}}
    <div class="mb-3 row">
        <label for="sample_size" class="col-sm-2 col-form-label">Sample Size</label>
        <div class="col-sm-10">
            <input type="number" name="sample_size" id="sample_size" class="form-control"
                value="{{ old('sample_size', $election_poll->sample_size ?? '') }}" required>
        </div>
    </div>

    {{-- Submit --}}
    <div class="row">
        <div class="col-sm-10 offset-sm-2">
            <button type="submit" class="btn btn-primary">
                {{ isset($election_poll) ? 'Update' : 'Create' }}
            </button>
        </div>
    </div>
</form>
