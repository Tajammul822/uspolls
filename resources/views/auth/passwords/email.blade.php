@extends('auth.layouts')

@section('content')
    <div class="container">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        <form class="my-4" method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group mb-2">
                <label class="form-label" for="username">Email Address</label>
                <input type="email" class="form-control" id="useremail" name="email" placeholder="Enter Email Address"
                    required autofocus>
            </div>
            @error('email')
                <span class="text-danger">{{ $message }}</span>
            @enderror
            <!--end form-group-->
            <div class="form-group mb-0 row">
                <div class="col-12">
                    <div class="d-grid mt-3">
                        <button class="btn btn-primary" type="submit">Reset <i
                                class="fas fa-sign-in-alt ms-1"></i></button>
                    </div>
                </div><!--end col-->
            </div> <!--end form-group-->
        </form>
    </div>
@endsection
