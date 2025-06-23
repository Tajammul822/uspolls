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
                            <option value="{{ $race->race_type }}">{{ $race->race_type }}</option>
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
        {{-- <div class="card">
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
        </div> --}}

        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-table"></i> Polling Data</div>
                <div class="time-filters"></div>
            </div>
            <div class="polling-table-container">
                <table class="polling-table" id="polling-table" style="width:100%">
                    <thead id="poll-table-head">
                        <tr>
                            <th></th>
                            <th>Pollster</th>
                            <th>Date</th>
                            <th>Sample</th>
                            <!-- Three “generic” result columns; we can leave headers blank or label “Result” -->
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>Net</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dynamically injected rows (including group headers and poll rows) -->
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
            <p class="page-subtitle">
                Most recent polling data from various sources across different races.
            </p>
        </div>

        <div class="filter-card">
            <div class="filters-container">
                <div class="filters-title">
                    <div class="poll-types-container">
                        <div class="poll-type-column">
                            <div class="poll-type-item active">
                                <i class="fas fa-user"></i> President
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
                        <select name="state_id" class="filter-select">
                            <option value="">All States</option>
                            @foreach ($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-option">
                        <select name="pollster_id" class="filter-select">
                            <option value="">All Pollsters</option>
                            @foreach ($pollesters as $pollester)
                                <option value="{{ $pollester->id }}">{{ $pollester->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-option">
                        <select name="timeframe" class="filter-select">
                            <option value="7">Last 7 days</option>
                            <option value="30">Last 30 days</option>
                            <option value="90">Last 90 days</option>
                            <option value="180">Last 6 months</option>
                        </select>
                    </div>
                </div>

                <button class="apply-btn">Apply Filters</button>
            </div>
        </div>
        {{-- 
    
        <div class="table-container">
            <table class="polls-table">
                <thead id="poll-table-head"></thead>
                <tbody id="poll-table-body"></tbody>
            </table>
            <div id="pagination" class="pagination-container"></div>
        </div> --}}

        <div class="no-polls-container">
            <div class="no-polls-icon"><i class="fas fa-search"></i></div>
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

    {{-- <script>
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
    </script> --}}

    {{-- <script>
        $(function() {
            // 1) Initialize DataTable once with fixed columns
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
                        title: ''
                    }, // result col 1
                    {
                        title: ''
                    }, // result col 2
                    {
                        title: ''
                    }, // result col 3
                    {
                        title: 'Net'
                    }
                ]
            });

            var allResults = []; // array of arrays, each is sorted results for a poll row

            // Build a poll row aligned under headerNames (array length 3)
            function buildPollRow(poll, headerNames, pollIdx) {
                // poll.results: [ { name, pct }, ... ]
                // Copy and sort descending for net and detail:
                var results = (poll.results || []).map(r => ({
                    name: r.name,
                    pct: parseFloat(r.pct) || 0
                }));
                results.sort((a, b) => b.pct - a.pct);
                allResults[pollIdx] = results;

                // Compute net = top - second
                var topPct = results[0]?.pct || 0;
                var secondPct = results[1]?.pct || 0;
                var netVal = (topPct - secondPct).toFixed(1);
                var netCls = (topPct - secondPct) >= 0 ? 'positive' : 'negative';
                var netDisplay = ((topPct - secondPct) >= 0 ? '+' : '') + netVal + '%';

                var row = [];
                row.push('+'); // toggle cell
                row.push(poll.pollster || '');
                row.push(poll.date || '');
                row.push(poll.sample || '');

                // For each of the 3 headerNames, put that candidate's pct or blank
                headerNames.forEach(name => {
                    var found = results.find(r => r.name === name);
                    if (found) {
                        var cls = (found.pct === topPct) ? 'poll-result positive' : 'poll-result negative';
                        row.push(`<span class="${cls}">${found.pct}%</span>`);
                    } else {
                        row.push('');
                    }
                });
                // Pad if fewer than 3 (unlikely because we always supply length-3 array)
                for (var k = headerNames.length; k < 3; k++) {
                    row.push('');
                }
                row.push(`<span class="poll-result ${netCls}">${netDisplay}</span>`);
                return row;
            }

            // Main: load polls from an endpoint that returns JSON array of polls
            function loadPolls(url) {
                $.getJSON(url, function(list) {
                    // Clear existing rows
                    table.clear().draw();
                    allResults = [];

                    if (!list || !list.length) {
                        return;
                    }
                    // Group by race_id in order of appearance
                    var groups = [];
                    var seen = {};
                    list.forEach(poll => {
                        var rid = poll.race_id;
                        if (!seen[rid]) {
                            seen[rid] = {
                                race_id: rid,
                                race_label: poll.race_label || ('Race ' + rid),
                                polls: []
                            };
                            groups.push(seen[rid]);
                        }
                        seen[rid].polls.push(poll);
                    });

                    var pollGlobalIdx = 0; // index for allResults

                    // Insert group-header row and poll rows for each group
                    groups.forEach(group => {
                        var polls = group.polls;

                        // Compute top-3 candidate names by average pct in this group
                        var stats = {}; // name -> { sum, count }
                        polls.forEach(poll => {
                            (poll.results || []).forEach(r => {
                                var name = r.name,
                                    pct = parseFloat(r.pct) || 0;
                                if (!stats[name]) stats[name] = {
                                    sum: 0,
                                    count: 0
                                };
                                stats[name].sum += pct;
                                stats[name].count++;
                            });
                        });
                        var avgs = Object.entries(stats).map(([name, sc]) => ({
                            name,
                            avg: sc.sum / sc.count
                        }));
                        avgs.sort((a, b) => b.avg - a.avg);
                        var headerNames = avgs.slice(0, 3).map(x => x.name);
                        // Pad to length 3 if fewer:
                        for (var k = headerNames.length; k < 3; k++) {
                            headerNames.push('');
                        }

                        // 1) Insert group-header row: ['', race_label, '', '', cand1, cand2, cand3, '']
                        var ghRow = [
                            '',
                            group.race_label,
                            '',
                            '',
                            headerNames[0],
                            headerNames[1],
                            headerNames[2],
                            ''
                        ];
                        var ghNode = table.row.add(ghRow).draw(false).node();
                        $(ghNode).addClass('group-header');

                        // 2) Insert each poll row, storing data-poll-row-index on its <tr>
                        polls.forEach(poll => {
                            var rowArr = buildPollRow(poll, headerNames, pollGlobalIdx);
                            var node = table.row.add(rowArr).draw(false).node();
                            $(node).addClass('poll-row');
                            $(node).attr('data-poll-row-index', pollGlobalIdx);
                            pollGlobalIdx++;
                        });
                    });

                    // After rows inserted, bind toggle click
                    $('#polling-table tbody').off('click').on('click', 'td.details-control', function() {
                        var tr = $(this).closest('tr');
                        // If this is a group-header, do nothing
                        if ($(tr).hasClass('group-header')) return;

                        var row = table.row(tr);
                        var pollIdx = parseInt($(tr).attr('data-poll-row-index'), 10);
                        if (row.child.isShown()) {
                            row.child.hide();
                            tr.removeClass('shown');
                            $(this).text('+');
                        } else {
                            // Build detail list from allResults[pollIdx]
                            var details = allResults[pollIdx] || [];
                            var html = '<div class="row-details"><ul>';
                            details.forEach(r => {
                                html += `<li>${r.name}: ${r.pct}%</li>`;
                            });
                            html += '</ul></div>';
                            row.child(html).show();
                            tr.addClass('shown');
                            $(this).text('−');
                        }
                    });
                });
            }

            // Load by race type when race-select changes
            $('#race-select').change(function() {
                var v = $(this).val();
                if (v) {
                    loadPolls(`/polls/by-race/${v}`);
                } else {
                    table.clear().draw();
                }
            });

            // Load by state when state-select changes
            $('#state').change(function() {
                var v = $(this).val();
                if (v) {
                    loadPolls(`/polls/by-state/${v}`);
                } else {
                    table.clear().draw();
                }
            });

            // Live search suggestions and selecting a candidate
            $('#search-input').on('keyup', function() {
                var q = $(this).val().trim();
                if (!q) {
                    return $('#suggestions').empty();
                }
                $.getJSON("{{ route('candidates.search') }}", {
                    search: q
                }, function(list) {
                    var html = '';
                    list.forEach(item => {
                        html +=
                            `<div data-key="${item.key}" style="padding:5px;cursor:pointer;">${item.label}</div>`;
                    });
                    $('#suggestions').html(html);
                });
            });
            $('#suggestions').on('click', 'div', function() {
                var key = $(this).data('key');
                $('#search-input').val($(this).text());
                $('#suggestions').empty();
                // Call your search endpoint which returns { polls: [...] } or just [...]
                $.getJSON(`/polls/results/${key}`, function(data) {
                    // If your endpoint returns { polls: [...] }, extract:
                    var list = Array.isArray(data) ? data : (data.polls || []);
                    if (!Array.isArray(list)) list = [];
                    if (list.length) {
                        loadPolls(null); // clear first
                        // Directly feed the list into the grouping logic:
                        // We can reuse loadPolls logic by temporarily hijacking $.getJSON replacement,
                        // but simpler: refactor grouping into a separate function that takes the array directly.
                        // For brevity, we’ll call a helper:
                        renderFromList(list);
                    } else {
                        table.clear().draw();
                    }
                });
            });

            // Extract grouping/render logic into a reusable function
            function renderFromList(list) {
                // Same as loadPolls but using the provided list instead of URL fetch
                table.clear().draw();
                allResults = [];
                if (!list || !list.length) return;

                // Group by race_id
                var groups = [];
                var seen = {};
                list.forEach(poll => {
                    var rid = poll.race_id;
                    if (!seen[rid]) {
                        seen[rid] = {
                            race_id: rid,
                            race_label: poll.race_label || ('Race ' + rid),
                            polls: []
                        };
                        groups.push(seen[rid]);
                    }
                    seen[rid].polls.push(poll);
                });

                var pollGlobalIdx = 0;
                groups.forEach(group => {
                    var polls = group.polls;
                    // Compute top-3 candidate names
                    var stats = {};
                    polls.forEach(poll => {
                        (poll.results || []).forEach(r => {
                            var name = r.name,
                                pct = parseFloat(r.pct) || 0;
                            if (!stats[name]) stats[name] = {
                                sum: 0,
                                count: 0
                            };
                            stats[name].sum += pct;
                            stats[name].count++;
                        });
                    });
                    var avgs = Object.entries(stats).map(([name, sc]) => ({
                        name,
                        avg: sc.sum / sc.count
                    }));
                    avgs.sort((a, b) => b.avg - a.avg);
                    var headerNames = avgs.slice(0, 3).map(x => x.name);
                    for (var k = headerNames.length; k < 3; k++) headerNames.push('');

                    // Insert group-header
                    var ghRow = [
                        '',
                        group.race_label,
                        '',
                        '',
                        headerNames[0],
                        headerNames[1],
                        headerNames[2],
                        ''
                    ];
                    var ghNode = table.row.add(ghRow).draw(false).node();
                    $(ghNode).addClass('group-header');

                    // Insert poll rows
                    polls.forEach(poll => {
                        var rowArr = buildPollRow(poll, headerNames, pollGlobalIdx);
                        var node = table.row.add(rowArr).draw(false).node();
                        $(node).addClass('poll-row');
                        $(node).attr('data-poll-row-index', pollGlobalIdx);
                        pollGlobalIdx++;
                    });
                });

                // Re-bind toggle click
                $('#polling-table tbody').off('click').on('click', 'td.details-control', function() {
                    var tr = $(this).closest('tr');
                    if ($(tr).hasClass('group-header')) return;
                    var row = table.row(tr);
                    var pollIdx = parseInt($(tr).attr('data-poll-row-index'), 10);
                    if (row.child.isShown()) {
                        row.child.hide();
                        tr.removeClass('shown');
                        $(this).text('+');
                    } else {
                        var details = allResults[pollIdx] || [];
                        var html = '<div class="row-details"><ul>';
                        details.forEach(r => {
                            html += `<li>${r.name}: ${r.pct}%</li>`;
                        });
                        html += '</ul></div>';
                        row.child(html).show();
                        tr.addClass('shown');
                        $(this).text('−');
                    }
                });
            }

            // Helper: when you call loadPolls(url), it does $.getJSON and grouping.
            // For search which returns data directly, call renderFromList(list).
        });
    </script> --}}

    {{-- <script>
        $(function() {
            // Initialize DataTable once
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
                        title: ''
                    },
                    {
                        title: ''
                    },
                    {
                        title: ''
                    },
                    {
                        title: 'Net'
                    }
                ]
            });

            var allResults = []; // stores sorted results arrays for detail toggles

            // Build a poll row aligned under headerNames (length 3)
            function buildPollRow(poll, headerNames, pollIdx) {
                var results = (poll.results || []).map(r => ({
                    name: r.name,
                    pct: parseFloat(r.pct) || 0
                }));
                results.sort((a, b) => b.pct - a.pct);
                allResults[pollIdx] = results;

                var topPct = results[0]?.pct || 0;
                var secondPct = results[1]?.pct || 0;
                var netVal = (topPct - secondPct).toFixed(1);
                var netCls = (topPct - secondPct) >= 0 ? 'positive' : 'negative';
                var netDisplay = ((topPct - secondPct) >= 0 ? '+' : '') + netVal + '%';

                var row = [];
                row.push('+');
                row.push(poll.pollster || '');
                row.push(poll.date || '');
                row.push(poll.sample || '');

                headerNames.forEach(name => {
                    var found = results.find(r => r.name === name);
                    if (found) {
                        var cls = (found.pct === topPct) ? 'poll-result positive' : 'poll-result negative';
                        row.push(`<span class="${cls}">${found.pct}%</span>`);
                    } else {
                        row.push('');
                    }
                });
                // pad if fewer than 3 (though headerNames always length 3)
                for (var k = headerNames.length; k < 3; k++) {
                    row.push('');
                }
                row.push(`<span class="poll-result ${netCls}">${netDisplay}</span>`);
                return row;
            }

            // Core rendering: given an array of poll objects, group by race_id and insert rows
            function renderFromList(list) {
                table.clear().draw();
                allResults = [];
                if (!list || !list.length) return;

                // Group by race_id in order
                var groups = [],
                    seen = {};
                list.forEach(poll => {
                    var rid = poll.race_id;
                    if (!seen[rid]) {
                        seen[rid] = {
                            race_id: rid,
                            race_label: poll.race_label || ('Race ' + rid),
                            polls: []
                        };
                        groups.push(seen[rid]);
                    }
                    seen[rid].polls.push(poll);
                });

                var pollGlobalIdx = 0;
                groups.forEach(group => {
                    var polls = group.polls;
                    // Compute top 3 candidate names by average pct
                    var stats = {};
                    polls.forEach(poll => {
                        (poll.results || []).forEach(r => {
                            var name = r.name,
                                pct = parseFloat(r.pct) || 0;
                            if (!stats[name]) stats[name] = {
                                sum: 0,
                                count: 0
                            };
                            stats[name].sum += pct;
                            stats[name].count++;
                        });
                    });
                    var avgs = Object.entries(stats).map(([name, sc]) => ({
                        name,
                        avg: sc.sum / sc.count
                    }));
                    avgs.sort((a, b) => b.avg - a.avg);
                    var headerNames = avgs.slice(0, 3).map(x => x.name);
                    for (var k = headerNames.length; k < 3; k++) headerNames.push('');

                    // Insert group-header row: ['', race_label, '', '', cand1, cand2, cand3, '']
                    var ghRow = [
                        '',
                        group.race_label,
                        '',
                        '',
                        headerNames[0],
                        headerNames[1],
                        headerNames[2],
                        ''
                    ];
                    var ghNode = table.row.add(ghRow).draw(false).node();
                    $(ghNode).addClass('group-header');

                    // Insert poll rows
                    polls.forEach(poll => {
                        var rowArr = buildPollRow(poll, headerNames, pollGlobalIdx);
                        var node = table.row.add(rowArr).draw(false).node();
                        $(node).addClass('poll-row');
                        $(node).attr('data-poll-row-index', pollGlobalIdx);
                        pollGlobalIdx++;
                    });
                });

                // Bind detail toggle
                $('#polling-table tbody').off('click').on('click', 'td.details-control', function() {
                    var tr = $(this).closest('tr');
                    if ($(tr).hasClass('group-header')) return;
                    var row = table.row(tr);
                    var pollIdx = parseInt($(tr).attr('data-poll-row-index'), 10);
                    if (row.child.isShown()) {
                        row.child.hide();
                        tr.removeClass('shown');
                        $(this).text('+');
                    } else {
                        var details = allResults[pollIdx] || [];
                        var html = '<div class="row-details"><ul>';
                        details.forEach(r => {
                            html += `<li>${r.name}: ${r.pct}%</li>`;
                        });
                        html += '</ul></div>';
                        row.child(html).show();
                        tr.addClass('shown');
                        $(this).text('−');
                    }
                });
            }

            // Load by race type
            $('#race-select').change(function() {
                var v = $(this).val();
                if (v) {
                    $.getJSON(`/polls/by-race/${v}`, function(list) {
                        renderFromList(list);
                    });
                } else {
                    table.clear().draw();
                }
            });

            // Load by state
            $('#state').change(function() {
                var v = $(this).val();
                if (v) {
                    $.getJSON(`/polls/by-state/${v}`, function(list) {
                        renderFromList(list);
                    });
                } else {
                    table.clear().draw();
                }
            });

            // Live search suggestions
            $('#search-input').on('keyup', function() {
                var q = $(this).val().trim();
                if (!q) {
                    $('#suggestions').empty();
                    return;
                }
                $.getJSON("{{ route('candidates.search') }}", {
                    search: q
                }, function(list) {
                    var html = '';
                    list.forEach(item => {
                        html +=
                            `<div data-key="${item.key}" style="padding:5px;cursor:pointer;">${item.label}</div>`;
                    });
                    $('#suggestions').html(html);
                });
            });

            // Click on suggestion
            $('#suggestions').on('click', 'div', function() {
                var key = $(this).data('key');
                $('#search-input').val($(this).text());
                $('#suggestions').empty();
                $.getJSON(`/polls/results/${key}`, function(data) {
                    var list = Array.isArray(data) ? data : (data.polls || []);
                    if (!Array.isArray(list)) list = [];
                    renderFromList(list);
                }).fail(function() {
                    table.clear().draw();
                });
            });
        });
    </script> --}}

    <script>
        $(function() {
            // Initialize DataTable once
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
                        title: ''
                    },
                    {
                        title: ''
                    },
                    {
                        title: ''
                    },
                    {
                        title: 'Net'
                    }
                ]
            });
            var allResults = [];

            // Party → color mapping
            function getPartyColor(party) {
                if (!party) return 'gray';
                const p = party.toLowerCase();
                if (p.includes('democrat')) return 'blue';
                if (p.includes('republican')) return 'red';
                if (p.includes('libertarian')) return '#F39835';
                if (p.includes('green')) return 'green';
                // add more as needed
                return 'gray';
            }

            // Build one poll row aligned under headerNames (length 3)
            function buildPollRow(poll, headerNames, pollIdx) {
                // poll.results entries: { name, pct, party }
                var results = (poll.results || []).map(r => ({
                    name: r.name,
                    pct: parseFloat(r.pct) || 0,
                    party: r.party || ''
                }));
                results.sort((a, b) => b.pct - a.pct);
                allResults[pollIdx] = results;

                var top = results[0] || {
                    pct: 0,
                    party: ''
                };
                var second = results[1] || {
                    pct: 0,
                    party: ''
                };
                var netVal = (top.pct - second.pct).toFixed(1);
                var netDisplay = ((top.pct - second.pct) >= 0 ? '▲' : '') + netVal + '%';
                var netColor = getPartyColor(top.party);

                var row = [];
                row.push('+');
                row.push(poll.pollster || '');
                row.push(poll.date || '');
                row.push(poll.sample || '');

                headerNames.forEach(name => {
                    var found = results.find(r => r.name === name);
                    if (found) {
                        var color = getPartyColor(found.party);
                        var signClass = (found.pct === top.pct) ? 'poll-result positive' :
                            'poll-result negative';
                        row.push(`<span class="${signClass}" style="color:${color}">${found.pct}%</span>`);
                    } else {
                        row.push('');
                    }
                });
                // pad if fewer than 3
                for (var k = headerNames.length; k < 3; k++) row.push('');
                row.push(`<span class="poll-result" style="color:${netColor}">${netDisplay}</span>`);
                return row;
            }

            // Core: render from a list of poll objects
            function renderFromList(list) {
                table.clear().draw();
                allResults = [];
                if (!list || !list.length) return;

                // Group by race_id in order of appearance
                var groups = [],
                    seen = {};
                list.forEach(poll => {
                    var rid = poll.race_id;
                    if (!seen[rid]) {
                        seen[rid] = {
                            race_id: rid,
                            race_label: poll.race_label || ('Race ' + rid),
                            polls: []
                        };
                        groups.push(seen[rid]);
                    }
                    seen[rid].polls.push(poll);
                });

                var pollGlobalIdx = 0;
                groups.forEach(group => {
                    var polls = group.polls;
                    // Compute top-3 candidate names by average pct
                    var stats = {};
                    polls.forEach(poll => {
                        (poll.results || []).forEach(r => {
                            var name = r.name,
                                pct = parseFloat(r.pct) || 0;
                            if (!stats[name]) stats[name] = {
                                sum: 0,
                                count: 0
                            };
                            stats[name].sum += pct;
                            stats[name].count++;
                        });
                    });
                    var avgs = Object.entries(stats).map(([name, sc]) => ({
                        name,
                        avg: sc.sum / sc.count
                    }));
                    avgs.sort((a, b) => b.avg - a.avg);
                    var headerNames = avgs.slice(0, 3).map(x => x.name);
                    for (var k = headerNames.length; k < 3; k++) headerNames.push('');

                    // Determine party for each headerName (first occurrence in polls)
                    var headerParties = headerNames.map(name => {
                        if (!name) return '';
                        for (let poll of polls) {
                            let found = (poll.results || []).find(r => r.name === name);
                            if (found && found.party) return found.party;
                        }
                        return '';
                    });
                    // Build colored candidate-name cells
                    var candCells = headerNames.map((name, i) => {
                        if (!name) return '';
                        var color = getPartyColor(headerParties[i]);
                        return `<span style="color:${color}">${name}</span>`;
                    });

                    // Insert group-header row: ['', race_label, '', '', cand1, cand2, cand3, '']
                    var ghRow = [
                        '',
                        group.race_label,
                        '',
                        '',
                        candCells[0],
                        candCells[1],
                        candCells[2],
                        ''
                    ];
                    var ghNode = table.row.add(ghRow).draw(false).node();
                    $(ghNode).addClass('group-header');

                    // Insert poll rows
                    polls.forEach(poll => {
                        var rowArr = buildPollRow(poll, headerNames, pollGlobalIdx);
                        var node = table.row.add(rowArr).draw(false).node();
                        $(node).addClass('poll-row').attr('data-poll-row-index', pollGlobalIdx);
                        pollGlobalIdx++;
                    });
                });

                // Bind detail toggle
                $('#polling-table tbody').off('click').on('click', 'td.details-control', function() {
                    var tr = $(this).closest('tr');
                    if ($(tr).hasClass('group-header')) return;
                    var row = table.row(tr);
                    var idx = parseInt($(tr).attr('data-poll-row-index'), 10);
                    if (row.child.isShown()) {
                        row.child.hide();
                        tr.removeClass('shown');
                        $(this).text('+');
                    } else {
                        var details = allResults[idx] || [];
                        var html = '<div class="row-details"><ul>';
                        details.forEach(r => {
                            var color = getPartyColor(r.party);
                            html +=
                                `<li><span style="color:${color}">${r.name}</span>: ${r.pct}%</li>`;
                        });
                        html += '</ul></div>';
                        row.child(html).show();
                        tr.addClass('shown');
                        $(this).text('−');
                    }
                });
            }

            // Load by race type
            $('#race-select').change(function() {
                var v = $(this).val();
                if (v) {
                    $.getJSON(`/polls/by-race/${v}`, renderFromList)
                        .fail(() => table.clear().draw());
                } else {
                    table.clear().draw();
                }
            });
            // Load by state
            $('#state').change(function() {
                var v = $(this).val();
                if (v) {
                    $.getJSON(`/polls/by-state/${v}`, renderFromList)
                        .fail(() => table.clear().draw());
                } else {
                    table.clear().draw();
                }
            });
            // Search suggestions
            $('#search-input').on('keyup', function() {
                var q = $(this).val().trim();
                if (!q) {
                    $('#suggestions').empty();
                    return;
                }
                $.getJSON("{{ route('candidates.search') }}", {
                    search: q
                }, function(list) {
                    var html = '';
                    list.forEach(item => {
                        html +=
                            `<div data-key="${item.key}" style="padding:5px;cursor:pointer;">${item.label}</div>`;
                    });
                    $('#suggestions').html(html);
                });
            });
            // Click suggestion
            $('#suggestions').on('click', 'div', function() {
                var key = $(this).data('key');
                $('#search-input').val($(this).text());
                $('#suggestions').empty();
                $.getJSON(`/polls/results/${key}`, function(resp) {
                    var list = Array.isArray(resp) ? resp : (resp.polls || []);
                    renderFromList(list);
                }).fail(() => table.clear().draw());
            });
        });
    </script>

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

                // console.log(`filterBy(${tf}) →`, {
                //     labs,
                //     apps,
                //     diss
                // });
                return [labs, apps, diss];
            }

            // 5) Update sidebar metrics
            function updateMetrics(appArr, disArr) {
                // console.log('updateMetrics inputs:', {
                //     appArr,
                //     disArr
                // });
                const avgApp = average(appArr);
                const avgDis = average(disArr);
                const net = avgApp - avgDis;
                // console.log('computed avgs:', {
                //     avgApp,
                //     avgDis,
                //     net
                // });

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
            item.addEventListener('click', function() {
                document.querySelectorAll('.poll-type-item').forEach(i => {
                    i.classList.remove('active');
                });
                this.classList.add('active');
            });
        });

        // Apply filters button functionality
        document.querySelector('.apply-btn').addEventListener('click', function() {
            const activePollType = document.querySelector('.poll-type-item.active').textContent.trim();
            const state = document.querySelector('.filter-select:nth-child(1)').value;
            const pollster = document.querySelector('.filter-select:nth-child(2)').value;
            const timeframe = document.querySelector('.filter-select:nth-child(3)').value;


            console.log(activePollType);
            alert(
                `Filters applied:\nPoll Type: ${activePollType}\nState: ${state}\nPollster: ${pollster}\nTimeframe: ${timeframe}`
            );
        });
    </script>

    {{-- <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // ... your poll-type click handler stays the same ...

        document.querySelector('.apply-btn').addEventListener('click', async () => {
            const activePollType = document.querySelector('.poll-type-item.active').textContent.trim();
            const [stateSelect, pollsterSelect, timeframeSelect] = document.querySelectorAll('.filter-select');

            // if value is "", we send null
            const state_id = stateSelect.value === "" ? null : parseInt(stateSelect.value);
            const pollster_id = pollsterSelect.value === "" ? null : parseInt(pollsterSelect.value);
            // timeframeSelect.value is like "7", "30", etc.
            const days = parseInt(timeframeSelect.value);

            try {
                const res = await fetch("{{ route('polls.filter') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        pollType: activePollType,
                        state_id: state_id,
                        pollster_id: pollster_id,
                        timeframe: days,
                    })
                });

                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const data = await res.json();
                console.log('Filtered polls:', data);
              
            } catch (err) {
               
            }
        });
    </script> --}}

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            document.querySelector('.apply-btn').addEventListener('click', async () => {
                // --- 1) Gather filters ---
                const activeTab = document.querySelector('.poll-type-item.active');
                const pollType = activeTab ?
                    activeTab.textContent.trim() :
                    'President';

                const stateId = +document.querySelector('select[name="state_id"]').value || null;
                const pollsterId = +document.querySelector('select[name="pollster_id"]').value || null;
                const timeframe = +document.querySelector('select[name="timeframe"]').value;

                // --- 2) Fetch AJAX ---
                let data = [];
                try {
                    const res = await fetch("{{ route('polls.filter') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            pollType,
                            state_id: stateId,
                            pollster_id: pollsterId,
                            timeframe
                        })
                    });
                    if (!res.ok) throw new Error(res.statusText);
                    data = await res.json();
                } catch (err) {
                    console.error('AJAX error:', err);
                    data = [];
                }

                // --- 3) Render into .no-polls-container ---
                const container = document.querySelector('.no-polls-container');
                container.innerHTML = ''; // wipe it out

                if (!data.length) {
                    // re-insert the original “no polls” message
                    container.innerHTML = `
                                            <div class="no-polls-icon"><i class="fas fa-search"></i></div>
                                            <h2 class="no-polls-title">No polls found with the selected filters</h2>
                                            <p class="no-polls-text">Try adjusting your filters to see more polling results.</p>
                                        `;
                    return;
                }

                // build header + body
                let html = '<table class="polls-table">';
                html += `
                        <thead>
                            <tr>
                            <th>Pollster</th>
                            <th>Date</th>
                            <th>Sample</th>
                            <th>Net</th>
                            </tr>
                        </thead>
                        <tbody>
                        `;

                data.forEach(p => {
                    // p.pollster, p.date, p.sample, p.net OR whatever your AJAX returns
                    html += `
                            <tr>
                            <td>${p.pollster}</td>
                            <td>${p.date}</td>
                            <td>${p.sample}</td>
                            <td class="poll-result ${p.net >= 0 ? 'positive' : 'negative'}">
                                ${p.net >= 0 ? '+' : ''}${p.net}%
                            </td>
                            </tr>
                        `;
                });

                html += '</tbody></table>';
                container.innerHTML = html;
            });
        });
    </script>
@endsection
