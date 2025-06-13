@extends('admin.layout')
@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="container-xxl">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Race Approvals</h4>
                                <a href="{{ route('race_approvals.create', ['race_id' => $race->id]) }}">
                                    <button type="button" class="btn btn-info"> + New Approvals</button>
                                </a>
                            </div><!--end col-->
                        </div> <!--end row-->
                    </div><!--end card-header-->
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table datatable" id="datatable_2">
                                <thead class="">
                                    <tr>
                                    <tr>
                                        <th>Name</th>
                                        <th>Pollster</th>
                                        <th>Date</th>
                                        <th>Sample Size</th>
                                        <th>Approve Rating (%)</th>
                                        <th>Disapprove Rating (%)</th>
                                        <th>Actions</th>
                                    </tr>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($raceApprovals as $item)
                                        <tr>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->pollster }}</td>
                                            <td>{{ $item->race_date->format('Y-m-d') }}</td>
                                            <td>{{ $item->sample_size }}</td>
                                            <td>{{ $item->approve_rating }}</td>
                                            <td>{{ $item->disapprove_rating }}</td>

                                            <td class="d-flex justify-evenly-space align-items-center" style="gap: 5px;">
                                                <a href="{{ route('race_approvals.edit', $item) }}"
                                                    class="btn btn-primary btn-sm float-left mr-1"
                                                    style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                                    title="edit" data-placement="bottom">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('race_approvals.destroy', ['race_approval' => $item->id, 'race_id' => $race->id]) }}">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="btn btn-danger btn-sm dltBtn"
                                                        data-id="{{ $item->id }}"
                                                        style="height:30px; width:30px;border-radius:50%"
                                                        data-toggle="tooltip" data-placement="bottom" title="Delete">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No items yet</td>
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
