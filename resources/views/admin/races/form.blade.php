
{{-- resources/views/races/form.blade.php --}} 
<form method="POST" action="{{ isset($race) ? route('races.update', $race->id) : route('races.store') }}">
    @csrf
    @isset($race)
        @method('PUT')
    @endisset

    <!-- Race -->
    <div class="mb-3 row">
        <label for="race" class="col-sm-2 col-form-label">Race</label>
        <div class="col-sm-10">
            <select name="race" id="race" class="form-select" required onchange="toggleRaceFields()">
                <option value="">Select Race</option>
                <option value="election" {{ old('race', $race->race ?? '') == 'election' ? 'selected' : '' }}>
                    Election
                </option>
                <option value="approval" {{ old('race', $race->race ?? '') == 'approval' ? 'selected' : '' }}>
                    Approval
                </option>
            </select>
        </div>
    </div>

    <!-- Approval Fields -->
    <div id="approval-fields" style="display: none;">
        <div class="mb-3 row">
            <label for="candidate_id" class="col-sm-2 col-form-label">Candidate</label>
            <div class="col-sm-10">
                <select id="candidate_id" name="candidate_id" class="form-select" onchange="fillParty(this)">
                    <option value="">Select Candidate</option>
                    @foreach ($candidates as $c)
                        <option value="{{ $c->id }}" data-party="{{ $c->party }}"
                            {{ old('candidate_id', optional($approvalCandidate)->candidate_id) == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3 row">
            <label for="party" class="col-sm-2 col-form-label">Party</label>
            <div class="col-sm-10">
                <input type="text" id="party" name="party" class="form-control"
                    value="{{ old('party', $approvalCandidate?->candidate?->party) }}" readonly>
            </div>
        </div>

        <div class="mb-3 row">
            <label for="is_featured_approval" class="col-sm-2 col-form-label">Is_featured</label>
            <div class="col-sm-10">
                <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured_approval" value="1"
                    {{ old('is_featured', $race->is_featured ?? false) ? 'checked' : '' }}>
            </div>
        </div>
    </div>

    <!-- Election Fields -->
    <div id="election-fields" style="display: none;">
        <div class="mb-3 row">
            <label for="race_type" class="col-sm-2 col-form-label">Race Type</label>
            <div class="col-sm-10">
                <select name="race_type" id="race_type" class="form-select" onchange="toggleDistrictField()">
                    <option value="">None</option>
                    @foreach (['president', 'senate', 'house', 'governor', 'other'] as $type)
                        <option value="{{ $type }}"
                            {{ old('race_type', $race->race_type ?? '') == $type ? 'selected' : '' }}>
                            {{ ucfirst($type) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3 row">
            <label for="election_round" class="col-sm-2 col-form-label">Election Round</label>
            <div class="col-sm-10">
                <select name="election_round" id="election_round" class="form-select">
                    <option value="">None</option>
                    <option value="primary"
                        {{ old('election_round', $race->election_round ?? '') == 'primary' ? 'selected' : '' }}>
                        Primary
                    </option>
                    <option value="general"
                        {{ old('election_round', $race->election_round ?? '') == 'general' ? 'selected' : '' }}>
                        General
                    </option>
                </select>
            </div>
        </div>

        <div class="mb-3 row">
            <label for="state_id" class="col-sm-2 col-form-label">State</label>
            <div class="col-sm-10">
                <select name="state_id" id="state_id" class="form-select">
                    <option value="">Select State</option>
                    <option value="general"
                        {{ old('state_id', (string) ($race->state_id ?? '')) === 'general' ? 'selected' : '' }}>
                        General
                    </option>
                    @foreach ($states as $state)
                        <option value="{{ $state->id }}"
                            {{ old('state_id', $race->state_id ?? '') == $state->id ? 'selected' : '' }}>
                            {{ $state->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- District: shown only when race_type === 'house' -->
        <div class="mb-3 row" id="district-field">
            <label for="district" class="col-sm-2 col-form-label">District</label>
            <div class="col-sm-10">
                <input type="number" name="district" id="district" class="form-control"
                    value="{{ old('district', $race->district ?? '') }}">
            </div>
        </div>

        <div class="mb-3 row">
            <label for="is_featured_election" class="col-sm-2 col-form-label">Is_featured</label>
            <div class="col-sm-10">
                <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured_election" value="1"
                    {{ old('is_featured', $race->is_featured ?? false) ? 'checked' : '' }}>
            </div>
        </div>

        <div class="mb-3 row">
            <label class="col-sm-2 col-form-label">Candidates</label>
            <div class="col-sm-10">
                <div id="candidates-container">
                    @php
                        $candidatesData = old('candidates', $race->candidates ?? []);
                    @endphp
                    @if (count($candidatesData) > 0)
                        @foreach ($candidatesData as $index => $cd)
                            <div class="candidate-group mb-2">
                                <select name="candidates[{{ $index }}][candidate_id]"
                                    class="form-select mb-1 candidate-select" onchange="updateCandidateDropdowns()">
                                    <option value="">Select Candidate</option>
                                    @foreach ($candidates as $c)
                                        <option value="{{ $c->id }}" data-party="{{ $c->party }}"
                                            {{ old("candidates.$index.candidate_id", $cd['candidate_id'] ?? '') == $c->id ? 'selected' : '' }}>
                                            {{ $c->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="text" name="candidates[{{ $index }}][party]"
                                    class="form-control mb-1 party-input" placeholder="Party"
                                    value="{{ old("candidates.$index.party", $cd['party'] ?? '') }}" readonly>
                                @if ($index > 0)
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="removeCandidate(this)">Remove</button>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="candidate-group mb-2">
                            <select name="candidates[0][candidate_id]" class="form-select mb-1 candidate-select"
                                onchange="updateCandidateDropdowns()">
                                <option value="">Select Candidate</option>
                                @foreach ($candidates as $c)
                                    <option value="{{ $c->id }}" data-party="{{ $c->party }}">
                                        {{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="text" name="candidates[0][party]" class="form-control mb-1 party-input"
                                placeholder="Party" readonly>
                        </div>
                    @endif
                </div>
                <button type="button" class="btn btn-sm btn-secondary mt-2" onclick="addCandidate()">Add Candidate</button>
            </div>
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

<!-- JavaScript -->
<script>
    const allCandidates = [
        @foreach ($candidates as $c)
            { id: "{{ $c->id }}", name: "{{ $c->name }}", party: "{{ $c->party }}" },
        @endforeach
    ];

    function toggleRaceFields() {
        const race = document.getElementById('race').value;
        document.getElementById('election-fields').style.display = race === 'election' ? 'block' : 'none';
        document.getElementById('approval-fields').style.display = race === 'approval' ? 'block' : 'none';
        if (race === 'approval') {
            document.querySelectorAll('.candidate-select').forEach(s => s.value = '');
            updateCandidateDropdowns();
        }
        toggleDistrictField();
    }

    function toggleDistrictField() {
        const type = document.getElementById('race_type').value;
        const ctr = document.getElementById('district-field');
        if (document.getElementById('race').value === 'election' && type === 'house') {
            ctr.style.display = 'flex';
        } else {
            ctr.style.display = 'none';
        }
    }

    function addCandidate() {
        const container = document.getElementById('candidates-container');
        const idx = container.children.length;
        const div = document.createElement('div');
        div.className = 'candidate-group mb-2';
        div.innerHTML = `
            <select name="candidates[${idx}][candidate_id]" class="form-select mb-1 candidate-select" onchange="updateCandidateDropdowns()">
                <option value="">Select Candidate</option>
                @foreach ($candidates as $c)
                    <option value="{{ $c->id }}" data-party="{{ $c->party }}">{{ $c->name }}</option>
                @endforeach
            </select>
            <input type="text" name="candidates[${idx}][party]" class="form-control mb-1 party-input" placeholder="Party" readonly>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeCandidate(this)">Remove</button>
        `;
        container.appendChild(div);
        updateCandidateDropdowns();
    }

    function removeCandidate(btn) {
        btn.closest('.candidate-group').remove();
        updateCandidateDropdowns();
    }

    function updateCandidateDropdowns() {
        const selected = Array.from(document.querySelectorAll('.candidate-select'))
            .map(s => s.value).filter(Boolean);
        document.querySelectorAll('.candidate-select').forEach(select => {
            const curr = select.value;
            select.innerHTML = '<option value="">Select Candidate</option>';
            allCandidates.forEach(c => {
                if (!selected.includes(c.id) || c.id === curr) {
                    const o = document.createElement('option');
                    o.value = c.id; o.textContent = c.name;
                    o.setAttribute('data-party', c.party);
                    if (c.id === curr) o.selected = true;
                    select.appendChild(o);
                }
            });
            select.onchange = () => {
                updatePartyField(select);
                updateCandidateDropdowns();
            };
        });
    }

    function updatePartyField(sel) {
        const inp = sel.closest('.candidate-group').querySelector('.party-input');
        const o = sel.options[sel.selectedIndex];
        inp.value = o?.getAttribute('data-party') || '';
    }

    function fillParty(sel) {
        const o = sel.options[sel.selectedIndex];
        document.getElementById('party').value = o?.getAttribute('data-party') || '';
    }

    window.addEventListener('DOMContentLoaded', () => {
        toggleRaceFields();
        document.getElementById('race_type').addEventListener('change', toggleDistrictField);
        document.querySelectorAll('.candidate-select').forEach(s => {
            s.onchange = () => { updatePartyField(s); updateCandidateDropdowns(); };
            if (s.value) updatePartyField(s);
        });
        const ac = document.getElementById('candidate_id');
        if (ac) {
            ac.onchange = () => fillParty(ac);
            if (ac.value) fillParty(ac);
        }
    });
</script>

<style>
    .candidate-group {
        border-left: 3px solid #0d6efd;
        padding-left: 10px;
        margin-bottom: 10px;
    }
</style>
