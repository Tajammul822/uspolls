<form method="POST"
    action="{{ isset($pollster) ? route('pollsters.update', $pollster->id) : route('pollsters.store') }}">
    @csrf

    {{-- If editing, spoof PUT --}}
    @isset($pollster)
        @method('PUT')
    @endisset

    {{-- ----- Name ----- --}}
    <div class="mb-3 row">
        <label for="name" class="col-sm-2 col-form-label">Name</label>
        <div class="col-sm-10">
            <input type="text" id="name" name="name" class="form-control" placeholder="Enter pollster name"
                value="{{ old('name', $pollster->name ?? '') }}" required>
        </div>
    </div>

    {{-- ----- Website URL (optional) ----- --}}
    <div class="mb-3 row">
        <label for="website_url" class="col-sm-2 col-form-label">Website URL</label>
        <div class="col-sm-10">
            <input type="url" id="website_url" name="website_url" class="form-control"
                placeholder="https://example.com" value="{{ old('website_url', $pollster->website_url ?? '') }}">
        </div>
    </div>

    {{-- ----- Status ----- --}}
    <div class="mb-3 row">
        <label for="status" class="col-sm-2 col-form-label">Status</label>
        <div class="col-sm-10">
            <select id="status" name="status" class="form-select" required>
                <option value="">Select Status</option>
                <option value="1" {{ old('status', $pollster->status ?? '') == '1' ? 'selected' : '' }}>Active
                </option>
                <option value="0" {{ old('status', $pollster->status ?? '') == '0' ? 'selected' : '' }}>Inactive
                </option>
            </select>
        </div>
    </div>

    {{-- ----- Submit/Reset Buttons ----- --}}
    <div class="row">
        <div class="col-sm-10 offset-sm-2">
            <button type="submit" class="btn btn-primary">
                {{ isset($pollster) ? 'Update Pollster' : 'Create Pollster' }}
            </button>
            <button type="reset" class="btn btn-danger">Reset</button>
        </div>
    </div>
</form>
