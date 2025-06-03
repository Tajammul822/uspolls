@extends('admin.layout')
@section('content')
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Create Users</h4>
                        </div><!--end col-->
                    </div> <!--end row-->
                </div><!--end card-header-->    
                <div class="card-body pt-0">
                    <form method="post" action="{{route('users.store')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3 row">
                            <label for="horizontalInput1" class="col-sm-2 col-form-label">Username</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="horizontalInput1" name="name" placeholder="Enter UserName">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="horizontalInput1" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" id="horizontalInput1" name="email" placeholder="Enter Email">
                            </div>
                        </div>
                        <div class="mb-3 row">
                        <label for="horizontalInput1" class="col-sm-2 col-form-label">Password</label>
                            <div class="col-sm-10">
                                <input class="form-control" type="password" value="hunter2" name="password" id="example-password-input">
                            </div>
                        </div>
                        <div class="mb-3 row">
                        <label for="horizontalInput1" class="col-sm-2 col-form-label">Confirm Password</label>
                            <div class="col-sm-10">
                                <input class="form-control" type="password" value="hunter2" name="password_confirmation" id="example-password-input">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="horizontalInput1" class="col-sm-2 col-form-label">User_Type</label>
                            <div class="col-sm-10">
                                <select class="form-select" name="user_type" aria-label="Default select example">
                                    <option selected="">Select User_type</option>
                                    <option value="1">Admin</option>
                                    <option value="0">User</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="horizontalInput1" class="col-sm-2 col-form-label">Status</label>
                            <div class="col-sm-10">
                                <select class="form-select" name="status" aria-label="Default select example">
                                    <option selected="">Select Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-10 ms-auto">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <button type="reset" class="btn btn-danger">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div><!--end card-body-->
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->
</div>
@endsection