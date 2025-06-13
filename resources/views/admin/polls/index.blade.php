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
                                <h4 class="card-title">Polls</h4>
                                <a href="{{ route('polls.create', ['race_id' => $race->id]) }}">
                                    <button type="button" class="btn btn-info"> + New Poll</button>
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
                                        <th>Date</th>
                                        <th>Source</th>
                                        <th>Sample</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($polls as $item)
                                        <tr>
                                            <td>{{ $item->race->race }}</td>
                                            <td>{{ $item->poll_date->format('Y-m-d') }}</td>
                                            <td>{{ $item->pollster_source }}</td>
                                            <td>{{ $item->sample_size }}</td>

                                            <td class="d-flex justify-evenly-space align-items-center" style="gap: 5px;">
                                                <a href="{{ route('polls.edit', ['poll' => $item->id, 'race_id' => $race->id]) }}"
                                                    class="btn btn-primary btn-sm float-left mr-1"
                                                    style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                                    title="edit" data-placempollent="bottom">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <form method="POST"
                                                    action="{{ route('polls.destroy', ['poll' => $item->id, 'race_id' => $race->id]) }}">
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
                                </tbody>
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
