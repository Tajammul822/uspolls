<!-- resources/views/polls/details.blade.php -->

@extends('admin.layout')

@section('content')
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="container-xxl">
        <div class="row justify-content-center">
            <div class="col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                               <h4>{{ ucfirst($poll->poll_type) }} Poll Details</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        @if ($poll->poll_type === 'approval')
                            <form method="POST" action="{{ route('polls.approval.store') }}">
                                @csrf
                                <input type="hidden" name="poll_id" value="{{ $poll->id }}">

                                <!-- Name -->
                                <div class="mb-3 row">
                                    <label for="name" class="col-sm-2 col-form-label">Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" id="name" name="name" class="form-control"
                                            value="{{ old('name', $approval->name ?? '') }}" required>
                                    </div>
                                </div>

                                <!-- Poll Date -->
                                <div class="mb-3 row">
                                    <label for="poll_date" class="col-sm-2 col-form-label">Poll Date</label>
                                    <div class="col-sm-10">
                                        <input type="date" id="poll_date" name="poll_date" class="form-control"
                                            value="{{ old('poll_date', optional($approval)->poll_date) }}" required>
                                    </div>
                                </div>

                                <!-- Pollster -->
                                <div class="mb-3 row">
                                    <label for="pollster" class="col-sm-2 col-form-label">Pollster</label>
                                    <div class="col-sm-10">
                                        <input type="text" id="pollster" name="pollster" class="form-control"
                                            value="{{ old('pollster', $approval->pollster ?? '') }}" required>
                                    </div>
                                </div>

                                <!-- Sample Size -->
                                <div class="mb-3 row">
                                    <label for="sample_size" class="col-sm-2 col-form-label">Sample Size</label>
                                    <div class="col-sm-10">
                                        <input type="number" id="sample_size" name="sample_size" class="form-control"
                                            value="{{ old('sample_size', $approval->sample_size ?? '') }}" required>
                                    </div>
                                </div>

                                <!-- Approve Rating -->
                                <div class="mb-3 row">
                                    <label for="approve_rating" class="col-sm-2 col-form-label">Approve Rating (%)</label>
                                    <div class="col-sm-10">
                                        <input type="number" step="0.01" id="approve_rating" name="approve_rating"
                                            class="form-control"
                                            value="{{ old('approve_rating', $approval->approve_rating ?? '') }}" required>
                                    </div>
                                </div>

                                <!-- Disapprove Rating -->
                                <div class="mb-3 row">
                                    <label for="disapprove_rating" class="col-sm-2 col-form-label">Disapprove Rating
                                        (%)</label>
                                    <div class="col-sm-10">
                                        <input type="number" step="0.01" id="disapprove_rating" name="disapprove_rating"
                                            class="form-control"
                                            value="{{ old('disapprove_rating', $approval->disapprove_rating ?? '') }}"
                                            required>
                                    </div>
                                </div>

                                <!-- Buttons -->
                                <div class="mb-3 row">
                                    <div class="col-sm-10 offset-sm-2">
                                        <button type="submit" class="btn btn-primary">Save Approval</button>
                                        <button type="reset" class="btn btn-danger">Reset</button>
                                    </div>
                                </div>
                            </form>
                        @elseif($poll->poll_type === 'election')
                            <form method="POST" action="{{ route('polls.election.store') }}">
                                @csrf
                                <input type="hidden" name="poll_id" value="{{ $poll->id }}">

                                <!-- Poll Date -->
                                <div class="mb-3 row">
                                    <label for="poll_date_election" class="col-sm-2 col-form-label">Poll Date</label>
                                    <div class="col-sm-10">
                                        <input type="date" id="poll_date_election" name="poll_date" class="form-control"
                                            value="{{ old('poll_date', optional($election)->poll_date) }}" required>
                                    </div>
                                </div>

                                <!-- Pollster Source -->
                                <div class="mb-3 row">
                                    <label for="pollster_source" class="col-sm-2 col-form-label">Pollster Source</label>
                                    <div class="col-sm-10">
                                        <input type="text" id="pollster_source" name="pollster_source"
                                            class="form-control"
                                            value="{{ old('pollster_source', $election->pollster_source ?? '') }}"
                                            required>
                                    </div>
                                </div>

                                <!-- Sample Size -->
                                <div class="mb-3 row">
                                    <label for="sample_size_election" class="col-sm-2 col-form-label">Sample Size</label>
                                    <div class="col-sm-10">
                                        <input type="number" id="sample_size_election" name="sample_size"
                                            class="form-control"
                                            value="{{ old('sample_size', $election->sample_size ?? '') }}" required>
                                    </div>
                                </div>

                                <h5>Results</h5>
                                @foreach ($poll->pollCandidates as $pc)
                                    <div class="mb-3 row">
                                        <label for="result_{{ $pc->candidate_id }}"
                                            class="col-sm-2 col-form-label">{{ $pc->candidate->name }} (%)</label>
                                        <div class="col-sm-10">
                                            <input type="hidden" name="results[{{ $pc->candidate_id }}][candidate_id]"
                                                value="{{ $pc->candidate_id }}">
                                            <input type="number" step="0.01" id="result_{{ $pc->candidate_id }}"
                                                name="results[{{ $pc->candidate_id }}][result_percentage]"
                                                class="form-control"
                                                value="{{ old("results.{$pc->candidate_id}.result_percentage", optional($results->firstWhere('candidate_id', $pc->candidate_id))->result_percentage) }}"
                                                required>
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Buttons -->
                                <div class="mb-3 row">
                                    <div class="col-sm-10 offset-sm-2">
                                        <button type="submit" class="btn btn-primary">Save Election</button>
                                        <button type="reset" class="btn btn-danger">Reset</button>
                                    </div>
                                </div>
                            </form>
                        @endif    
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@endsection
