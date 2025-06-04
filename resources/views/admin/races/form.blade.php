
<form method="POST"
    action="{{ isset($race) ? route('races.update', $race->id) : route('races.store') }}">
    @csrf

    @isset($race)
        @method('PUT')
    @endisset

  
    <div class="mb-3 row">
        <label for="name" class="col-sm-2 col-form-label">Name</label>
        <div class="col-sm-10">
            <input type="text" id="name" name="name" class="form-control" placeholder="Enter race name"
                value="{{ old('name', $race->name ?? '') }}" required>
        </div>
    </div>

   
    <div class="mb-3 row">
        <label for="status" class="col-sm-2 col-form-label">Status</label>
        <div class="col-sm-10">
            <select id="status" name="status" class="form-select" required>
                <option value="">Select Status</option>
                <option value="1" {{ old('status', $race->status ?? '') == '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status', $race->status ?? '') == '0' ? 'selected' : '' }}>Inactive
                </option>
            </select>
        </div>
    </div>

 
    <div class="row">
        <div class="col-sm-10 offset-sm-2">
            <button type="submit" class="btn btn-primary">
                {{ isset($race) ? 'Update Race' : 'Create Race' }}
            </button>
            <button type="reset" class="btn btn-danger">Reset</button>
        </div>
    </div>
</form>
