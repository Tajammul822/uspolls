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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />
    <style>
        /* 1) Base “hidden” state: no border, and completely hidden */
        #suggestions {
            display: none;
            max-width: 300px;
            position: absolute;
            width: 280px;
            background: #fff;
            border-radius: 5px;
            top: 173px;
            z-index: 1;
            margin: 0 auto;
            right: 32vw;
        }

        /* 2) When it has at least one child (i.e. you’ve injected <div>…</div>), show it with border */
        #suggestions:not(:empty) {
            display: block;
            border: 1px solid #e3e3e3;
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

        /* .chart-wrapper {
            height: 350px;
            position: relative;
            margin-top: 1rem;
        }

        .stat-item {
            display: inline-block;
            margin-right: 1.5rem;
        }

        .positive {
            color: #38a169;
        }

        .negative {
            color: #e53e3e;
        } */

        .chart-container {
            width: 100%;
        }

        .time-filters,
        .approvalfilters {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .time-filter,
        .approvalfilter {
            cursor: pointer;
            padding: .25rem .5rem;
            border-radius: .25rem;
        }

        .time-filter.active,
        .approvalfilter.active {
            background: #3182ce;
            color: #fff;
        }

        .chart-wrapper {
            height: 350px;
            position: relative;
        }

        .stat-item {
            display: inline-block;
            margin-right: 1.5rem;
        }

        .positive {
            color: #38a169;
        }

        .negative {
            color: #e53e3e;
        }

        .polling-table th,
        .polling-table td {
            text-align: center;
            padding: 8px;
        }

        .poll-result.positive {
            color: green;
        }

        .poll-result.negative {
            color: red;
        }

        .toggle-more {
            background: none;
            border: none;
            cursor: pointer;
            color: #007bff;
        }

        .more-list {
            background: #f9f9f9;
            border: 1px solid #ddd;
            margin-top: 4px;
            padding: 5px;
        }

        .pagination-controls button {
            margin: 0 3px;
            padding: 4px 8px;
        }

        .pagination-controls .active {
            font-weight: bold;
        }

        #suggestions div {
            padding: 5px;
            cursor: pointer;
        }

        #suggestions div:hover {
            background: #e0e0e0;
        }

        /* Accordion detail row styling */
        .row-details {
            background: #f9f9f9;
            padding: 0.5em 1em;
        }

        .details-control {
            cursor: pointer;
            text-align: center;
            font-weight: bold;
        }

        /* Positive / negative streaks */
        .poll-result.positive {
            color: green;
        }

        .poll-result.negative {
            color: red;
        }



        /* Accordion detail row styling */
        .row-details {
            background: #f9f9f9;
            padding: 0.5em 1em;
        }

        .details-control {
            cursor: pointer;
            text-align: center;
            font-weight: bold;
        }

        /* Positive / negative colors */
        .poll-result.positive {
            color: green;
        }

        .poll-result.negative {
            color: red;
        }



        /* Header styles */
        .header-section {
            background: linear-gradient(to right, #1a3a6c, #2c5282);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .page-title {
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .page-subtitle {
            font-size: 18px;
            color: rgba(255, 255, 255, 0.85);
            max-width: 600px;
            margin: 0 auto;
        }

        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.2), transparent);
            margin: 25px 0;
        }

        /* Poll types inside filter card */
        .filter-card {
            background: white;
            padding: 30px;
            border-bottom: 1px solid #e2e8f0;
        }

        .filter-card-title {
            font-size: 22px;
            font-weight: 700;
            color: #1a3a6c;
            margin-bottom: 25px;
            text-align: center;
        }

        /* .poll-types-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            gap: 20px;
        } */

        .poll-type-column {

            display: flex;
            justify-content: space-between;
            flex-direction: row;
        }

        .poll-type-header {
            font-weight: 700;
            margin-bottom: 15px;
            color: #1a3a6c;
            font-size: 18px;
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
        }


        #polling-table tbody tr.group-header td {
            font-weight: bold;
            background-color: #f0f0f0;
        }

        .poll-type-item {
            padding: 12px 15px;
            border-radius: 6px;
            background: #edf2f7;
            margin-bottom: 10px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 22%;
        }

        .poll-type-item i {
            color: #1a3a6c;
        }

        .poll-type-item:hover {
            background: #e2e8f0;
            transform: translateX(3px);
        }

        .poll-type-item.active {
            background: #2b5080;
            font-weight: 600;
            color: white;
        }

        .poll-type-item.active i {
            color: #ffffff;
        }

        /* Main filters */
        .filters-container {
            padding: 20px;
        }

        .filters-title {
            font-size: 18px;
            font-weight: 600;
            color: #1a3a6c;
            margin-bottom: 20px;
            text-align: center;
        }

        .filter-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            gap: 15px;
        }

        .filter-option {
            flex: 1;
        }

        .filter-label {
            font-weight: 500;
            color: #4a5568;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .filter-select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #cbd5e0;
            border-radius: 8px;
            background: white;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .filter-select:focus {
            border-color: #1a3a6c;
            box-shadow: 0 0 0 2px rgba(26, 58, 108, 0.2);
            outline: none;
        }

        .apply-btn {
            width: 30%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            background: #1a3a6c;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 10px;
        }

        .apply-btn:hover {
            background: #2c5282;
        }

        /* No polls message */
        .no-polls-container {
            padding: 50px 30px;
            text-align: center;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
        }

        .no-polls-icon {
            font-size: 60px;
            color: #a0aec0;
            margin-bottom: 20px;
            opacity: 0.7;
        }

        .no-polls-title {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #2d3748;
        }

        .no-polls-text {
            font-size: 18px;
            color: #4a5568;
            max-width: 500px;
            margin: 0 auto;
        }



        /* Polls table styling */
        .polls-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-family: Arial, sans-serif;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .polls-table thead {
            background-color: #f5f7fa;
        }

        .polls-table th,
        .polls-table td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #e1e4e8;
        }

        .polls-table th {
            font-size: 14px;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .polls-table tbody tr:nth-child(even) {
            background-color: #fcfcfc;
        }

        .polls-table tbody tr:hover {
            background-color: #f1f8ff;
        }

        .polls-table .positive {
            color: #2e7d32;
            /* green */
            font-weight: 600;
        }

        .polls-table .negative {
            color: #c62828;
            /* red */
            font-weight: 600;
        }

        /* Responsive wrap */
        @media (max-width: 600px) {

            .polls-table th,
            .polls-table td {
                padding: 10px;
                font-size: 12px;
            }
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
                {{-- <a href="{{ route('presidential') }}" class="text-decoration-none hover-opacity-100">Presidential</a>
                <a href="{{ route('senate') }}" class="text-decoration-none hover-opacity-100">Senate</a>
                <a href="{{ route('house') }}" class="text-decoration-none hover-opacity-100">House</a>
                <a href="{{ route('governor') }}" class="text-decoration-none hover-opacity-100">Governor</a> --}}
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
