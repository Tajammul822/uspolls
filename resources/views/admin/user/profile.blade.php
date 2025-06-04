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
                    <div class="d-flex align-items-center flex-row flex-wrap">
                      {{--<div class="position-relative me-3">
                            <img src="{{asset('images/user/'.$profile->profile_image)}}" alt="" height="120" class="rounded-circle">
                            <a href="#" class="thumb-md justify-content-center d-flex align-items-center bg-primary text-white rounded-circle position-absolute end-0 bottom-0 border border-3 border-card-bg">
                                <i class="fas fa-camera"></i>
                            </a>
                        </div>
                        <div class="">
                            <h5 class="fw-semibold fs-22 mb-1">{{ ('$profile->name') }}</h5>
                            <p class="mb-0 text-muted fw-medium">UI/UX Designer, USA</p>
                        </div>--}}  
                    </div>
                </div>
                <div class="card-body pt-0">
                    <form method="POST" action="{{route('profile-update',$profile->id)}}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3 row">
                            <label for="horizontalInput1" class="col-sm-2 col-form-label">Username</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="horizontalInput1" name="name" value="{{ $profile->name }}" placeholder="Enter UserName">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="horizontalInput1" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" id="horizontalInput1" name="email" placeholder="Enter Email" value="{{ $profile->email }}" disabled>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="horizontalInput1" class="col-sm-2 col-form-label">Image</label>
                            <div class="col-sm-10">
                                <input type="file" class="form-control" id="horizontalInput1" name="profile_image">
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