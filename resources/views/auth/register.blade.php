
<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="light" data-bs-theme="light">

    
<!-- Mirrored from mannatthemes.com/rizz/default/auth-register.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 12 Dec 2024 16:11:56 GMT -->
<head>
        

        <meta charset="utf-8" />
                <title>Politix | Politix - Register to politix</title>
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
                <meta content="" name="author" />
                <meta http-equiv="X-UA-Compatible" content="IE=edge" />

                <!-- App favicon -->
                <link rel="shortcut icon" href="{{ url('assets/images/favicon.ico') }}">

       
         <!-- App css -->
         <link href="{{ url('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
         <link href="{{ url('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
         <link href="{{ url('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    </head>

    
    <!-- Top Bar Start -->
    <body>
    <div class="container-xxl">
        <div class="row vh-100 d-flex justify-content-center">
            <div class="col-12 align-self-center">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 mx-auto">
                            <div class="card">  
                                <div class="card-body pt-0">                                    
                                    <form class="my-4" method="POST" action="{{ route('auth.register') }}">
                                        @csrf <!-- CSRF protection -->
    
                                        <div class="form-group mb-2">
                                            <label class="form-label" for="username">Username</label>
                                            <input type="text" class="form-control" id="username" name="name" placeholder="Enter username" value="{{ old('name') }}" required>
                                            @error('name')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
    
                                        <div class="form-group mb-2">
                                            <label class="form-label" for="useremail">Email</label>
                                            <input type="email" class="form-control" id="useremail" name="email" placeholder="Enter email" value="{{ old('email') }}" required>
                                            @error('email')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
    
                                        <div class="form-group mb-2">
                                            <label class="form-label" for="userpassword">Password</label>
                                            <input type="password" class="form-control" name="password" id="userpassword" placeholder="Enter password" required>
                                            @error('password')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
    
                                        <div class="form-group mb-2">
                                            <label class="form-label" for="Confirmpassword">Confirm Password</label>
                                            <input type="password" class="form-control" name="password_confirmation" id="Confirmpassword" placeholder="Confirm password" required>
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
                                    @if(session('success'))
                                    <div class="alert alert-success mt-3">{{ session('success') }}</div>
                                    @endif
    
                                    @if(session('error'))
                                    <div class="alert alert-danger mt-3">{{ session('error') }}</div>
                                    @endif
                                    <div class="text-center">
                                        <p class="text-muted">Already have an account ? <a href="{{ route('login') }}" class="text-primary ms-2">Log in</a></p>
                                    </div>
                                </div>
                            </div><!--end card-->
                        </div><!--end col-->
                    </div><!--end row-->
                </div><!--end card-body-->
            </div><!--end col-->
        </div><!--end row-->                                        
    </div><!-- container -->
    </body>
    <!--end body-->

</html>