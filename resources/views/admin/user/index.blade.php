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
                                <h4 class="card-title">Export Users</h4>
                                <a href="{{ route('users.create') }}">
                                    <button type="button" class="btn btn-info">Create User</button>
                                </a>
                            </div><!--end col-->
                        </div> <!--end row-->
                    </div><!--end card-header-->
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table datatable" id="datatable_2">
                                <thead class="">
                                    <tr>
                                        <th>UserName</th>
                                        <th>Email</th>
                                        <th>User_Type</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>

                                                @if ($user->role == 1)
                                                    Admin
                                                @elseif($user->role == 0)
                                                    User
                                                @endif
                                            </td>
                                            <td>
                                                @if ($user->status == 1)
                                                    <span class="badge bg-primary" style="">Active</span>
                                                @elseif($user->status == 0)
                                                    <span class="badge bg-warning">Inactive</span>
                                                @else
                                                    Null
                                                @endif
                                            </td>

                                            <td class="d-flex justify-evenly-space align-items-center">
                                                <a href="{{ route('users.edit', ['user' => $user->id]) }}"
                                                    class="btn btn-primary btn-sm float-left mr-1"
                                                    style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                                    title="edit" data-placement="bottom">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('users.destroy', ['user' => $user->id]) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm dltBtn"
                                                        data-id="{{ $user->id }}"
                                                        style="height:30px; width:30px;border-radius:50%"
                                                        data-toggle="tooltip" data-placement="bottom" title="Delete">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
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
