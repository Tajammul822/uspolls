@extends('frontend.layout')
@section('content')
    <!-- Main Content (70%) -->
    <div class="main-content">
        <!-- Filters -->
        <div class="filters">
            <div class="filter-group">
                <label for="poll-type"><i class="fas fa-filter"></i> Poll Type:</label>
                <select id="poll-type">
                    <option value="all">All Polls</option>
                    <option value="approval">Approval Ratings</option>
                    <option value="senate">Senate Races</option>
                    <option value="president">President Matchups</option>
                </select>
            </div>

            {{-- <div class="filter-group">
                <label for="state"><i class="fas fa-map-marker-alt"></i> State:</label>
                <select id="state">
                    <option value="all">All States</option>
                    <option value="az">Arizona</option>
                    <option value="ga">Georgia</option>
                    <option value="mi">Michigan</option>
                    <option value="nv">Nevada</option>
                    <option value="pa">Pennsylvania</option>
                    <option value="tx">Texas</option>
                </select>
            </div> --}}

            <!-- State Filter -->
            <div class="filters">
                <div class="filter-group">
                    <label for="state"><i class="fas fa-map-marker-alt"></i> State:</label>
                    <select id="state">
                        <option value="">-- Select State --</option>
                        @foreach ($states as $st)
                            <option value="{{ $st->id }}">{{ $st->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="filter-group">
                <label for="search-input"><i class="fas fa-search"></i> Find a Poll</label>
                <input type="text" id="search-input" placeholder="Search by candidate…">
                <div id="suggestions"></div>
            </div>
        </div>

        <!-- Polling Table -->
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-table"></i> Latest Polling Data</div>
                <div class="time-filters">…</div>
            </div>
            <div class="polling-table-container">
                <table class="polling-table" id="polling-table">
                    <thead id="poll-table-head">
                        <!-- dynamically injected -->
                    </thead>
                    <tbody id="poll-table-body">
                        <!-- dynamically injected -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="chart-container">
            <div class="time-filters">
                <div class="time-filter active">1D</div>
                <div class="time-filter">1W</div>
                <div class="time-filter">1M</div>
                <div class="time-filter">1Y</div>
                <div class="time-filter">5Y</div>
                <div class="time-filter">ALL</div>
            </div>
            <!-- </div> -->
            <div class="chart-header">
                <div class="chart-title">President Approval Rating Trend</div>
                <div class="chart-stats">
                    <div class="stat-item">
                        <div class="stat-label">Current Approval</div>
                        <div class="stat-value positive">49.7%</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Current Disapproval</div>
                        <div class="stat-value negative">47.5%</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Net Approval</div>
                        <div class="stat-value positive">+2.2%</div>
                    </div>
                </div>
            </div>

            <div class="chart-wrapper">
                <canvas id="approvalChart"></canvas>
            </div>
        </div>

        <!-- Polling Table -->
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-table"></i> Latest Polling Data</div>
                <div class="time-filters">
                    <div class="time-filter">30 Days</div>
                    <div class="time-filter active">1 Year</div>
                    <div class="time-filter">5 Years</div>
                </div>
            </div>

            <div class="polling-table-container">
                <table class="polling-table">
                    <thead>
                        <tr>
                            <th>Pollster</th>
                            <th>Date</th>
                            <th>Approve</th>
                            <th>Disapprove</th>
                            <th>Net</th>
                            <th>Sample Size</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Jun 18, 2025</td>
                            <td>RealClear Politics</td>
                            <td class="poll-result positive">49.7%</td>
                            <td class="poll-result negative">47.5%</td>
                            <td class="poll-result positive">+2.2%</td>
                            <td>1,200 LV</td>
                        </tr>
                        <tr>
                            <td>Jun 17, 2025</td>
                            <td>Gallup</td>
                            <td class="poll-result positive">48.9%</td>
                            <td class="poll-result negative">48.2%</td>
                            <td class="poll-result positive">+0.7%</td>
                            <td>1,500 RV</td>
                        </tr>
                        <tr>
                            <td>Jun 16, 2025</td>
                            <td>Pew Research</td>
                            <td class="poll-result positive">49.1%</td>
                            <td class="poll-result negative">47.8%</td>
                            <td class="poll-result positive">+1.3%</td>
                            <td>2,000 A</td>
                        </tr>
                        <tr>
                            <td>Jun 15, 2025</td>
                            <td>YouGov</td>
                            <td class="poll-result negative">47.3%</td>
                            <td class="poll-result positive">49.1%</td>
                            <td class="poll-result negative">-1.8%</td>
                            <td>1,350 LV</td>
                        </tr>
                        <tr>
                            <td>Jun 14, 2025</td>
                            <td>Rasmussen</td>
                            <td class="poll-result positive">51.2%</td>
                            <td class="poll-result negative">46.8%</td>
                            <td class="poll-result positive">+4.4%</td>
                            <td>1,000 LV</td>
                        </tr>
                        <tr>
                            <td>Jun 13, 2025</td>
                            <td>Ipsos</td>
                            <td class="poll-result positive">48.5%</td>
                            <td class="poll-result negative">48.0%</td>
                            <td class="poll-result positive">+0.5%</td>
                            <td>1,250 A</td>
                        </tr>
                        <tr>
                            <td>Jun 12, 2025</td>
                            <td>Monmouth</td>
                            <td class="poll-result negative">47.9%</td>
                            <td class="poll-result positive">48.4%</td>
                            <td class="poll-result negative">-0.5%</td>
                            <td>800 RV</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar (30%) -->
    <div class="sidebar">
        <!-- Key Metrics -->
        <div class="sidebar-card">
            <div class="sidebar-title"><i class="fas fa-tachometer-alt"></i> Key Metrics</div>
            <div class="data-points">
                <div class="data-card">
                    <div class="data-value positive">49.7%</div>
                    <div class="data-label">Current Approval</div>
                </div>
                <div class="data-card">
                    <div class="data-value negative">47.5%</div>
                    <div class="data-label">Current Disapproval</div>
                </div>
                <div class="data-card">
                    <div class="data-value positive">+2.2%</div>
                    <div class="data-label">Net Approval</div>
                </div>
                <div class="data-card">
                    <div class="data-value">64%</div>
                    <div class="data-label">Re-election Chance</div>
                </div>
            </div>
        </div>

        <!-- Latest Analysis -->
        <div class="sidebar-card">
            <div class="sidebar-title"><i class="fas fa-newspaper"></i> Latest Analysis</div>

            <div class="blog-post">
                <div class="blog-title">Why Approval Ratings Are Rising Despite Economic Concerns</div>
                <div class="blog-meta">
                    <span><i class="far fa-clock"></i> 2 hours ago</span>
                    <span><i class="far fa-user"></i> John Pollster</span>
                </div>
            </div>

            <div class="blog-post">
                <div class="blog-title">Regional Breakdown: Approval Strong in Swing States</div>
                <div class="blog-meta">
                    <span><i class="far fa-clock"></i> 5 hours ago</span>
                    <span><i class="far fa-user"></i> Sarah Analyst</span>
                </div>
            </div>

            <div class="blog-post">
                <div class="blog-title">Historical Comparison: How This President Stacks Up</div>
                <div class="blog-meta">
                    <span><i class="far fa-clock"></i> 1 day ago</span>
                    <span><i class="far fa-user"></i> Michael Historian</span>
                </div>
            </div>

            <div class="blog-post">
                <div class="blog-title">Demographic Shifts in Approval Ratings Since June</div>
                <div class="blog-meta">
                    <span><i class="far fa-clock"></i> 2 days ago</span>
                    <span><i class="far fa-user"></i> Jennifer Demographer</span>
                </div>
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="sidebar-card">
            <div class="sidebar-title"><i class="fas fa-calendar-alt"></i> Upcoming Events</div>

            <div class="blog-post">
                <div class="blog-title">June Debate: Potential Impact on Approval Ratings</div>
                <div class="blog-meta">
                    <span><i class="far fa-calendar"></i> Jun 25, 2025</span>
                </div>
            </div>

            <div class="blog-post">
                <div class="blog-title">Economic Report Release (Q2 2025)</div>
                <div class="blog-meta">
                    <span><i class="far fa-calendar"></i> Jun 28, 2025</span>
                </div>
            </div>

            <div class="blog-post">
                <div class="blog-title">Major Policy Announcement Expected</div>
                <div class="blog-meta">
                    <span><i class="far fa-calendar"></i> Jul 2, 2025</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize charts after page load
        document.addEventListener('DOMContentLoaded', function() {
            // Approval trend chart (line)
            const ctx = document.getElementById('approvalChart').getContext('2d');
            const approvalChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['May 12', 'May 21', 'May 30', 'Jun 8', 'Jun 17', 'Jun 18'],
                    datasets: [{
                        label: 'Approval Rating',
                        data: [47.2, 47.8, 48.5, 49.1, 49.3, 49.7],
                        borderColor: '#38a169',
                        backgroundColor: 'rgba(56, 161, 105, 0.1)',
                        borderWidth: 3,
                        pointRadius: 5,
                        pointBackgroundColor: '#38a169',
                        fill: true,
                        tension: 0.2
                    }, {
                        label: 'Disapproval Rating',
                        data: [49.1, 48.5, 48.0, 47.8, 47.6, 47.5],
                        borderColor: '#e53e3e',
                        backgroundColor: 'rgba(229, 62, 62, 0.1)',
                        borderWidth: 3,
                        pointRadius: 5,
                        pointBackgroundColor: '#e53e3e',
                        fill: true,
                        tension: 0.2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                boxWidth: 15,
                                padding: 20
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(0, 0, 0, 0.7)',
                            padding: 10,
                            titleFont: {
                                size: 14
                            },
                            bodyFont: {
                                size: 13
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            min: 36,
                            max: 54,
                            title: {
                                display: true,
                                text: 'Percentage'
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });

            // Time filter functionality
            const timeFilters = document.querySelectorAll('.time-filter');
            timeFilters.forEach(filter => {
                filter.addEventListener('click', function() {
                    timeFilters.forEach(f => f.classList.remove('active'));
                    this.classList.add('active');

                    // In a real app, this would update the chart data
                    // For this demo, we'll just show a message
                    const timeText = this.textContent;
                    let labels, approvalData, disapprovalData;

                    if (timeText === '1D') {
                        labels = ['6 AM', '9 AM', '12 PM', '3 PM', '6 PM'];
                        approvalData = [49.2, 49.3, 49.5, 49.7, 49.7];
                        disapprovalData = [47.8, 47.7, 47.6, 47.5, 47.5];
                    } else if (timeText === '1W') {
                        labels = ['Jun 12', 'Jun 13', 'Jun 14', 'Jun 15', 'Jun 16', 'Jun 17',
                            'Jun 18'
                        ];
                        approvalData = [47.9, 48.5, 51.2, 47.3, 49.1, 49.3, 49.7];
                        disapprovalData = [48.4, 48.0, 46.8, 49.1, 47.8, 47.6, 47.5];
                    } else if (timeText === '1M') {
                        labels = ['May 18', 'May 23', 'May 28', 'Jun 2', 'Jun 7', 'Jun 12',
                            'Jun 18'
                        ];
                        approvalData = [46.5, 47.1, 47.8, 48.3, 48.8, 47.9, 49.7];
                        disapprovalData = [49.5, 48.9, 48.2, 47.7, 47.2, 48.4, 47.5];
                    } else if (timeText === '1Y') {
                        labels = ['Jul 2024', 'Oct 2024', 'Jan 2025', 'Apr 2025', 'Jun 2025'];
                        approvalData = [42.3, 44.7, 46.2, 47.8, 49.7];
                        disapprovalData = [54.1, 51.3, 49.8, 48.2, 47.5];
                    } else if (timeText === '5Y') {
                        labels = ['2020', '2021', '2022', '2023', '2024', '2025'];
                        approvalData = [45.2, 46.8, 44.3, 43.7, 44.9, 49.7];
                        disapprovalData = [50.8, 49.2, 51.7, 52.3, 51.1, 47.5];
                    } else { // ALL
                        labels = ['2019', '2020', '2021', '2022', '2023', '2024', '2025'];
                        approvalData = [43.7, 45.2, 46.8, 44.3, 43.7, 44.9, 49.7];
                        disapprovalData = [52.3, 50.8, 49.2, 51.7, 52.3, 51.1, 47.5];
                    }

                    // Update chart
                    approvalChart.data.labels = labels;
                    approvalChart.data.datasets[0].data = approvalData;
                    approvalChart.data.datasets[1].data = disapprovalData;
                    approvalChart.update();
                });
            });

            // Table time filter functionality
            const tableTimeFilters = document.querySelectorAll('.card .time-filter');
            tableTimeFilters.forEach(filter => {
                filter.addEventListener('click', function() {
                    tableTimeFilters.forEach(f => f.classList.remove('active'));
                    this.classList.add('active');

                    // In a real app, this would filter the table data
                    alert(`Table data filtered to ${this.textContent} view`);
                });
            });

            // General filter functionality
            // document.getElementById('poll-filter').addEventListener('change', function () {
            //     const filterValue = this.value;
            //     alert(`Filtering to show: ${this.options[this.selectedIndex].text}`);
            // });
        });
    </script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#search-input').on('keyup', function() {
            let q = $(this).val().trim();
            if (!q) return $('#suggestions').empty();

            $.getJSON("{{ route('candidates.search') }}", {
                search: q
            }, function(list) {
                let html = '';
                list.forEach(item => {
                    html +=
                        `<div data-key="${item.key}" style="padding:5px;cursor:pointer;">${item.label}</div>`;
                });
                $('#suggestions').html(html);
            });
        });

        $('#suggestions').on('click', 'div', function() {
            const key = $(this).data('key');
            const label = $(this).text();
            $('#search-input').val(label);
            $('#suggestions').empty();

            const url = "{{ url('/polls/results') }}/" + key;

            $.getJSON(url, function(data) {
                const names = data.candidate_names;
                renderTableHead(names);

                let rows = '';
                data.polls.forEach(p => {
                    rows += buildRow(p.pollster_name, p.poll_date, p.sample_size, p.results);
                });
                $('#poll-table-body').html(rows);
            });
        });

        function renderTableHead(candidateNames) {
            let head = `<tr>
            <th>Pollster</th>
            <th>Date</th>
            <th>Sample</th>`;
            candidateNames.forEach(name => {
                head += `<th>${name}</th>`;
            });
            head += `<th>Net</th></tr>`;
            $('#poll-table-head').html(head);
        }

        function buildRow(pollster, date, sample, results) {
            results.sort((a, b) => b.pct - a.pct);
            const maxPct = results[0].pct;
            const runnerUp = results[1] ? results[1].pct : 0;
            const net = (maxPct - runnerUp).toFixed(1);

            let tr = `<tr>
            <td>${pollster}</td>
            <td>${date}</td>
            <td>${sample}</td>`;

            results.forEach(r => {
                const cls = (r.pct === maxPct) ? 'poll-result positive' : 'poll-result negative';
                tr += `<td class="${cls}">${r.pct}%</td>`;
            });

            const netClass = net >= 0 ? 'poll-result positive' : 'poll-result negative';
            tr += `<td class="${netClass}">+${net}%</td></tr>`;

            return tr;
        }
    </script>


    <script>
        // CSRF for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // On State change, fetch polls
        $('#state').on('change', function() {
            const sid = $(this).val();
            $('#poll-table-body').empty();

            if (!sid) return;

            $.getJSON(`/polls/by-state/${sid}`, function(list) {
                if (list.length === 0) {
                    $('#poll-table-body').html(
                        '<tr><td colspan="6">No polls found for this state.</td></tr>'
                    );
                    return;
                }

                let rows = '';
                list.forEach(p => {
                    rows += `<tr>
        <td>${p.pollster}</td>
        <td>${p.date}</td>
        <td>${p.sample}</td>
        <td class="poll-result ${p.c1class}">${p.cand1}%</td>
        <td class="poll-result ${p.c2class}">${p.cand2}%</td>
        <td class="poll-result ${p.leadClass}">${p.net >= 0? '+' : ''}${p.net}%</td>
      </tr>`;
                });

                $('#poll-table-body').html(rows);
            });
        });
    </script>
@endsection
