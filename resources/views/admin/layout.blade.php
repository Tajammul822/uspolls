<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="light" data-bs-theme="light">

<head>


    <meta charset="utf-8" />
    <title>Politix Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Politix Dashboard" name="description" />
    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ url('assets/images/favicon.ico') }}">
    <link rel="stylesheet" href="{{ url('assets/css/jsvectormap.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/style.css') }}">

    <!-- App css -->
    <link rel="stylesheet" href="{{ url('assets/css/bootstrap.min.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ url('assets/css/icons.min.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ url('assets/css/app.min.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ url('assets/css/selectr.min.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ url('assets/css/datepicker.min.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ url('assets/css/huebee.min.css') }}" type="text/css" />

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
                        <h3 class="mb-0 fw-bold text-truncate">{{ $greeting }},
                            {{ Auth::user()->name ?? 'Guest' }}!</h3>
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
                        <a class="nav-link dropdown-toggle arrow-none nav-icon" data-bs-toggle="dropdown" href="#"
                            role="button" aria-haspopup="false" aria-expanded="false">
                            <img src="{{ url('assets/images/users/avatar-1.jpg') }}" alt=""
                                class="thumb-lg rounded-circle">
                        </a>
                        <div class="dropdown-menu dropdown-menu-end py-0">
                            <div class="d-flex align-items-center dropdown-item py-2 bg-secondary-subtle">
                                <div class="flex-shrink-0">
                                    <img src="{{ url('assets/images/users/avatar-1.jpg') }}" alt=""
                                        class="thumb-md rounded-circle">
                                </div>
                                <div class="flex-grow-1 ms-2 text-truncate align-self-center">
                                    <h6 class="my-0 fw-medium text-dark fs-13">William James</h6>
                                    <small class="text-muted mb-0">Front End Developer</small>
                                </div><!--end media-body-->
                            </div>
                            <div class="dropdown-divider mt-0"></div>
                            <small class="text-muted px-2 pb-1 d-block">Account</small>
                            <a class="dropdown-item" href="{{ route('admin-profile') }}"><i
                                    class="las la-user fs-18 me-1 align-text-bottom"></i> Profile</a>

                            <a class="dropdown-item" href="{{ route('change.password.form') }}"><i
                                    class="las la-user fs-18 me-1 align-text-bottom"></i> Change Password</a>
                            <div class="dropdown-divider mb-0"></div>
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
        <div class="startbar-menu">
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
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <i class="iconoir-home-simple menu-icon"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#sidebarApplications" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="sidebarApplications">
                                <i class="iconoir-view-grid menu-icon"></i>
                                <span>Users</span>
                            </a>
                            <div class="collapse " id="sidebarApplications">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('users.index') }}">User List</a>
                                        {{-- {{ route('users.index')}} --}}
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('users.create') }}">User Add</a>
                                        {{-- {{ route('users.create')}} --}}
                                    </li>
                                </ul>
                            </div>
                        </li>

                        {{-- <li class="nav-item">
                            <a class="nav-link" href="#sidebarPollster" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="sidebarPollster">
                                <i class="fa-solid fa-person-booth menu-icon"></i>
                                <span>Pollsters</span>
                            </a>
                            <div class="collapse " id="sidebarPollster">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('pollsters.index') }}">Pollster List</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('pollsters.create') }}">Pollster Add</a>
                                    </li>
                                </ul>
                            </div>
                        </li> --}}

                        <li class="nav-item">
                            <a class="nav-link" href="#sidebarPolls" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="sidebarPolls">
                                <i class="fas fa-poll menu-icon"></i>
                                <span>Polls</span>
                            </a>
                            <div class="collapse " id="sidebarPolls">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('polls.index') }}">Poll List</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('polls.create') }}">Poll Add</a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                       <li class="nav-item">
                            <a class="nav-link" href="#sidebarCandidate" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="sidebarCandidate">
                                <i class="fas fa-users menu-icon"></i>
                                <span>Candidates</span>
                            </a>
                            <div class="collapse " id="sidebarCandidate">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('candidates.index') }}">Candidate List</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('candidates.create') }}">Candidate Add</a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        {{--  <li class="nav-item">
                            <a class="nav-link" href="#sidebarPoll_Candidate" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="sidebarPoll_Candidate">
                                <i class="fa-solid fa-user-tie menu-icon"></i>
                                <span>Poll Candidates</span>
                            </a>
                            <div class="collapse " id="sidebarPoll_Candidate">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('poll_candidates.index') }}">Poll Candidate List</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('poll_candidates.create') }}">Poll Candidate Add</a>
                                    </li>
                                </ul>
                            </div>
                        </li>


                        <li class="nav-item">
                            <a class="nav-link" href="#sidebarPoll_Approval" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="sidebarPoll_Approval">
                                <i class="fa-solid fa-ranking-star menu-icon"></i>
                                <span>Poll Approval</span>
                            </a>
                            <div class="collapse " id="sidebarPoll_Approval">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('poll_approvals.index') }}">Poll Approval List</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('poll_approvals.create') }}">Poll Approval Add</a>
                                    </li>
                                </ul>
                            </div>
                        </li>


                        <li class="nav-item">
                            <a class="nav-link" href="#sidebarRace" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="sidebarRace">
                                <i class="fa-solid fa-flag-checkered menu-icon"></i>
                                <span>Races</span>
                            </a>
                            <div class="collapse " id="sidebarRace">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('races.index') }}">Race List</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('races.create') }}">Race Add</a>
                                    </li>
                                </ul>
                            </div>
                        </li> --}}


                        <li class="nav-item">
                            <a class="nav-link" href="#sidebarStates" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="sidebarStates">
                                <i class="fa-solid fa-house menu-icon"></i>
                                <span>States</span>
                            </a>
                            <div class="collapse " id="sidebarStates">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('states.index') }}">State List</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('states.create') }}">State Add</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    < <div class="startbar-overlay d-print-none">
        </div>

        <div class="page-wrapper">

            <!-- Page Content-->
            <div class="page-content">
                <div class="container-xxl">
                    @yield('admin-dasboard-content')
                    @yield('create-poll-content')
                    @yield('content')
                    
                </div><!-- container -->

                <footer class="footer text-center text-sm-start d-print-none">
                    <div class="container-xxl">
                        <div class="row">
                            <div class="col-12">
                                <div class="card mb-0 rounded-bottom-0">
                                    <div class="card-body">
                                        <p class="text-muted mb-0">
                                            ©
                                            <script>
                                                document.write(new Date().getFullYear())
                                            </script>
                                            Politix
                                            <span class="text-muted d-none d-sm-inline-block float-end">
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
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
        <script src="{{ url('assets/js/app.js') }}"></script>
        <script src="{{ url('assets/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ url('assets/js/simplebar.min.js') }}"></script>
        <script src="{{ url('assets/js/apexcharts.min.js') }}"></script>
        <script src="{{ url('assets/js/irregular-data-series.js') }}"></script>
        <script src="{{ url('assets/js/ohlc.js') }}"></script>
        <script src="{{ url('assets/js/apexcharts.init.js') }}"></script>
        <script src="{{ url('assets/js/simple-datatables.js') }}"></script>
        <script src="{{ url('assets/js/datatable.init.js') }}"></script>
        <script src="{{ url('assets/js/stock-prices.js') }}"></script>
        <script src="{{ url('assets/js/jsvectormap.min.js') }}"></script>
        <script src="{{ url('assets/js/world.js') }}"></script>
        <script src="{{ url('assets/js/index.init.js') }}"></script>
        <script src="{{ url('assets/js/forms-advanced.js') }}"></script>
        <script src="{{ url('assets/js/selectr.min.js') }}"></script>
        <script src="{{ url('assets/js/huebee.pkgd.min.js') }}"></script>
        <script src="{{ url('assets/js/datepicker-full.min.js') }}"></script>
        <script src="{{ url('assets/js/moment.js') }}"></script>
        <script src="{{ url('assets/js/imask.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>
