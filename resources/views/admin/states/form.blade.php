<form method="POST"
    action="{{ isset($state) ? route('states.update', $state->id) : route('states.store') }}">
    @csrf

    {{-- If editing, spoof PUT --}}
    @isset($state)
        @method('PUT')
    @endisset

    {{-- ----- Name ----- --}}
    <div class="mb-3 row">
        <label for="name" class="col-sm-2 col-form-label">Name</label>
        <div class="col-sm-10">
            <input type="text" id="name" name="name" class="form-control"
                placeholder="Enter state name, e.g. California" value="{{ old('name', $state->name ?? '') }}" required>
        </div>
    </div>

    {{-- ----- Abbreviation (2 chars) ----- --}}
    <div class="mb-3 row">
        <label for="abbreviation" class="col-sm-2 col-form-label">Abbreviation</label>
        <div class="col-sm-10">
            <input type="text" id="abbreviation" name="abbreviation" class="form-control" placeholder="e.g. CA"
                maxlength="2" value="{{ old('abbreviation', $state->abbreviation ?? '') }}" required>
        </div>
    </div>

    {{-- ----- Submit/Reset Buttons ----- --}}
    <div class="row">
        <div class="col-sm-10 offset-sm-2">
            <button type="submit" class="btn btn-primary">
                {{ isset($state) ? 'Update State' : 'Create State' }}
            </button>
            <button type="reset" class="btn btn-danger">Reset</button>
        </div>
    </div>
</form>
