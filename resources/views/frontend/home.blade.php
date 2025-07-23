@extends('frontend.layout')
@section('content')

    <style>
        .arrow-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            background-color: #007bff;
            color: white;
            border-radius: 50%;
            font-size: 16px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .arrow-link:hover {
            background-color: #0056b3;
            color: #fff;
        }

        /* apporval */

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }

        .approval-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .approval-card-body {
            padding: 1rem;
        }

        .approval-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .approval-link {
            position: absolute;
            top: 1.45rem;
            background: #0d6efd;
            right: 0.75rem;
            font-size: 21px;
            margin: 0 auto;
            width: 35px;
            height: 35px;
            text-align: center;
            border-radius: 100%;
            color: #ffffff;
            text-decoration: none;
            transition: color 0.2s;
        }

        .approval-link:hover {
            color: #333;
        }


        /* Pagination Css*/

        .pagination-wrapper nav {
            display: flex;
            justify-content: center;
        }

        .page-item .page-link {
            margin: 0 4px;
            border-radius: 6px;
            color: #000;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
        }

        .page-item.active .page-link {
            background-color: #007bff;
            color: #fff;
            border-color: #007bff;
        }


        /* Table reset for clarity */
        .polls-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1em 0;
        }

        .polls-table th,
        .polls-table td {
            padding: 0.5em 0.75em;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        /* Date‐separator styling */
        .date-separator td {
            font-size: 0.85em;
            background: rgb(200 200 255);
            font-weight: 800;
            color: #444;
            padding: 0.25em 0.75em;
            border-bottom: none;
        }

        /* Poll rows styling */
        .polls-table tbody tr:not(.date-separator) td {
            font-size: 1em;
            color: #222;
        }

        /* Spread arrow link */
        .poll-spread {
            display: flex;
            align-items: center;
            gap: 0.2em;
            /* space between number and arrow */
            white-space: nowrap;
        }

        .arrow-link {
            text-decoration: none;
            font-size: 1.1em;
            opacity: 0.6;
        }

        .arrow-link:hover {
            opacity: 1;
        }

        .poll-spread.positive {
            color: #027502;
        }

        .poll-spread.negative {
            color: #a00;
        }

        @media screen and (max-width: 590px) {
            .filter-card {
                padding: 20px;
            }

            .filters-container {
                padding: 20px;
            }

            .poll-type-column {
                display: flex;
                flex-direction: column;
            }

            .poll-type-item {
                width: 100%;
            }

            .filter-row {
                display: flex;
                gap: 15px;
                flex-direction: column;
            }

            .apply-btn {
                width: 100%;
            }

            .filters {
                margin: 0px 0px 25px 0px !important;
                gap: 0px;
                display: flex;
                flex-direction: row;
                flex-wrap: wrap;
                width: 100%;
            }

            .card-title {
                width: 100%;
                font-size: 18px;
            }

            .filter-group {
                display: flex;
                gap: 10px;
                align-items: flex-start;
                flex-direction: column;
                width: 100%;
            }

            .filter-group select,
            .filter-group input {
                width: 100% !important;
            }

            .chart-container {
                padding: 10px 0px !important;
            }

            .stat-label {
                font-size: 12px;
            }

            .chart-title {
                font-size: 18px;
            }

            .stat-item {
                margin-right: 0.5rem;
            }

            .sidebar-card>table:nth-child(2) {
                width: 100% !important;
                table-layout: auto;
            }


            .card-grid {
                grid-template-columns: 1fr;
            }

            ..menu_btn {
                gap: 08px !important;
            }
        }


        @media screen and (max-width: 768px) {
            .filters {
                flex-direction: row;
                align-items: stretch;
            }

            .card-title {
                display: flex;
                flex-direction: row;
                width: 100%;
                align-items: center;
            }

            .chart-stats {
                flex-direction: row;
                gap: 10px;
            }

            .sidebar-card>table:nth-child(2) {
                width: 100% !important;
                table-layout: auto;
            }
        }
    </style>
    <!-- Main Content (70%) -->
    <div class="main-content">



        <div class="filter-card">
            <div class="filters-container">
                <div class="filter-row">
                    {{-- Poll Type --}}
                    <div class="filter-option">
                        {{-- <label for="polltypeSel">Poll Type</label> --}}
                        <select id="polltypeSel" class="filter-select">
                            <option value="">All Types</option>
                            @foreach ($racesType as $type)
                                <option value="{{ strtolower($type->race_type) }}">
                                    {{ ucfirst($type->race_type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- State --}}
                    <div class="filter-option">
                        {{-- <label for="stateSel">State</label> --}}
                        <select id="stateSel" class="filter-select">
                            <option value="">All States</option>
                            @foreach ($State as $st)
                                <option value="{{ $st->id }}">{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Pollster --}}
                    <div class="filter-option">
                        {{-- <label for="pollsterSel">Pollster</label> --}}
                        <select id="pollsterSel" class="filter-select">
                            <option value="">All Pollsters</option>
                            @foreach ($pollesters as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Timeframe --}}
                    <div class="filter-option">
                        {{-- <label for="timeframeSel">Timeframe</label> --}}
                        <select id="timeframeSel" class="filter-select">
                            <option value="7">Last 7 days</option>
                            <option value="30">Last 30 days</option>
                            <option value="90" selected>Last 90 days</option>
                            <option value="180">Last 6 months</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="header-section">
                <h1 class="page-title">Latest Polls</h1>
                <p class="page-subtitle">Most recent polling data from various sources across different races.</p>
            </div>
        </div>

        <template id="no-polls-template">
            <div class="no-polls-container">
                <div class="no-polls-icon"><i class="fas fa-search"></i></div>
                <h2 class="no-polls-title">No polls found with the selected filters</h2>
                <p class="no-polls-text">Try adjusting your filters to see more polling results.</p>
            </div>
        </template>

        <div class="polls-results"></div>

        <!-- Filters -->
        <div class="filters">
            <!-- Race Filter -->
            <div class="filters">
                <div class="filter-group">
                    <label for="race-select"><i class="fas fa-flag"></i> Race:</label>
                    <select id="race-select" name="race_type">
                        <option value="" disabled>-- Select Race --</option>
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
                    <select id="state-select" name="state_id">
                        <option value="">-- Select State --</option>
                        <option value="temp">All States</option>
                        @foreach ($states as $st)
                            <option value="{{ $st->id }}">{{ $st->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-table"></i> Race Data</div>
                <div class="time-filters"></div>
            </div>
            <div class="polling-table-container">
                <table id="races-table" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Race</th>
                            <th>State</th>
                            <th>Candidates</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        {{-- Approval Section Card --}}

        <div id="approval-cards-container">
            @include('frontend.approval-cards')
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
    </div>

    <!-- Sidebar (30%) -->
    <div class="sidebar">
        <div class="sidebar-card">
            <div class="sidebar-title">
                <i class="fas fa-star"></i> Featured Races
            </div>

            @if ($featuredRaces->isEmpty())
                <p class="p-3 text-sm text-gray-500">
                    No featured races at the moment.
                </p>
            @else
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr>
                            <th class="border-b px-2 py-1">Race</th>
                            <th class="border-b px-2 py-1">Election_round</th>
                            <th class="border-b px-2 py-1">State</th>
                            <th class="border-b px-2 py-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($featuredRaces as $fp)
                            <tr>
                                @if ($fp->race == 'election')
                                    <td class="border-b px-2 py-1">{{ $fp->race_type . ' ' . $fp->race }}</td>
                                @elseif($fp->race == 'approval')
                                    <td class="border-b px-2 py-1">
                                        {{ optional($fp->candidates->first())->name . ' ' . $fp->race }}</td>
                                @endif

                                <td class="border-b px-2 py-1">{{ $fp->election_round ?: 'N/A' }}</td>
                                @if ($fp->district)
                                    <td class="border-b px-2 py-1">{{ $fp->state->name ?? 'N/A' }} - {{ $fp->district }}
                                    </td>
                                @else
                                    <td class="border-b px-2 py-1">{{ $fp->state->name ?? 'N/A' }}</td>
                                @endif

                                <td class="border-b px-2 py-1">
                                    @if ($fp->race == 'election')
                                        <a href="/details?race_id={{ $fp->id }}" class="arrow-link"
                                            title="View Details">➔</a>
                                    @elseif($fp->race == 'approval')
                                        <a href="{{ route('approval.details', [
                                            'candidateSlug' => \Illuminate\Support\Str::slug(optional($fp->candidates->first())->name ?: 'unknown'),
                                            'race_id' => $fp->id,
                                        ]) }}"
                                            class="arrow-link" title="View Details">
                                            ➔
                                            </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Key Metrics -->
        <div class="sidebar-card">
            {{-- <div class="sidebar-title"><i class="fas fa-tachometer-alt"></i> Key Metrics</div>
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
            </div> --}}
        </div>
    </div>

    <!-- jQuery & DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>



    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const polltypeSel = document.getElementById('polltypeSel');
            const stateSel = document.getElementById('stateSel');
            const pollsterSel = document.getElementById('pollsterSel');
            const timeframeSel = document.getElementById('timeframeSel');
            const container = document.querySelector('.polls-results');
            const noResults = document.getElementById('no-polls-template').innerHTML;

            const colorMap = {
                'democratic party': '#CFECF7',
                'republican party': '#FFEFEF',
                'libertarian party': 'gold',
                'green party': 'green',
                'constitution party': 'darkred',
                'independent': 'gray'
            };

            // ─── CACHE INITIAL DROPDOWN OPTIONS ───
            const initialStates = Array.from(stateSel.options)
                .map(o => ({
                    value: o.value,
                    text: o.text
                }));
            const initialPollsters = Array.from(pollsterSel.options)
                .map(o => ({
                    value: o.value,
                    text: o.text
                }));

            let latestOpts = {
                states: [],
                pollesters: []
            };

            // 1) Poll Type change
            polltypeSel.addEventListener('change', async () => {
                const pt = polltypeSel.value;

                // always reset timeframe
                timeframeSel.value = '90';

                if (pt) {
                    // clear and then fetch new lists
                    stateSel.innerHTML = '<option value="">All States</option>';
                    pollsterSel.innerHTML = '<option value="">All Pollsters</option>';
                    latestOpts = {
                        states: [],
                        pollesters: []
                    };

                    try {
                        const res = await fetch("{{ route('polls.options') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({
                                pollType: pt
                            })
                        });
                        if (res.ok) {
                            latestOpts = await res.json();
                            // populate States
                            stateSel.innerHTML = '<option value="">All States</option>' +
                                latestOpts.states.map(s => `<option value="${s.id}">${s.name}</option>`)
                                .join('');
                            // populate Pollsters
                            pollsterSel.innerHTML = '<option value="">All Pollsters</option>' +
                                latestOpts.pollesters.map(p =>
                                    `<option value="${p.id}">${p.name}</option>`).join('');
                        }
                    } catch (e) {
                        console.error('Options load error', e);
                    }
                } else {
                    // “All Types” selected → restore the initial full lists
                    stateSel.innerHTML = initialStates
                        .map(o => `<option value="${o.value}">${o.text}</option>`)
                        .join('');
                    pollsterSel.innerHTML = initialPollsters
                        .map(o => `<option value="${o.value}">${o.text}</option>`)
                        .join('');
                    latestOpts = {
                        states: [],
                        pollesters: []
                    };
                }

                fetchPolls();
            });

            // 2) State change
            stateSel.addEventListener('change', async () => {
                const pt = polltypeSel.value;
                const sid = stateSel.value;

                if (pt) {
                    pollsterSel.innerHTML = '<option value="">All Pollsters</option>';
                    if (sid) {
                        try {
                            const res = await fetch("{{ route('polls.pollsters') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                },
                                body: JSON.stringify({
                                    pollType: pt,
                                    state_id: sid
                                })
                            });
                            if (res.ok) {
                                const data = await res.json();
                                pollsterSel.innerHTML = '<option value="">All Pollsters</option>' +
                                    data.pollesters.map(p =>
                                        `<option value="${p.id}">${p.name}</option>`).join('');
                            }
                        } catch (e) {
                            console.error('Pollster load error', e);
                        }
                    } else {
                        // rollback to pollType list
                        pollsterSel.innerHTML = '<option value="">All Pollsters</option>' +
                            latestOpts.pollesters.map(p => `<option value="${p.id}">${p.name}</option>`)
                            .join('');
                    }
                }
                // if no pollType, leave both selects untouched

                fetchPolls();
            });

            // 3) Pollster & Timeframe
            pollsterSel.addEventListener('change', fetchPolls);
            timeframeSel.addEventListener('change', fetchPolls);

            async function fetchPolls() {
                const payload = {
                    pollType: polltypeSel.value || null,
                    state_id: +stateSel.value || null,
                    pollster_id: +pollsterSel.value || null,
                    timeframe: +timeframeSel.value
                };
                let data = [];
                try {
                    const res = await fetch("{{ route('polls.filter') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify(payload)
                    });
                    data = res.ok ? await res.json() : [];
                } catch (err) {
                    console.error('Error loading polls', err);
                }

                if (!data.length) {
                    container.innerHTML = noResults;
                    return;
                }

                const grouped = data.reduce((acc, poll) => {
                    (acc[poll.date] = acc[poll.date] || []).push(poll);
                    return acc;
                }, {});

                let html = `<table class="polls-table">
            <thead>
              <tr>
                <th>Race</th>
                <th>Pollster</th>
                <th>Candidate</th>
                <th>Action</th>
              </tr>
            </thead><tbody>`;

                for (let dateKey of Object.keys(grouped)) {
                    const grp = grouped[dateKey];
                    html += `
              <tr class="date-separator">
                <td colspan="4">${grp[0].dateFormatted}</td>
              </tr>`;
                    grp.forEach(poll => {
                        const isPrimary = poll.election_round &&
                            poll.election_round.toLowerCase().startsWith('primary');
                        const bg = isPrimary ? (colorMap[poll.party] || '') : '';
                        const style = bg ? ` style="background-color: ${bg}"` : '';

                        html += `
                  <tr${style}>
                    <td>${poll.race}${poll.election_round ? ' - '+poll.election_round : ''}${poll.district ? ' - '+poll.district : ''}</td>
                    <td>${poll.pollster}</td>
                    <td>${poll.candidate}</td>
                    <td>
                      <a href="/details?race_id=${poll.race_id}"
                         class="arrow-link" title="View Details">➔</a>
                    </td>
                  </tr>`;
                    });
                }

                html += `</tbody></table>`;
                container.innerHTML = html;
            }

            // Initial load
            fetchPolls();
        });
    </script>

    <script>
        $(function() {
            var colorMap = {
                'democratic party': '#CFECF7',
                'republican party': '#FFEFEF',
                'libertarian party': 'gold',
                'green party': 'green',
                'constitution party': 'darkred',
                'independent': 'gray'
            };

            var table = $('#races-table').DataTable({
                pageLength: 10,
                lengthChange: false,
                searching: false,
                info: false,
                ordering: false,
                stripeClasses: [],

                createdRow: function(row, data) {
                    if (
                        data.election_round &&
                        data.election_round.toLowerCase() === 'primary' &&
                        Array.isArray(data.leading) &&
                        data.leading.length > 0
                    ) {
                        var party = data.leading[0].party;
                        var bg = colorMap[party] || '';
                        if (bg) {
                            row.style.backgroundColor = bg;
                            row.classList.add('primary-party-row');
                        }
                    }
                },

                columns: [{
                        data: 'race_type',
                        render: function(raceType, type, row) {
                            return raceType + (row.election_round ? ' - ' + row.election_round :
                                '');
                        }
                    },
                    // {
                    //     data: 'election_round'
                    // },
                    {
                        data: 'state_name',
                        render: function(stateName, type, row) {
                            // If there's a district, show "State – District", otherwise just the state
                            return row.district ?
                                stateName + ' - ' + row.district :
                                stateName;
                        }
                    },
                    {
                        data: 'leading',
                        render: function(data) {
                            if (Array.isArray(data) && data.length) {
                                return data
                                    .map(function(c) {
                                        return c.name + ' (' + c.percentage + '%)';
                                    })
                                    .join('<br>');
                            }
                            return '<span style="color:#999;">No data</span>';
                        }
                    },
                    {
                        data: 'id',
                        orderable: false,
                        render: function(id) {
                            return `
            <a href="/details?race_id=${id}" class="arrow-link" title="View Details">
              ➔
            </a>`;
                        }
                    }
                ]
            });

            function fetch(params) {
                $.getJSON('/Apiracesdata' + params, function(list) {
                    table.clear().rows.add(list).draw();
                }).fail(function() {
                    table.clear().draw();
                });
            }

            $('#race-select').on('change', function() {
                $('#state-select').val('');
                var rt = $(this).val();
                fetch(rt ? '?race_type=' + encodeURIComponent(rt) : '');
            });

            $('#state-select').on('change', function() {
                $('#race-select').val('');
                var sid = $(this).val();
                fetch('?state_id=' + encodeURIComponent(sid));
            });

            // Initial load: explicitly fetch all elections
            fetch('?race=election');
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

                return [labs, apps, diss];
            }

            // 5) Update sidebar metrics
            function updateMetrics(appArr, disArr) {

                const avgApp = average(appArr);
                const avgDis = average(disArr);
                const net = avgApp - avgDis;


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
        document.addEventListener('click', function(e) {
            const link = e.target.closest('.pagination a');
            if (link) {
                e.preventDefault();
                fetch(link.href, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.text())
                    .then(html => {
                        document.querySelector('#approval-cards-container').innerHTML = html;
                        window.history.pushState({}, '', link.href);
                    })
                    .catch(err => console.error('Pagination load failed', err));
            }
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
@endsection
