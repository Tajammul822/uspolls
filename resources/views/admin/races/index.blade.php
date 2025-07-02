@extends('admin.layout')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @php
        // Party → background‑color map
        $colorMap = [
            'Democratic Party' => 'blue',
            'Republican Party' => 'red',
            'Libertarian Party' => 'gold',
            'Green Party' => 'green',
            'Constitution Party' => 'darkred',
            'Independent' => 'gray',
        ];
    @endphp

    <style>
        .primary-party-row,
        .primary-party-row td,
        .primary-party-row td * {
            color: white !important;
        }
    </style>

    <div class="container-xxl">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Race</h4>
                                <a href="{{ route('races.create') }}">
                                    <button type="button" class="btn btn-info">Create Race</button>
                                </a>
                            </div><!--end col-->
                        </div> <!--end row-->
                    </div><!--end card-header-->
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table datatable" id="datatable_2">
                                <thead class="">
                                    <tr>
                                        <th>Race</th>
                                        <th>Race Type</th>
                                        <th>Election Round</th>
                                        <th>State</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($races as $race)
                                        @php
                                            $isPrimary = strtolower($race->election_round ?? '') === 'primary';
                                            $firstPivot = $race->raceCandidates->first();
                                            $party =
                                                $firstPivot && $firstPivot->candidate
                                                    ? $firstPivot->candidate->party
                                                    : null;

                                            $bgColor =
                                                $isPrimary && isset($colorMap[$party]) ? $colorMap[$party] : null;

                                            $style = $bgColor
                                                ? "background-color: {$bgColor}; color: white !important;"
                                                : '';
                                        @endphp

                                        <tr @if ($isPrimary) class="primary-party-row" @endif
                                            style="{{ $style }}">
                                            <td>{{ ucfirst($race->race) }}</td>
                                            <td>{{ ucfirst($race->race_type ?? 'N/A') }}</td>
                                            <td>{{ ucfirst($race->election_round ?? 'N/A') }}</td>
                                            <td>{{ $race->state->name ?? 'N/A' }}</td>
                                            <td>
                                                @if ($race->status)
                                                    <span class="badge bg-primary">Active</span>
                                                @else
                                                    <span class="badge bg-warning">Inactive</span>
                                                @endif
                                            </td>

                                            <td class="d-flex justify-evenly-space align-items-center" style="gap: 5px;">
                                                <a href="{{ route('races.edit', $race->id) }}"
                                                    class="btn btn-primary btn-sm float-left mr-1"
                                                    style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                                    title="edit" data-placement="bottom">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('races.destroy', $race->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm dltBtn"
                                                        data-id="{{ $race->id }}"
                                                        style="height:30px; width:30px;border-radius:50%"
                                                        data-toggle="tooltip" data-placement="bottom" title="Delete">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                                <a href="{{ $race->race === 'election'
                                                    ? route('polls.index', ['race_id' => $race->id])
                                                    : route('race_approvals.index', ['race_id' => $race->id]) }}"
                                                    class="btn btn-info btn-sm" title="Manage Entries">
                                                    <i class="fas fa-list"></i>
                                                </a>
                                            </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center">No Races found.</td>
                                            </tr>
                                    @endforelse
                            </table>
                            {{-- <button type="button" class="btn btn-sm btn-primary csv">Export CSV</button>
                            <button type="button" class="btn btn-sm btn-primary sql">Export SQL</button>
                            <button type="button" class="btn btn-sm btn-primary txt">Export TXT</button>
                            <button type="button" class="btn btn-sm btn-primary json">Export JSON</button> --}}
                        </div>
                    </div><!--end card-body-->
                </div><!--end card-->
            </div> <!--end col-->
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.dltBtn');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection
