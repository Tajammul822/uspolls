<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Politix</title>
    <link rel="stylesheet" href="{{ url('assets/css/bootstrap.min.css') }}" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ url('assets/css/custom.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        #suggestions {
                border: 1px solid #e3e3e3;
                max-width: 300px;
                position: absolute;
                width: 280px;
                background: #fff;
                border-radius: 5px;
                top: 155px;
                z-index: 1;
                margin: 0 auto;
                right: 35vw;
        }

        #suggestions div {
            padding: 5px;
            cursor: pointer;
        }

        #suggestions div:hover {
            background: #f0f0f0;
        }

        #results {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="d-flex align-items-center justify-content-between px-4 py-2 border-bottom"
        style="border-color: #2e2e4d !important;">
        <div class="d-flex align-items-center">
            <!-- Logo -->
            <div class="logo d-flex align-items-center me-4">
                <i class="fas fa-chart-line text-warning"></i>
                <span class="ms-2 fs-5 fw-bold text-white">Politix</span>
            </div>

            <!-- Navigation Menu -->
            <nav class="d-none d-md-flex gap-3 menu_list">
                <a href="{{ route('homeboard') }}" class="text-decoration-none active-link">Home</a>
                <a href="{{ route('presidential') }}" class="text-decoration-none hover-opacity-100">Presidential</a>
                <a href="{{ route('senate') }}" class="text-decoration-none hover-opacity-100">Senate</a>
                <a href="{{ route('house') }}" class="text-decoration-none hover-opacity-100">House</a>
                <a href="{{ route('governor') }}" class="text-decoration-none hover-opacity-100">Governor</a>
            </nav>
        </div>

        <!-- Right Side Actions -->
        <div class="d-flex align-items-center gap-3">
            @if (Route::has('login'))
                <nav class="d-flex align-items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="btn btn-light btn-sm rounded text-[#1b1b18] border border-[#19140035] hover:border-[#1915014a] dark:text-[#EDEDEC] dark:border-[#3E3E3A] dark:hover:border-[#62605b]">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="btn btn-light btn-sm rounded text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]">
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="btn btn-light btn-sm rounded text-[#1b1b18] border border-[#19140035] hover:border-[#1915014a] dark:text-[#EDEDEC] dark:border-[#3E3E3A] dark:hover:border-[#62605b]">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </div>
    </header>

    <div class="container">
        @yield('content')
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-content">

            <div class="copyright">
                <div class="footerlogo">
                    <i class="fas fa-chart-line"></i>
                    <span>Politix</span>
                </div>
                <div>
                    © 2025 Politix. All rights reserved. | Data Source: Politics, Gallup, Pew Research, and
                    other leading polling firms
                </div>
            </div>
        </div>
    </footer>

    @if (Route::has('login'))
        <div class="d-none d-lg-block h-14_5"></div>
    @endif

    
    <script src="{{ url('assets/js/bootstrap.bundle.min.js') }}"></script>

</body>

</html>
