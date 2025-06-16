<form method="POST"
    action="{{ isset($pollster) ? route('pollsters.update', $pollster->id) : route('pollsters.store') }}" enctype="multipart/form-data">
    @csrf

    {{-- If editing, spoof PUT --}}
    @isset($pollster)
        @method('PUT')
    @endisset

    {{-- Name --}}
    <div class="mb-3 row">
        <label for="name" class="col-sm-2 col-form-label">Name</label>
        <div class="col-sm-10">
            <input type="text" id="name" name="name" class="form-control" placeholder="Pollster name"
                value="{{ old('name', $pollster->name ?? '') }}" required>
        </div>
    </div>

    {{-- Rank --}}
    <div class="mb-3 row">
        <label for="rank" class="col-sm-2 col-form-label">Rank</label>
        <div class="col-sm-10">
            <select id="rank" name="rank" class="form-select" required>
                <option value="">Select rank</option>
                @foreach (['A+', 'A', 'B', 'C'] as $r)
                    <option value="{{ $r }}"
                        {{ old('rank', $pollster->rank ?? '') === $r ? 'selected' : '' }}>{{ $r }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Description --}}
    <div class="mb-3 row">
        <label for="description" class="col-sm-2 col-form-label">Description</label>
        <div class="col-sm-10">
            <textarea id="description" name="description" class="form-control" rows="3" placeholder="Optional description">{{ old('description', $pollster->description ?? '') }}</textarea>
        </div>
    </div>

    {{-- Website --}}
    <div class="mb-3 row">
        <label for="website" class="col-sm-2 col-form-label">Website</label>
        <div class="col-sm-10">
            <input type="url" id="website" name="website" class="form-control" placeholder="https://example.com"
                value="{{ old('website', $pollster->website ?? '') }}">
        </div>
    </div>

    {{-- Submit --}}
    <div class="row">
        <div class="col-sm-10 offset-sm-2">
            <button type="submit" class="btn btn-primary">
                {{ isset($pollster) ? 'Update' : 'Create' }}
            </button>
            <button type="reset" class="btn btn-danger">Reset</button>
        </div>
    </div>
</form>
