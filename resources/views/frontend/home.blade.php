@extends('frontend.layout')
@section('content')
    <!-- Main Content (70%) -->
    <div class="main-content">
        <!-- Filters -->
        <div class="filters">
            <!-- Race Filter -->
            <div class="filters">
                <div class="filter-group">
                    <label for="race-select"><i class="fas fa-flag"></i> Race:</label>
                    <select id="race-select">
                        <option value="">-- Select Race --</option>
                        @foreach ($races as $race)
                            <option value="{{ $race->id }}">{{ $race->race }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

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
                <div class="card-title"><i class="fas fa-table"></i> Polling Data</div>
                <div class="time-filters"></div>
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

        <div class="chart-container">
            {{-- Time filters --}}
            <div class="time-filters">
                @foreach (['1D', '1W', '1M', '1Y', '5Y', 'ALL'] as $tf)
                    <div class="time-filter {{ $tf === '1M' ? 'active' : '' }}">{{ $tf }}</div>
                @endforeach
            </div>

            {{-- Header & stats --}}
            <div class="chart-header">
                <div class="chart-title">President Approval Rating Trend</div>
                @if ($approvalStats)
                    <div class="chart-stats" style="margin-top:.5rem;">
                        <div class="stat-item">
                            <div class="stat-label">Current Approval</div>
                            <div class="stat-value positive">{{ $approvalStats['approve'] }}%</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Current Disapproval</div>
                            <div class="stat-value negative">{{ $approvalStats['disapprove'] }}%</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">Net Approval</div>
                            <div class="stat-value {{ $approvalStats['net'] >= 0 ? 'positive' : 'negative' }}">
                                {{ ($approvalStats['net'] >= 0 ? '+' : '') . $approvalStats['net'] }}%
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Canvas --}}
            <div class="chart-wrapper">
                <canvas id="approvalChart"></canvas>
            </div>
        </div>

        <!-- Polling Table -->
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-table"></i> Latest Approval Data</div>
                <div class=" approvalfilters" id="table-filters">
                    <div class=" approvalfilter active">7D</div>
                    <div class=" approvalfilter">1M</div>
                    <div class=" approvalfilter">1Y</div>
                </div>
            </div>
            <div class="polling-table-container">
                <table class="polling-table">
                    <thead>
                        <tr>
                            <th>Pollster</th>
                            <th>Date</th>
                            <th>Sample Size</th>
                            <th>Approve</th>
                            <th>Disapprove</th>
                            <th>Net</th>
                        </tr>
                    </thead>
                    <tbody id="polling-body">
                        <!-- injected by JS -->
                    </tbody>
                </table>
            </div>
        </div>


        <div class="header-section">
            <h1 class="page-title">Latest Polls</h1>
            <p class="page-subtitle">Most recent polling data from various sources across different races.</p>
        </div>

        <div class="filter-card">

            <div class="filters-container">
                <div class="filters-title">
                    <div class="poll-types-container">
                        <div class="poll-type-column">
                            <div class="poll-type-item active">
                                <i class="fas fa-user"></i> Presidential
                            </div>
                            <div class="poll-type-item">
                                <i class="fas fa-landmark"></i> Senate
                            </div>
                            <div class="poll-type-item">
                                <i class="fas fa-flag-usa"></i> House
                            </div>
                            <div class="poll-type-item">
                                <i class="fas fa-user-tie"></i> Governors
                            </div>
                        </div>
                    </div>
                </div>

                <div class="filter-row">
                    <div class="filter-option">
                        <div class="filter-label">State</div>
                        <select class="filter-select">
                            <option>All States</option>
                            <option>Arizona</option>
                            <option>Florida</option>
                            <option>Michigan</option>
                            <option>Ohio</option>
                            <option>Texas</option>
                        </select>
                    </div>

                    <div class="filter-option">
                        <div class="filter-label">Pollster</div>
                        <select class="filter-select">
                            <option>All Pollsters</option>
                            <option>Gallup</option>
                            <option>Pew Research</option>
                            <option>YouGov</option>
                            <option>Ipsos</option>
                            <option>Rasmussen</option>
                        </select>
                    </div>

                    <div class="filter-option">
                        <div class="filter-label">Timeframe</div>
                        <select class="filter-select">
                            <option>Last 7 days</option>
                            <option>Last 30 days</option>
                            <option>Last 90 days</option>
                            <option>Last 6 months</option>
                        </select>
                    </div>
                </div>

                <button class="apply-btn">Apply Filters</button>
            </div>
        </div>

        <div class="no-polls-container">
            <div class="no-polls-icon">
                <i class="fas fa-search"></i>
            </div>
            <h2 class="no-polls-title">No polls found with the selected filters</h2>
            <p class="no-polls-text">Try adjusting your filters to see more polling results.</p>
        </div>


    </div>

    <!-- Sidebar (30%) -->
    <div class="sidebar">
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
        <!-- Key Metrics -->
        <div class="sidebar-card">
            <div class="sidebar-title"><i class="fas fa-tachometer-alt"></i> Key Metrics</div>
            <div class="data-points">
                <div class="data-card">
                    <div id="avg-approval" class="data-value positive"></div>
                    <div class="data-label">Current Approval</div>
                </div>
                <div class="data-card">
                    <div id="avg-disapproval" class="data-value negative"></div>
                    <div class="data-label">Current Disapproval</div>
                </div>
                <div class="data-card">
                    <div id="avg-net" class="data-value positive"></div>
                    <div class="data-label">Net Approval</div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery & DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <script>
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var table = $('#polling-table').DataTable({
                pageLength: 10,
                lengthChange: false,
                searching: false,
                info: false,
                ordering: false,
                columns: [{
                        title: '',
                        className: 'details-control',
                        orderable: false
                    },
                    {
                        title: 'Pollster'
                    },
                    {
                        title: 'Date'
                    },
                    {
                        title: 'Sample'
                    },
                    {
                        title: '1st'
                    },
                    {
                        title: '2nd'
                    },
                    {
                        title: '3rd'
                    },
                    {
                        title: 'Net'
                    }
                ]
            });

            var allResults = [];

            function renderTableHead(names) {
                var th = '<tr>';
                th += '<th></th><th>Pollster</th><th>Date</th><th>Sample</th>';
                names.slice(0, 3).forEach(n => th += `<th>${n}</th>`);
                th += '<th>Net</th></tr>';
                $('#poll-table-head').html(th);
            }

            function buildRow(poll, idx) {
                var results = (poll.results || []).slice().sort((a, b) => b.pct - a.pct);
                allResults[idx] = results;
                var net = ((results[0]?.pct || 0) - (results[1]?.pct || 0)).toFixed(1);
                var netCls = net >= 0 ? 'positive' : 'negative';

                var row = [];
                // toggle cell
                row.push('+');
                // pollster, date, sample
                row.push(poll.pollster || poll.pollster_name || '');
                row.push(poll.date || poll.poll_date || '');
                row.push(poll.sample || poll.sample_size || '');

                // ensure exactly 3 candidate columns
                for (var j = 0; j < 3; j++) {
                    if (results[j]) {
                        var r = results[j];
                        var cls = (r.pct === results[0].pct) ?
                            'poll-result positive' :
                            'poll-result negative';
                        row.push(`<span class="${cls}">${r.pct}%</span>`);
                    } else {
                        row.push('');
                    }
                }

                // net column
                row.push(`<span class="poll-result ${netCls}">${net>=0?'+':''}${net}%</span>`);
                return row;
            }

            function loadPolls(url) {
                $.getJSON(url, function(list) {
                    if (!list || !list.length) {
                        return table.clear().draw();
                    }
                    var names = (list[0].results || []).map(r => r.name);
                    renderTableHead(names);
                    table.clear();
                    list.forEach((p, i) => table.row.add(buildRow(p, i)));
                    table.draw();
                });
            }

            // race change
            $('#race-select').change(function() {
                var v = $(this).val();
                v ? loadPolls(`/polls/by-race/${v}`) : table.clear().draw();
            });
            // state change
            $('#state').change(function() {
                var v = $(this).val();
                v ? loadPolls(`/polls/by-state/${v}`) : table.clear().draw();
            });

            // live search suggestions
            $('#search-input').on('keyup', function() {
                var q = $(this).val().trim();
                if (!q) return $('#suggestions').empty();
                $.getJSON("{{ route('candidates.search') }}", {
                    search: q
                }, function(list) {
                    var html = '';
                    list.forEach(item => html +=
                        `<div data-key="${item.key}" style="padding:5px;cursor:pointer;">${item.label}</div>`
                    );
                    $('#suggestions').html(html);
                });
            });

            // search selection
            $('#suggestions').on('click', 'div', function() {
                var key = $(this).data('key');
                $('#search-input').val($(this).text());
                $('#suggestions').empty();
                $.getJSON(`/polls/results/${key}`, function(data) {
                    renderTableHead(data.candidate_names);
                    table.clear();
                    allResults = [];
                    data.polls.forEach((p, i) => table.row.add(buildRow(p, i)));
                    table.draw();
                });
            });

            // accordion toggle
            $('#polling-table tbody').on('click', 'td.details-control', function() {
                var tr = $(this).closest('tr'),
                    row = table.row(tr),
                    idx = row.index();
                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                    $(this).text('+');
                } else {
                    var html = '<div class="row-details"><ul>';
                    (allResults[idx] || []).forEach(r => html += `<li>${r.name}: ${r.pct}%</li>`);
                    html += '</ul></div>';
                    row.child(html).show();
                    tr.addClass('shown');
                    $(this).text('−');
                }
            });

        });
    </script>


    {{-- <script>
        // CSRF header for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // On Race change → fetch polls and rebuild table
        $('#race-select').on('change', function() {
            const rid = $(this).val();
            $('#poll-table-head, #poll-table-body').empty();
            if (!rid) return;

            $.getJSON(`/polls/by-race/${rid}`, function(list) {
                if (!list.length) {
                    $('#poll-table-body').html(
                        '<tr><td colspan="6">No polls for this race.</td></tr>'
                    );
                    return;
                }

                // 1) Derive headers from first poll
                const names = list[0].results.map(r => r.name);
                let th = '<tr><th>Pollster</th><th>Date</th><th>Sample</th>';
                names.forEach(n => th += `<th>${n}</th>`);
                th += '<th>Net</th></tr>';
                $('#poll-table-head').html(th);

                // 2) Build rows
                let rows = '';
                list.forEach(p => {
                    const top = p.results[0].pct;
                    const runner = p.results[1]?.pct || 0;
                    const net = (top - runner).toFixed(1);
                    const leadClass = net >= 0 ? 'positive' : 'negative';

                    rows += `<tr>
        <td>${p.pollster}</td>
        <td>${p.date}</td>
        <td>${p.sample}</td>`;

                    p.results.forEach((r, i) => {
                        const cls = i === 0 ? 'poll-result positive' :
                            'poll-result negative';
                        rows += `<td class="${cls}">${r.pct}%</td>`;
                    });

                    rows += `<td class="poll-result ${leadClass}">` +
                        `${net>=0?'+':''}${net}%` +
                        `</td></tr>`;
                });
                $('#poll-table-body').html(rows);
            });
        });
    </script>

    <script>
        $('#state').on('change', function() {
            const sid = $(this).val();
            $('#poll-table-head, #poll-table-body').empty();
            if (!sid) return;

            $.getJSON(`/polls/by-state/${sid}`, function(list) {
                if (list.length === 0) {
                    $('#poll-table-body').html(
                        '<tr><td colspan="6">No polls found for this state.</td></tr>'
                    );
                    return;
                }

                // 1) Build header from first poll's result names
                const names = list[0].results.map(r => r.name);
                let th = `<tr>
        <th>Pollster</th>
        <th>Date</th>
        <th>Sample</th>`;
                names.forEach(n => th += `<th>${n}</th>`);
                th += `<th>Net</th></tr>`;
                $('#poll-table-head').html(th);

                // 2) Build body rows
                let rows = '';
                list.forEach(p => {
                    const top = p.results[0].pct;
                    const runner = p.results[1]?.pct || 0;
                    const net = (top - runner).toFixed(1);
                    const leadClass = net >= 0 ? 'positive' : 'negative';

                    rows += `<tr>
          <td>${p.pollster}</td>
          <td>${p.date}</td>
          <td>${p.sample}</td>`;

                    p.results.forEach((r, i) => {
                        const cls = (i === 0) ? 'poll-result positive' :
                            'poll-result negative';
                        rows += `<td class="${cls}">${r.pct}%</td>`;
                    });

                    rows += `<td class="poll-result ${leadClass}">${net >= 0 ? '+' : ''}${net}%</td>
        </tr>`;
                });

                $('#poll-table-body').html(rows);
            });
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
    </script> --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1) Data from server
            const rawDates = @json($rawDates); // ["2025-06-17", ...]
            const fullLabels = @json($labels); // ["Jun 17", ...]
            const fullApprove = @json($approvalData); // [47.2, ...]
            const fullDisapprove = @json($disapprovalData);

            // 2) Setup Chart.js
            const ctx = document.getElementById('approvalChart').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: fullLabels,
                    datasets: [{
                            label: 'Approval Rating',
                            data: fullApprove,
                            borderColor: '#38a169',
                            backgroundColor: 'rgba(56,161,105,0.1)',
                            borderWidth: 3,
                            pointRadius: 5,
                            fill: true,
                            tension: 0.2
                        },
                        {
                            label: 'Disapproval Rating',
                            data: fullDisapprove,
                            borderColor: '#e53e3e',
                            backgroundColor: 'rgba(229,62,62,0.1)',
                            borderWidth: 3,
                            pointRadius: 5,
                            fill: true,
                            tension: 0.2
                        }
                    ]
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
                            callbacks: {
                                label(ctx) {
                                    return ctx.dataset.label + ': ' + ctx.formattedValue + '%';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            type: 'category',
                            title: {
                                display: true,
                                text: 'Date'
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            min: 0,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Percentage'
                            },
                            ticks: {
                                callback: v => v + '%'
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

            // 3) Average helper
            function average(arr) {
                if (!Array.isArray(arr) || arr.length === 0) return 0;
                let sum = 0;
                for (const v of arr) {
                    sum += Number(v) || 0;
                }
                return sum / arr.length;
            }

            // 4) Filter helper (does NOT mutate now)
            function filterBy(tf) {
                const now = new Date();
                let thresh;
                switch (tf) {
                    case '1D':
                        thresh = new Date(+now - 1 * 24 * 60 * 60 * 1000);
                        break;
                    case '1W':
                        thresh = new Date(+now - 7 * 24 * 60 * 60 * 1000);
                        break;
                    case '1M':
                        thresh = new Date(now.getFullYear(), now.getMonth() - 1, now.getDate());
                        break;
                    case '1Y':
                        thresh = new Date(now.getFullYear() - 1, now.getMonth(), now.getDate());
                        break;
                    case '5Y':
                        thresh = new Date(now.getFullYear() - 5, now.getMonth(), now.getDate());
                        break;
                    default:
                        thresh = new Date(0);
                }

                const labs = [],
                    apps = [],
                    diss = [];
                rawDates.forEach((d, i) => {
                    if (new Date(d) >= thresh) {
                        labs.push(fullLabels[i]);
                        apps.push(fullApprove[i]);
                        diss.push(fullDisapprove[i]);
                    }
                });

                console.log(`filterBy(${tf}) →`, {
                    labs,
                    apps,
                    diss
                });
                return [labs, apps, diss];
            }

            // 5) Update sidebar metrics
            function updateMetrics(appArr, disArr) {
                console.log('updateMetrics inputs:', {
                    appArr,
                    disArr
                });
                const avgApp = average(appArr);
                const avgDis = average(disArr);
                const net = avgApp - avgDis;
                console.log('computed avgs:', {
                    avgApp,
                    avgDis,
                    net
                });

                const aApp = avgApp.toFixed(1);
                const aDis = avgDis.toFixed(1);
                const aNet = net.toFixed(1);
                const sign = net >= 0 ? '+' : '';

                document.getElementById('avg-approval').textContent = `${aApp}%`;
                document.getElementById('avg-disapproval').textContent = `${aDis}%`;

                const netEl = document.getElementById('avg-net');
                netEl.textContent = `${sign}${aNet}%`;
                netEl.classList.toggle('positive', net >= 0);
                netEl.classList.toggle('negative', net < 0);
            }

            // 6) Initial draw using the “active” filter (defaults to 1D)
            const initialTF = document.querySelector('.time-filter.active').textContent;
            const [initLab, initApp, initDis] = filterBy(initialTF);
            chart.data.labels = initLab;
            chart.data.datasets[0].data = initApp;
            chart.data.datasets[1].data = initDis;
            chart.update();
            updateMetrics(initApp, initDis);

            // 7) Wire up the buttons
            document.querySelectorAll('.time-filter').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.time-filter').forEach(f => f.classList.remove(
                        'active'));
                    btn.classList.add('active');

                    const tf = btn.textContent;
                    const [labs, apps, diss] = filterBy(tf);

                    chart.data.labels = labs;
                    chart.data.datasets[0].data = apps;
                    chart.data.datasets[1].data = diss;
                    chart.update();
                    updateMetrics(apps, diss);
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1) Bring the PHP data into JS
            const latestApprovals = @json($latestApprovals);

            // 2) Helpers
            function filterRecords(tf) {
                const now = new Date();
                let thresh;
                switch (tf) {
                    case '7D':
                        thresh = new Date(+now - 7 * 24 * 60 * 60 * 1000);
                        break;
                    case '1M':
                        thresh = new Date(now.getFullYear(), now.getMonth() - 1, now.getDate());
                        break;
                    case '1Y':
                        thresh = new Date(now.getFullYear() - 1, now.getMonth(), now.getDate());
                        break;
                    default:
                        thresh = new Date(0);
                }
                return latestApprovals.filter(r => new Date(r.rawDate) >= thresh);
            }

            function renderTable(records) {
                const body = document.getElementById('polling-body');
                if (!body) return;

                body.innerHTML = records.map(r => `
                <tr>
                    <td>${r.pollster}</td>
                    <td>${r.displayDate}</td>
                    <td>${r.sampleSize}</td>
                    <td class="poll-result ${r.approve >= r.disapprove ? 'positive' : 'negative'}">
                    ${r.approve}%
                    </td>
                    <td class="poll-result ${r.disapprove > r.approve ? 'negative' : 'positive'}">
                    ${r.disapprove}%
                    </td>
                    <td class="poll-result ${r.net >= 0 ? 'positive' : 'negative'}">
                    ${r.net >= 0 ? '+' + r.net : r.net}%
                    </td>
                </tr>
                `).join('');
            }

            // 3) Wire up the buttons
            const buttons = document.querySelectorAll('#table-filters .approvalfilter');
            buttons.forEach(btn => {
                btn.addEventListener('click', () => {
                    buttons.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    renderTable(filterRecords(btn.textContent));
                });
            });

            // 4) Initial render (7 days)
            renderTable(filterRecords('7D'));
        });
    </script>


 <script>
        // Add active state to poll type items
        document.querySelectorAll('.poll-type-item').forEach(item => {
            item.addEventListener('click', function () {
                document.querySelectorAll('.poll-type-item').forEach(i => {
                    i.classList.remove('active');
                });
                this.classList.add('active');
            });
        });

        // Apply filters button functionality
        document.querySelector('.apply-btn').addEventListener('click', function () {
            const activePollType = document.querySelector('.poll-type-item.active').textContent.trim();
            const state = document.querySelector('.filter-select:nth-child(1)').value;
            const pollster = document.querySelector('.filter-select:nth-child(2)').value;
            const timeframe = document.querySelector('.filter-select:nth-child(3)').value;

            alert(`Filters applied:\nPoll Type: ${activePollType}\nState: ${state}\nPollster: ${pollster}\nTimeframe: ${timeframe}`);
        });
    </script>
@endsection
