<form method="POST"
    action="{{ isset($poll) ? route('polls.update', $poll->id) : route('polls.store') }}">
    @csrf

    @isset($poll)
        @method('PUT')
    @endisset

    {{-- ----- Race ----- --}}
    <div class="mb-3 row">
        <label for="race_id" class="col-sm-2 col-form-label">Race</label>
        <div class="col-sm-10">
            <select id="race_id" name="race_id" class="form-select" required>
                <option value="">Select Race</option>
                @foreach ($races as $race)
                    <option value="{{ $race->id }}"
                        {{ old('race_id', $poll->race_id ?? '') == $race->id ? 'selected' : '' }}>
                        {{ $race->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- ----- Title ----- --}}
    <div class="mb-3 row">
        <label for="title" class="col-sm-2 col-form-label">Title</label>
        <div class="col-sm-10">
            <input type="text" id="title" name="title" class="form-control" placeholder="Enter Poll Title"
                value="{{ old('title', $poll->title ?? '') }}" required>
        </div>
    </div>

    {{-- ----- Pollster ----- --}}
    <div class="mb-3 row">
        <label for="pollster_id" class="col-sm-2 col-form-label">Pollster</label>
        <div class="col-sm-10">
            <select id="pollster_id" name="pollster_id" class="form-select" required>
                <option value="">Select Pollster</option>
                @foreach ($pollsters as $pollster)
                    <option value="{{ $pollster->id }}"
                        {{ old('pollster_id', $poll->pollster_id ?? '') == $pollster->id ? 'selected' : '' }}>
                        {{ $pollster->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- ----- State (nullable) ----- --}}
    <div class="mb-3 row">
        <label for="state_id" class="col-sm-2 col-form-label">State</label>
        <div class="col-sm-10">
            <select id="state_id" name="state_id" class="form-select">
                <option value="">None</option>
                @foreach ($states as $state)
                    <option value="{{ $state->id }}"
                        {{ old('state_id', $poll->state_id ?? '') == $state->id ? 'selected' : '' }}>
                        {{ $state->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- ----- Field Date Start ----- --}}
    <div class="mb-3 row">
        <label for="field_date_start" class="col-sm-2 col-form-label">Field Date Start</label>
        <div class="col-sm-10">
            <input type="date" id="field_date_start" name="field_date_start" class="form-control"
                value="{{ old('field_date_start', $poll->field_date_start ?? '') }}" required>
        </div>
    </div>

    {{-- ----- Field Date End ----- --}}
    <div class="mb-3 row">
        <label for="field_date_end" class="col-sm-2 col-form-label">Field Date End</label>
        <div class="col-sm-10">
            <input type="date" id="field_date_end" name="field_date_end" class="form-control"
                value="{{ old('field_date_end', $poll->field_date_end ?? '') }}" required>
        </div>
    </div>

    {{-- ----- Release Date ----- --}}
    <div class="mb-3 row">
        <label for="release_date" class="col-sm-2 col-form-label">Release Date</label>
        <div class="col-sm-10">
            <input type="date" id="release_date" name="release_date" class="form-control"
                value="{{ old('release_date', $poll->release_date ?? '') }}" required>
        </div>
    </div>

    {{-- ----- Sample Size ----- --}}
    <div class="mb-3 row">
        <label for="sample_size" class="col-sm-2 col-form-label">Sample Size</label>
        <div class="col-sm-10">
            <input type="number" id="sample_size" name="sample_size" class="form-control" placeholder="e.g. 1500"
                value="{{ old('sample_size', $poll->sample_size ?? '') }}" required>
        </div>
    </div>

    {{-- ----- Margin of Error ----- --}}
    <div class="mb-3 row">
        <label for="margin_of_error" class="col-sm-2 col-form-label">Margin of Error (%)</label>
        <div class="col-sm-10">
            <input type="number" step="0.01" id="margin_of_error" name="margin_of_error" class="form-control"
                placeholder="e.g. 3.2" value="{{ old('margin_of_error', $poll->margin_of_error ?? '') }}" required>
        </div>
    </div>

    {{-- ----- Source URL ----- --}}
    <div class="mb-3 row">
        <label for="source_url" class="col-sm-2 col-form-label">Source URL</label>
        <div class="col-sm-10">
            <input type="url" id="source_url" name="source_url" class="form-control"
                placeholder="https://example.com/poll-source" value="{{ old('source_url', $poll->source_url ?? '') }}"
                required>
        </div>
    </div>

    {{-- ----- Tags (comma-separated) ----- --}}
    <div class="mb-3 row">
        <label for="tags" class="col-sm-2 col-form-label">Tags (comma-separated)</label>
        <div class="col-sm-10">
            <input type="text" id="tags" name="tags" class="form-control"
                placeholder="e.g. general, national"
                @php
// If editing and cast to array, implode; else fall back to old()
                    $tagsValue = old('tags');
                    if (!isset($tagsValue) && isset($poll->tags) && is_array($poll->tags)) {
                        $tagsValue = implode(',', $poll->tags);
                    } @endphp
                value="{{ $tagsValue ?? '' }}">
        </div>
    </div>

    {{-- ----- Status ----- --}}
    <div class="mb-3 row">
        <label for="status" class="col-sm-2 col-form-label">Status</label>
        <div class="col-sm-10">
            <select id="status" name="status" class="form-select" required>
                <option value="1" {{ old('status', $poll->status ?? '1') == '1' ? 'selected' : '' }}>
                    Active
                </option>
                <option value="0" {{ old('status', $poll->status ?? '') == '0' ? 'selected' : '' }}>
                    Inactive
                </option>
            </select>
        </div>
    </div>

    {{-- ----- Create/Update Button ----- --}}
    <div class="row">
        <div class="col-sm-10 offset-sm-2">
            <button type="submit" class="btn btn-primary">
                {{ isset($poll) ? 'Update Poll' : 'Create Poll' }}
            </button>
            <button type="reset" class="btn btn-danger">Reset</button>
        </div>
    </div>
</form>
