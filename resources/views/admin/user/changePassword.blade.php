@extends('admin.layout')

@section('content')
    <div class="container-xxl">
        <div class="row justify-content-center">
            <div class="col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                        </div>
                    </div>
                    <div class="col-lg-4 align-self-center mb-3 mb-lg-0">
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Change Password</h4>
                        </div><!--end card-header-->
                        <div class="card-body pt-0">
                            <form action="{{ route('change.admin.password') }}" method="POST">
                                @csrf

                                {{-- Current Password --}}
                                <div class="form-group mb-3 row">
                                    <label class="col-xl-3 col-lg-3 text-end mb-lg-0 align-self-center form-label">Current
                                        Password</label>
                                    <div class="col-lg-9 col-xl-8">
                                        <input class="form-control @error('current_password') is-invalid @enderror"
                                            type="password" name="current_password" placeholder="Password">
                                        @error('current_password')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- New Password --}}
                                <div class="form-group mb-3 row">
                                    <label class="col-xl-3 col-lg-3 text-end mb-lg-0 align-self-center form-label">New
                                        Password</label>
                                    <div class="col-lg-9 col-xl-8">
                                        <input class="form-control @error('new_password') is-invalid @enderror"
                                            type="password" name="new_password" placeholder="New Password">
                                        @error('new_password')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Confirm Password --}}
                                <div class="form-group mb-3 row">
                                    <label class="col-xl-3 col-lg-3 text-end mb-lg-0 align-self-center form-label">Confirm
                                        Password</label>
                                    <div class="col-lg-9 col-xl-8">
                                        <input class="form-control @error('new_confirm_password') is-invalid @enderror"
                                            type="password" name="new_confirm_password" placeholder="Re-Password">
                                        @error('new_confirm_password')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Buttons --}}
                                <div class="form-group row">
                                    <div class="col-lg-9 col-xl-8 offset-lg-3">
                                        <button type="submit" class="btn btn-primary">Change Password</button>
                                        <button type="reset" class="btn btn-danger">Reset</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                       
                    </div>>
                </div>
            </div>
        </div>
    </div>
@endsection
