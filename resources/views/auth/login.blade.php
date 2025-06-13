@extends('auth.layouts')

@section('content')
    <form class="my-4" method="POST" action="{{ route('auth.login') }}">
        @csrf <!-- Add CSRF protection -->

        <div class="form-group mb-2">
            <label class="form-label" for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required>
            @error('email')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input type="password" class="form-control" name="password" id="password" placeholder="Enter password"
                required>
            @error('password')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group mb-0 row">
            <div class="col-12">
                <div class="d-grid mt-3">
                    <button class="btn btn-primary" type="submit">Log In <i class="fas fa-sign-in-alt ms-1"></i></button>
                </div>
            </div>
        </div>
    </form>

    <div class="form-group row mt-1 mb-1">
        {{-- <div class="col-sm-6">
                                            <div class="form-check form-switch form-switch-success">
                                                <input class="form-check-input" type="checkbox"
                                                    id="customSwitchSuccess">
                                                <label class="form-check-label" for="customSwitchSuccess">Remember
                                                    me</label>
                                            </div>
                                        </div><!--end col--> --}}
        <div class="col-sm-12 text-start">
            <a href="{{ route('password.request') }}" class="text-muted font-13"><i class="dripicons-lock"></i> Forgot
                password?</a>
        </div><!--end col-->
    </div>

    <div class="text-start  mb-2">
        <p class="text-muted">Don't have an account ? <a href="{{ 'register' }}" class="text-primary ms-2">Resister</a>
        </p>
    </div>
    {{-- <div class="d-flex justify-content-center">
                                        <a href="{{ route('auth.facebook') }}" class="d-flex justify-content-center align-items-center thumb-md bg-blue-subtle text-blue rounded-circle me-2">
                                            <i class="fab fa-facebook align-self-center"></i>
                                        </a>
                                        <a href="{{ route('auth.twitter') }}" class="d-flex justify-content-center align-items-center thumb-md bg-info-subtle text-info rounded-circle me-2">
                                            <i class="fab fa-twitter align-self-center"></i>
                                        </a>
                                        <a href="{{ route('auth.google') }}" class="d-flex justify-content-center align-items-center thumb-md bg-danger-subtle text-danger rounded-circle">
                                            <i class="fab fa-google align-self-center"></i>
                                        </a>
                                    </div> --}}
@endsection
