@extends('auth.layouts')

@section('content')
    <form class="my-4" method="POST" action="{{ route('auth.register') }}">
        @csrf <!-- CSRF protection -->

        <div class="form-group mb-2">
            <label class="form-label" for="username">Username</label>
            <input type="text" class="form-control" id="username" name="name" placeholder="Enter username"
                value="{{ old('name') }}" required>
            @error('name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group mb-2">
            <label class="form-label" for="useremail">Email</label>
            <input type="email" class="form-control" id="useremail" name="email" placeholder="Enter email"
                value="{{ old('email') }}" required>
            @error('email')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

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
                    <button class="btn btn-primary" type="submit">Register <i class="fas fa-user-plus ms-1"></i></button>
                </div>
            </div>
        </div>
    </form>

    <!-- Show Flash Messages -->
    @if (session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger mt-3">{{ session('error') }}</div>
    @endif
    <div class="text-center">
        <p class="text-muted">Already have an account ? <a href="{{ route('login') }}" class="text-primary ms-2">Log in</a>
        </p>
    </div>
@endsection
