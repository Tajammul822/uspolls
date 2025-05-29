
<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="light" data-bs-theme="light">

    
<!-- Mirrored from mannatthemes.com/Politix/default/ui-navbar.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 12 Dec 2024 16:11:17 GMT -->
<head>
        

        <meta charset="utf-8" />
                <title>Politix | Politix - Admin & Dashboard Template</title>
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
                <meta content="" name="author" />
                <meta http-equiv="X-UA-Compatible" content="IE=edge" />

                <!-- App favicon -->
                <link rel="shortcut icon" href="{{ url('assets/images/favicon.ico')}}">

       

         <!-- App css -->
         <link href="{{ url('assets/css/bootstrap.min.css ')}}" rel="stylesheet" type="text/css" />
         <link href="{{ url('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
         <link href="{{ url('assets/css/app.min.css')}}" rel="stylesheet" type="text/css" />

    </head>

    
    <!-- Top Bar Start -->
    <body>
        <!-- Top Bar Start -->
        <div class="topbar d-print-none">
            <div class="container-xxl">
                <nav class="topbar-custom d-flex justify-content-between" id="topbar-custom">    
        

                    <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">                        
                        <li>
                            <button class="nav-link mobile-menu-btn nav-icon" id="togglemenu">
                                <i class="iconoir-menu-scale"></i>
                            </button>
                        </li> 
                        <li class="mx-3 welcome-text">
                            <h3 class="mb-0 fw-bold text-truncate">Good Morning, James!</h3>
                            <!-- <h6 class="mb-0 fw-normal text-muted text-truncate fs-14">Here's your overview this week.</h6> -->
                        </li>                   
                    </ul>
                    <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">
                            
                     
                        <li class="topbar-item">
                            <a class="nav-link nav-icon" href="javascript:void(0);" id="light-dark-mode">
                                <i class="icofont-moon dark-mode"></i>
                                <i class="icofont-sun light-mode"></i>
                            </a>                    
                        </li>
    
                        <li class="dropdown topbar-item">
                            <a class="nav-link dropdown-toggle arrow-none nav-icon" data-bs-toggle="dropdown" href="#" role="button"
                                aria-haspopup="false" aria-expanded="false">
                                <img src="assets/images/users/avatar-1.jpg" alt="" class="thumb-lg rounded-circle">
                            </a>
                            <div class="dropdown-menu dropdown-menu-end py-0">
                                <div class="d-flex align-items-center dropdown-item py-2 bg-secondary-subtle">
                                    <div class="flex-shrink-0">
                                        <img src="assets/images/users/avatar-1.jpg" alt="" class="thumb-md rounded-circle">
                                    </div>
                                    <div class="flex-grow-1 ms-2 text-truncate align-self-center">
                                        <h6 class="my-0 fw-medium text-dark fs-13">William James</h6>
                                        <small class="text-muted mb-0">Front End Developer</small>
                                    </div><!--end media-body-->
                                </div>
                     
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="las la-power-off fs-18 me-1 align-text-bottom"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </li>
                    </ul><!--end topbar-nav-->
                </nav>
                <!-- end navbar-->
            </div>
        </div>
        <!-- Top Bar End -->
        <!-- leftbar-tab-menu -->
        <div class="startbar d-print-none">
            <!--start brand-->
            <div class="brand">
                <h5>
                    POLITIX
                </h5>
            </div>
            <!--end brand-->
            <!--start startbar-menu-->
            <div class="startbar-menu" >
                <div class="startbar-collapse" id="startbarCollapse" data-simplebar>
                    <div class="d-flex align-items-start flex-column w-100">
                        <!-- Navigation -->
                        <ul class="navbar-nav mb-auto w-100">
                            <li class="menu-label pt-0 mt-0">
                                <!-- <small class="label-border">
                                    <div class="border_left hidden-xs"></div>
                                    <div class="border_right"></div>
                                </small> -->
                                <span>Main Menu</span>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#sidebarDashboards" data-bs-toggle="collapse" role="button"
                                    aria-expanded="false" aria-controls="sidebarDashboards">
                                    <i class="iconoir-home-simple menu-icon"></i>
                                    <span>Dashboards</span>
                            </a>
                            </li><!--end nav-item-->
                            <li class="nav-item">
                                <a class="nav-link" href="#sidebarApplications" data-bs-toggle="collapse" role="button"
                                    aria-expanded="false" aria-controls="sidebarApplications">
                                    <i class="iconoir-view-grid menu-icon"></i>
                                    <span>Users</span>
                                </a>
                                <div class="collapse " id="sidebarApplications">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link" href="#">User List</a>    {{-- {{ route('users.index')}} --}}
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#">User Add</a>    {{-- {{ route('users.create')}} --}}
                                        </li>
                                    </ul><!--end nav-->
                                </div><!--end startbarApplications-->
                            </li><!--end nav-item-->
                        </ul>
                    </div>
                </div><!--end startbar-collapse-->
            </div><!--end startbar-menu-->    
        </div><!--end startbar-->
        <div class="startbar-overlay d-print-none"></div>
        <!-- end leftbar-tab-menu-->


        <div class="page-wrapper">

            <!-- Page Content-->
            <div class="page-content">
                <div class="container-xxl">

                    <div class="row justify-content-center">
                        <div class="col-md-6 col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row align-items-center">
                                        <div class="col">                      
                                            <h4 class="card-title">3D Bubble Chart</h4>                      
                                        </div><!--end col-->
                                    </div>  <!--end row-->                                  
                                </div><!--end card-header-->
                                <div class="card-body pt-0">
                                    <div id="apex_bubble" class="apex-charts"></div>          
                                </div><!--end card-body--> 
                            </div><!--end card--> 
                        </div> <!--end col--> 
                        <div class="col-md-6 col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row align-items-center">
                                        <div class="col">                      
                                            <h4 class="card-title">Simple Candlestick Chart</h4>                      
                                        </div><!--end col-->
                                    </div>  <!--end row-->                                  
                                </div><!--end card-header-->
                                <div class="card-body pt-0">
                                    <div id="apex_candlestick" class="apex-charts"></div>
                                </div><!--end card-body--> 
                            </div><!--end card--> 
                        </div> <!--end col-->                                                       
                    </div><!--end row-->

                    <div class="row justify-content-center">
                        <div class="col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row align-items-center">
                                        <div class="col">                      
                                            <h4 class="card-title">Simple Pie Chart</h4>                      
                                        </div><!--end col-->
                                    </div>  <!--end row-->                                  
                                </div><!--end card-header-->
                                <div class="card-body pt-0">
                                    <div id="apex_pie1" class="apex-charts"></div>            
                                </div><!--end card-body--> 
                            </div><!--end card--> 
                        </div> <!--end col--> 
                        <div class="col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row align-items-center">
                                        <div class="col">                      
                                            <h4 class="card-title">Gradient Donut Chart</h4>                      
                                        </div><!--end col-->
                                    </div>  <!--end row-->                                  
                                </div><!--end card-header-->
                                <div class="card-body pt-0">
                                    <div id="apex_pie2" class="apex-charts"></div>
                                </div><!--end card-body--> 
                            </div><!--end card--> 
                        </div> <!--end col--> 
                        <div class="col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row align-items-center">
                                        <div class="col">                      
                                            <h4 class="card-title">Patterned Donut Chart</h4>                      
                                        </div><!--end col-->
                                    </div>  <!--end row-->                                  
                                </div><!--end card-header-->
                                <div class="card-body pt-0">
                                    <div id="apex_pie3" class="apex-charts"></div>            
                                </div><!--end card-body--> 
                            </div><!--end card--> 
                        </div> <!--end col--> 
                        <div class="col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row align-items-center">
                                        <div class="col">                      
                                            <h4 class="card-title">Basic RadialBar Chart</h4>                      
                                        </div><!--end col-->
                                    </div>  <!--end row-->                                  
                                </div><!--end card-header-->
                                <div class="card-body pt-0">
                                    <div id="apex_radialbar1" class="apex-charts"></div>
                                </div><!--end card-body--> 
                            </div><!--end card--> 
                        </div> <!--end col--> 
                        <div class="col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row align-items-center">
                                        <div class="col">                      
                                            <h4 class="card-title">Multiple RadialBars</h4>                      
                                        </div><!--end col-->
                                    </div>  <!--end row-->                                  
                                </div><!--end card-header-->
                                <div class="card-body pt-0">
                                    <div id="apex_radialbar2" class="apex-charts"></div>            
                                </div><!--end card-body--> 
                            </div><!--end card--> 
                        </div> <!--end col--> 
                        <div class="col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row align-items-center">
                                        <div class="col">                      
                                            <h4 class="card-title">Stroked Circular Guage</h4>                      
                                        </div><!--end col-->
                                    </div>  <!--end row-->                                  
                                </div><!--end card-header-->
                                <div class="card-body pt-0">
                                    <div id="apex_radialbar3" class="apex-charts"></div>
                                </div><!--end card-body--> 
                            </div><!--end card--> 
                        </div> <!--end col-->                                                       
                    </div><!--end row-->
                               
                </div><!-- container -->
                

                <footer class="footer text-center text-sm-start d-print-none">
                    <div class="container-xxl">
                        <div class="row">
                            <div class="col-12">
                                <div class="card mb-0 rounded-bottom-0">
                                    <div class="card-body">
                                        <p class="text-muted mb-0">
                                            ©
                                            <script> document.write(new Date().getFullYear()) </script>
                                            Politix
                                            <span
                                                class="text-muted d-none d-sm-inline-block float-end">
                                                Crafted with
                                                <i class="iconoir-heart text-danger"></i>
                                                by Politix</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>
                
                <!--end footer-->
            </div>
            <!-- end page content -->
        </div>
        <!-- end page-wrapper -->

        <!-- Javascript  -->  
        <!-- vendor js -->
        
        <script src="{{ url('assets/js/bootstrap.bundle.min.js')}}"></script>
        <script src="{{ url('assets/js/simplebar.min.js')}}"></script>
        <script src="{{ url('assets/js/app.js')}}"></script>
        <script src="{{ url('assets/js/apexcharts.min.js')}}"></script>
        <script src="{{ url('assets/js/irregular-data-series.js')}}"></script>
        <script src="{{ url('assets/js/ohlc.js')}}"></script>
        <script src="{{ url('assets/js/apexcharts.init.js')}}"></script>
    </body>
</html>