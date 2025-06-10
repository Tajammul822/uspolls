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
                                <h4 class="card-title">Export Table</h4>
                                <a href="{{ route('polls.create') }}">
                                    <button type="button" class="btn btn-info">Create Polls</button>
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
                                        <th>Poll Type</th>
                                        <th>Race Type</th>
                                        <th>Election Round</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($polls as $poll)
                                        <tr>
                                            <td>{{ ucfirst($poll->poll_type) }}</td>
                                            <td>{{ ucfirst($poll->race_type) }}</td>
                                            <td>{{ ucfirst($poll->election_round) }}</td>
                                            <td>
                                                @if ($poll->status == 1)
                                                    <span class="badge bg-primary">Active</span>
                                                @else
                                                    <span class="badge bg-warning">Inactive</span>
                                                @endif
                                            </td>

                                            <td class="d-flex justify-evenly-space align-items-center">
                                                <a href="{{ route('polls.edit', $poll->id) }}"
                                                    class="btn btn-primary btn-sm float-left mr-1"
                                                    style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                                    title="edit" data-placement="bottom">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('polls.destroy', $poll->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm dltBtn"
                                                        data-id="{{ $poll->id }}"
                                                        style="height:30px; width:30px;border-radius:50%"
                                                        data-toggle="tooltip" data-placement="bottom" title="Delete">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                                 <a href="{{ route('polls.details', $poll->id) }}"
                                                    class="btn btn-primary btn-sm float-left mr-1"
                                                    style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                                    title="edit" data-placement="bottom">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">No polls found.</td>
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
