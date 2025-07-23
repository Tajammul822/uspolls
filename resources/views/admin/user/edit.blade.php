@extends('admin.layout')
@section('content')
    <div class="container-xxl">
        <div class="row justify-content-center">
            <div class="col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Edit Users</h4>
                            </div><!--end col-->
                        </div> <!--end row-->
                    </div><!--end card-header-->
                    <div class="card-body pt-0">
                        <form method="POST" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mb-3 row">
                                <label for="horizontalInput1" class="col-sm-2 col-form-label">Username</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="horizontalInput1"
                                        value="{{ $user->name }}" name="name" placeholder="Enter UserName" disabled>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="horizontalInput1" class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" id="horizontalInput1"
                                        value="{{ $user->email }}" name="email" placeholder="Enter Email" disabled>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="horizontalInput1" class="col-sm-2 col-form-label">User Type</label>
                                <div class="col-sm-10">
                                    <select class="form-select" name="user_type" aria-label="Default select example">
                                        <option selected="">Select User Type</option>
                                        <option value="1" {{ $user->role == 1 ? 'selected' : '' }}>Admin</option>
                                        <option value="0" {{ $user->role == 0 ? 'selected' : '' }}>User</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="horizontalInput1" class="col-sm-2 col-form-label">Status</label>
                                <div class="col-sm-10">
                                    <select class="form-select" name="status" aria-label="Default select example">
                                        <option value="1" {{ $user->status == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ $user->status == 0 ? 'selected' : '' }}>Inactive
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-10 ms-auto">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <button type="reset" class="btn btn-danger">Reset</button>
                                </div>
                            </div>
                        </form>
                    </div><!--end card-body-->
                </div><!--end card-->
            </div> <!--end col-->
        </div><!--end row-->
    </div>
@endsection
