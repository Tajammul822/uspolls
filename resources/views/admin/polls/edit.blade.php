@extends('admin.layout')

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
    @endif

    <div class="container-xxl">
        <div class="row justify-content-center">
            <div class="col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Edit Polls</h4>
                            </div>
                        </div> 
                    </div>
                    <div class="card-body pt-0">
                        @include('admin.polls.form', [
                            'poll'      => $poll,       {{-- existing model --}}
                            'races'     => $races,
                            'pollsters' => $pollsters,
                            'states'    => $states
                        ])
                </div>
            </div>
        </div> 
    </div>
</div>
@endsection