<form
    method="POST"
    action="{{ isset($approval)
        ? route('poll_approvals.update', $approval->id)
        : route('poll_approvals.store') }}"
>
    @csrf

    {{-- If editing, spoof PUT --}}
    @isset($approval)
        @method('PUT')
    @endisset

    {{-- ----- Poll ----- --}}
    <div class="mb-3 row">
        <label for="poll_id" class="col-sm-2 col-form-label">Poll</label>
        <div class="col-sm-10">
            <select
                id="poll_id"
                name="poll_id"
                class="form-select"
                required
            >
                <option value="">Select Poll</option>
                @foreach($polls as $poll)
                    <option
                        value="{{ $poll->id }}"
                        {{ old('poll_id', $approval->poll_id ?? '') == $poll->id ? 'selected' : '' }}
                    >
                        {{ $poll->title }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- ----- Subject ----- --}}
    <div class="mb-3 row">
        <label for="subject" class="col-sm-2 col-form-label">Subject</label>
        <div class="col-sm-10">
            <input
                type="text"
                id="subject"
                name="subject"
                class="form-control"
                placeholder="Enter subject (e.g. Economy, Healthcare)"
                value="{{ old('subject', $approval->subject ?? '') }}"
                required
            >
        </div>
    </div>

    {{-- ----- Approve Percentage ----- --}}
    <div class="mb-3 row">
        <label for="approve_percentage" class="col-sm-2 col-form-label">Approve (%)</label>
        <div class="col-sm-10">
            <input
                type="number"
                step="0.01"
                id="approve_percentage"
                name="approve_percentage"
                class="form-control"
                placeholder="0.00 – 100.00"
                value="{{ old('approve_percentage', $approval->approve_percentage ?? '') }}"
                required
            >
        </div>
    </div>

    {{-- ----- Disapprove Percentage ----- --}}
    <div class="mb-3 row">
        <label for="disapprove_percentage" class="col-sm-2 col-form-label">Disapprove (%)</label>
        <div class="col-sm-10">
            <input
                type="number"
                step="0.01"
                id="disapprove_percentage"
                name="disapprove_percentage"
                class="form-control"
                placeholder="0.00 – 100.00"
                value="{{ old('disapprove_percentage', $approval->disapprove_percentage ?? '') }}"
                required
            >
        </div>
    </div>

    {{-- ----- Neutral Percentage (optional) ----- --}}
    <div class="mb-3 row">
        <label for="neutral_percentage" class="col-sm-2 col-form-label">Neutral (%)</label>
        <div class="col-sm-10">
            <input
                type="number"
                step="0.01"
                id="neutral_percentage"
                name="neutral_percentage"
                class="form-control"
                placeholder="0.00 – 100.00 (optional)"
                value="{{ old('neutral_percentage', $approval->neutral_percentage ?? '') }}"
            >
        </div>
    </div>

    {{-- ----- Submit/Reset Buttons ----- --}}
    <div class="row">
        <div class="col-sm-10 offset-sm-2">
            <button type="submit" class="btn btn-primary">
                {{ isset($approval) ? 'Update Entry' : 'Create Entry' }}
            </button>
            <button type="reset" class="btn btn-danger">Reset</button>
        </div>
    </div>
</form>
