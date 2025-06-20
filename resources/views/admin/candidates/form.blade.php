<form method="POST"
    action="{{ isset($candidate) ? route('candidates.update', $candidate->id) : route('candidates.store') }}" enctype="multipart/form-data">
    @csrf

    {{-- If editing, spoof PUT --}}
    @isset($candidate)
        @method('PUT')
    @endisset

    {{-- ----- Name ----- --}}
    <div class="mb-3 row">
        <label for="name" class="col-sm-2 col-form-label">Name</label>
        <div class="col-sm-10">
            <input type="text" id="name" name="name" class="form-control" placeholder="Enter candidate name"
                value="{{ old('name', $candidate->name ?? '') }}" required>
        </div>
    </div>

    {{-- ----- Party (optional) ----- --}}
    <div class="mb-3 row">
        <label for="party" class="col-sm-2 col-form-label">Party</label>
        <div class="col-sm-10">
            <input type="text" id="party" name="party" class="form-control"
                placeholder="Enter party (or leave blank)" value="{{ old('party', $candidate->party ?? '') }}">
        </div>
    </div>

    {{-- ----- Image (optional) ----- --}}
    <div class="mb-3 row">
        <label for="image" class="col-sm-2 col-form-label">Image</label>
        <div class="col-sm-10">
            <input type="file" id="image" name="image" class="form-control"
                {{ isset($candidate) ? '' : '' }}>
        </div>
    </div>

    @if (isset($candidate) && $candidate->image)
        <div class="mb-3 row">
            <label class="col-sm-2 col-form-label">Current</label>
            <div class="col-sm-10">
                <img src="{{ asset($candidate->image) }}" width="120" alt="Candidate image">
            </div>
        </div>
    @endif


    {{-- ----- Status ----- --}}
    <div class="mb-3 row">
        <label for="status" class="col-sm-2 col-form-label">Status</label>
        <div class="col-sm-10">
            <select id="status" name="status" class="form-select" required>
                <option value="">Select Status</option>
                <option value="1" {{ old('status', $candidate->status ?? '') == '1' ? 'selected' : '' }}>Active
                </option>
                <option value="0" {{ old('status', $candidate->status ?? '') == '0' ? 'selected' : '' }}>Inactive
                </option>
            </select>
        </div>
    </div>

    {{-- ----- Submit/Reset Buttons ----- --}}
    <div class="row">
        <div class="col-sm-10 offset-sm-2">
            <button type="submit" class="btn btn-primary">
                {{ isset($candidate) ? 'Update Candidate' : 'Create Candidate' }}
            </button>
            <button type="reset" class="btn btn-danger">Reset</button>
        </div>
    </div>
</form>
