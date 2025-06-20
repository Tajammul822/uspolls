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
    </div>

    <!-- Election Fields -->
    <div id="election-fields" style="display: none;">
        <div class="mb-3 row">
            <label for="race_type" class="col-sm-2 col-form-label">Race Type</label>
            <div class="col-sm-10">
                <select name="race_type" id="race_type" class="form-select">
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
                <button type="button" class="btn btn-sm btn-secondary mt-2" onclick="addCandidate()">Add
                    Candidate</button>
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
    // Global list of all candidates for reference
    const allCandidates = [
        @foreach ($candidates as $c)
            {
                id: "{{ $c->id }}",
                name: "{{ $c->name }}",
                party: "{{ $c->party }}"
            },
        @endforeach
    ];

    function toggleRaceFields() {
        const race = document.getElementById('race').value;
        document.getElementById('election-fields').style.display = race === 'election' ? 'block' : 'none';
        document.getElementById('approval-fields').style.display = race === 'approval' ? 'block' : 'none';

        // Clear election fields when switching to approval
        if (race === 'approval') {
            document.querySelectorAll('.candidate-select').forEach(select => select.value = '');
            updateCandidateDropdowns();
        }
    }

    function addCandidate() {
        const container = document.getElementById('candidates-container');
        const index = container.children.length;
        const div = document.createElement('div');
        div.className = 'candidate-group mb-2';
        div.innerHTML = `
                <select name="candidates[${index}][candidate_id]" class="form-select mb-1 candidate-select" onchange="updateCandidateDropdowns()">
                    <option value="">Select Candidate</option>
                    @foreach ($candidates as $c)
                        <option value="{{ $c->id }}" data-party="{{ $c->party }}">{{ $c->name }}</option>
                    @endforeach
                </select>
                <input type="text" name="candidates[${index}][party]" class="form-control mb-1 party-input" placeholder="Party" readonly>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeCandidate(this)">Remove</button>
            `;
        container.appendChild(div);
        updateCandidateDropdowns();
    }

    function removeCandidate(button) {
        button.closest('.candidate-group').remove();
        updateCandidateDropdowns();
    }

    function updateCandidateDropdowns() {
        const selectedIds = Array.from(document.querySelectorAll('.candidate-select'))
            .map(select => select.value)
            .filter(Boolean);

        document.querySelectorAll('.candidate-select').forEach(select => {
            const currentId = select.value;
            // Rebuild options
            select.innerHTML = '<option value="">Select Candidate</option>';

            allCandidates.forEach(candidate => {
                if (!selectedIds.includes(candidate.id) || candidate.id === currentId) {
                    const option = document.createElement('option');
                    option.value = candidate.id;
                    option.textContent = candidate.name;
                    option.setAttribute('data-party', candidate.party);
                    if (candidate.id === currentId) option.selected = true;
                    select.appendChild(option);
                }
            });

            // Reattach event listener
            select.onchange = () => {
                updatePartyField(select);
                updateCandidateDropdowns();
            };
        });
    }

    function updatePartyField(selectElement) {
        const partyInput = selectElement.closest('.candidate-group').querySelector('.party-input');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        partyInput.value = selectedOption.getAttribute('data-party') || '';
    }

    function fillParty(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const partyInput = document.getElementById('party');

        if (selectedOption && selectedOption.hasAttribute('data-party')) {
            partyInput.value = selectedOption.getAttribute('data-party');
        } else {
            partyInput.value = '';
        }
    }

    window.addEventListener('DOMContentLoaded', function() {
        toggleRaceFields();

        // Attach updatePartyField to election selects
        document.querySelectorAll('.candidate-select').forEach(select => {
            select.onchange = () => {
                updatePartyField(select);
                updateCandidateDropdowns();
            };
            if (select.value) updatePartyField(select);
        });

        // Fill party for approval mode on load
        const approvalCandidate = document.getElementById('candidate_id');
        if (approvalCandidate && approvalCandidate.value) {
            fillParty(approvalCandidate);
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
