@extends('auth.layouts')

@section('content')
    <div class="container">
        <form class="my-4" method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="form-group mb-2">
                <label class="form-label" for="userpassword">Password</label>
                <input type="password" class="form-control" name="password" id="userpassword" placeholder="Enter password"
                    required>
                @error('password')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group mb-2">
                <label class="form-label" for="Confirmpassword">Confirm Password</label>
                <input type="password" class="form-control" name="password_confirmation" id="Confirmpassword"
                    placeholder="Confirm password" required>
            </div>

            <div class="form-group mb-0 row">
                <div class="col-12">
                    <div class="d-grid mt-3">
                        <button class="btn btn-primary" type="submit">Reset Password <i
                                class="fas fa-sign-in-alt ms-1"></i></button>
                    </div>
                </div>
            </div>

        </form>
    </div>
@endsection
