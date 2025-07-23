@extends('frontend.layout')
@section('content')

    <style>
        /* Charts & layout styling (unchanged) */
        .charts-section {
            display: grid;
            grid-template-columns: 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .chart-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 25px;
            text-align: center;
        }

        .chart-header {
            margin-bottom: 20px;
        }

        .chart-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .chart-wrapper {
            position: relative;
            margin: 0 auto;
        }

        .demographic-section {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--dark-blue);
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--light-gray);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .demographic-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 30px;
        }

        .demographic-card {
            background: var(--light-gray);
            border-radius: 10px;
            padding: 25px;
        }

        .demographic-title {
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--dark-blue);
            font-size: 18px;
            text-align: center;
        }

        .demographic-item {
            margin-bottom: 20px;
        }

        .group-label {
            font-weight: 600;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
        }

        .demographic-bar {
            height: 12px;
            background: var(--medium-gray);
            border-radius: 6px;
            margin-bottom: 5px;
            overflow: hidden;
        }

        .demographic-fill {
            height: 100%;
        }

        .demographic-labels {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: var(--text-medium);
        }

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

        /* Accordion table styles */
        .polling-table {
            width: 100%;
            border-collapse: collapse;
        }

        .polling-table th,
        .polling-table td {
            padding: 8px;
            border: 1px solid #ddd;
            /* center most cells by default */
            text-align: center;
        }

        /* Only override toggle cell to be left-aligned */
        td.toggle-control {
            cursor: pointer;
            font-weight: bold;
            text-align: left;
        }

        .polling-row {
            display: flex;
            padding: 10px 15px;
            border-bottom: 1px solid #ccc;
            align-items: center;
            flex-wrap: wrap;
        }

        .polling-header {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .polling-cell {
            flex: 1;
            min-width: 100px;
        }

        .toggle-control {
            cursor: pointer;
            font-weight: bold;
            width: 20px;
            text-align: left;
        }

        .details-row {
            background-color: #f9f9f9;
            padding: 10px 15px;
        }

        .polling-details {
            padding: 10px;
            display: flex;
            justify-content: left;
            gap: 50px;
        }

        .details-list li {
            list-style: none;
            margin-bottom: 5px;

        }

        .details-row {
            display: none;
        }

        .sidebar-card .chart-wrapper {
            width: 100%;
            max-width: 100%;
        }

        /* 2) Force the canvas to fill its wrapper */
        #allCandidatesPie {
            width: 100% !important;
            height: 100% !important;
        }

        .search_input{
            padding:0.5rem; 
            width:200px; 
            border:1px solid #ccc;
            border-radius:4px;
        }


        @media (max-width:1100px) {
            .sidebar-card>table:nth-child(2) {
                width: 100% !important;
                table-layout: auto;
            }
        }


        @media screen and (max-width: 768px) {

            .polling-row {
                flex-direction: row !important;
                align-items: flex-start;
                padding: 8px 12px;
            }

            .polling-cell {
                width: 100%;
                min-width: auto;
                text-align: left;
                padding: 4px 0;
            }

            /* Give the toggle a bit of breathing room */
            .toggle-control {
                margin-bottom: 6px;
            }

            .details-row {
                width: 769px;
            }

            .polling-row {
                flex-wrap: nowrap !important;
                width: 769px !important;
            }

            .charts-section,
            .demographic-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
            }

            /* 4. Sidebar cards full width on mobile */
            .sidebar {
                flex: 1;
            }

            .sidebar-card {
                padding: 10px;
            }

            .demographic-item,
            .chart-container {
                padding: 15px;
            }

            .sidebar-card>table:nth-child(2) {
                width: 100% !important;
                table-layout: auto;
            }
        }





        @media (max-width:590px) {

            .polling-row {
                flex-direction: row !important;
                align-items: flex-start;
                padding: 8px 12px;
            }

            .polling-cell {
                width: 100%;
                min-width: auto;
                text-align: left;
                padding: 4px 0;
            }

            /* Give the toggle a bit of breathing room */
            .toggle-control {
                margin-bottom: 6px;
            }

            .details-row {
                width: 652px;
            }

            .polling-row {
                flex-wrap: nowrap !important;
                width: 652px !important;
            }

            .charts-section,
            .demographic-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
            }

            /* 4. Sidebar cards full width on mobile */
            .sidebar {
                flex: 1;
            }

            .sidebar-card {
                padding: 10px;
            }

            .demographic-item,
            .chart-container {
                padding: 15px;
            }

            .sidebar-card>table:nth-child(2) {
                width: 100% !important;
                table-layout: auto;
            }


            /* --- D3 Trend Chart Styles (from your other file) --- */
            #d3TrendChart {
                width: 100%;
                height: 400px;
            }

            .controls {
                display: flex;
                justify-content: center;
                gap: 15px;
                margin: 15px 0 10px;
            }

            .controls button {
                padding: 6px 14px;
                border: none;
                border-radius: 20px;
                background: #e0e7ff;
                color: #4f46e5;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s;
            }

            .controls button.active {
                background: #4f46e5;
                color: #fff;
            }

            .legend {
                display: flex;
                justify-content: center;
                flex-wrap: wrap;
                gap: 15px;
                margin-bottom: 10px;
            }

            .legend-item {
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .legend-color {
                width: 20px;
                height: 4px;
                border-radius: 2px;
                flex-shrink: 0;
            }

            .tooltip {
                position: absolute;
                padding: 8px;
                background: rgba(0, 0, 0, 0.8);
                color: #fff;
                border-radius: 4px;
                font-size: 13px;
                pointer-events: none;
                opacity: 0;
                transition: opacity 0.2s;
                z-index: 10;
            }

            .info-bar {
                display: flex;
                justify-content: space-between;
                padding-top: 8px;
                border-top: 1px solid #e9ecef;
                font-size: 14px;
            }

            .date-range {
                font-weight: 500;
                color: #495057;
            }

            /* tab button styling */

            .chart-tabs button {
                background: #f0f0f0;
                border: none;
                border-bottom: 2px solid transparent;
                font-weight: 600;
            }

            .chart-tabs button.active {
                background: #ffffff;
                border-bottom-color: #007bff;
            }

            .chart-tabs button:hover {
                background: #fafafa;
            }

            .search_input{
                width: 100px;
            }
        }
    </style>

    <div class="main-content">

        @php
            $template = $data[0]['results'] ?? [];
            $stats = [];
            foreach ($data as $poll) {
                foreach ($poll['results'] as $r) {
                    $stats[$r['name']]['sum'] = ($stats[$r['name']]['sum'] ?? 0) + $r['pct'];
                    $stats[$r['name']]['count'] = ($stats[$r['name']]['count'] ?? 0) + 1;
                }
            }
            $topCandidates = collect($stats)
                ->map(fn($v, $n) => ['name' => $n, 'avg' => $v['sum'] / $v['count']])
                ->sortByDesc('avg')
                ->pluck('name')
                ->take(3)
                ->toArray();
        @endphp

        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-table"></i> Polling Data</div>
                <div style="display:flex; justify-content:flex-end; margin-bottom:1rem;">
                    <input id="table-search" type="text" class="search_input" placeholder="Search polls...">
                </div>
            </div>
            <div class="polling-table-container">

                <!-- Header -->
                <div class="polling-row polling-header">
                    <div class="polling-cell"></div>
                    <div class="polling-cell">Pollster</div>
                    <div class="polling-cell">Date</div>
                    @foreach ($topCandidates as $name)
                        @php $c = collect($template)->first(fn($t)=>$t['name']==$name); @endphp
                        <div class="polling-cell" style="color: {{ $c['color'] ?? 'gray' }}">{{ $name }}</div>
                    @endforeach
                    <div class="polling-cell">Net</div>
                </div>

                <!-- Body -->
                @php
                    usort($data, function ($a, $b) {
                        return strtotime($b['date']) <=> strtotime($a['date']);
                    });
                @endphp
                @foreach ($data as $idx => $poll)
                    @php
                        $pctMap = [];
                        $colorMap = [];
                        foreach ($poll['results'] as $r) {
                            $pctMap[$r['name']] = $r['pct'];
                            $colorMap[$r['name']] = $r['color'];
                        }
                        $sorted = collect($poll['results'])->sortByDesc('pct')->values();
                        $topPct = $sorted->first()['pct'] ?? 0;
                    @endphp

                    <!-- Main Poll Row -->
                    <div class="polling-row" data-idx="{{ $idx }}">
                        <div class="polling-cell toggle-control">+</div>
                        <div class="polling-cell">{{ $poll['pollster'] }}</div>
                        <div class="polling-cell">{{ $poll['date'] }}</div>

                        @foreach ($topCandidates as $name)
                            @php
                                $pct = $pctMap[$name] ?? 0;
                                $color = $colorMap[$name] ?? 'gray';
                            @endphp
                            <div class="polling-cell" style="color: {{ $color }}">
                                {{ number_format($pct, 1) }}%
                            </div>
                        @endforeach

                        @php
                            $net = number_format($poll['net'], 1);
                            $netColor = $poll['net_color'];
                        @endphp
                        <div class="polling-cell" style="color: {{ $netColor }}">▲{{ $net }}%</div>
                    </div>

                    <!-- Details Row -->
                    <div class="details-row">
                        <div class="polling-details">
                            <div class="detailsrace">
                                <span>Race Type: {{ $poll['race_type'] }}</span>
                            </div>
                            <div class="detailssample">
                                <span>Sample: {{ $poll['sample'] }}</span>
                            </div>
                            <div class="detailslist">
                                <ul class="details-list">

                                    @php
                                        $sortedResults = collect($poll['results'])->sortByDesc('pct');
                                    @endphp

                                    @foreach ($sortedResults as $r)
                                        <li>
                                            @if ($r['image'])
                                                <img src="{{ asset($r['image']) }}" class="rounded-circle"
                                                    style="width:35px;height:35px;background:#e2e2e2;">
                                            @else
                                                <img src="{{ asset('images/default-avatar.jpg') }}" class="rounded-circle"
                                                    style="width:35px;height:35px;background:#e2e2e2;">
                                            @endif

                                            <span style="color: {{ $r['color'] }}">{{ $r['name'] }}</span>:
                                            {{ number_format($r['pct'], 1) }}%
                                            @if (isset($r['diff']) && $r['diff'] !== 0)
                                                <span class="ms-1 {{ $r['diff'] > 0 ? 'text-success' : 'text-danger' }}">
                                                    ({{ $r['diff'] > 0 ? '+' : '' }}{{ $r['diff'] }})
                                                </span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="sidebar-card">
            <div class="sidebar-title" style="gap : 5px;">
                <i class="fas fa-chart-simple"></i> Poll Visualizations
            </div>

            {{-- 1. Tab buttons --}}
            <div class="chart-tabs" style="display:flex; gap:1rem; margin-bottom:1rem;">
                <button id="tab-pie" class="active" style="padding:0.5rem 1rem; cursor:pointer;">
                    Pie Chart
                </button>
                <button id="tab-trend" style="padding:0.5rem 1rem; cursor:pointer;">
                    Trend Chart
                </button>
            </div>

            {{-- 2. Tab panes --}}
            <div class="charts-section">
                {{-- Pie Pane --}}
                <div id="pane-pie" class="chart-container">
                    <div class="chart-wrapper">
                        <canvas id="allCandidatesPie"></canvas>
                    </div>
                </div>

                <!-- Trend Pane -->
                <div id="pane-trend" class="chart-container" style="display:none;">
                    <div class="chart-header">
                        <h3 class="chart-title">Poll Average Trend (Last 30 Days)</h3>
                    </div>

                    <div class="average-label" style="text-align:center; margin-bottom:15px;">
                        Poll Average – Smoothed rolling average of all polls.
                    </div>

                    <div id="d3TrendChart" style="width:100%; height:300px; position: relative;"></div>
                    <div class="tooltip"
                        style="position:absolute; opacity:0; pointer-events:none;
                    background:rgba(0,0,0,0.8); color:#fff; padding:8px;
                    border-radius:4px;">
                    </div>

                    <div class="legend"
                        style="display:flex; flex-wrap: wrap; justify-content:center; gap:15px; margin-bottom:25px;"></div>

                    <div class="info-bar"
                        style="display:flex; justify-content:space-between;
                    font-size:14px; margin-top:10px;">
                        <div id="date-range"></div>
                        <div>Latest Spread: <strong id="latest-spread"></strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
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
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://d3js.org/d3.v7.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/luxon@3"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon@1"></script>

    <script>
        $(function() {
            $('.toggle-control').click(function() {
                var row = $(this).closest('.polling-row');
                var detail = row.next('.details-row');

                var isVisible = detail.is(':visible');

                detail.toggle(!isVisible);
                $(this).text(isVisible ? '+' : '-');
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1) register modified plugin
            Chart.register({
                id: 'centerLogo',
                beforeDraw(chart) {
                    const {
                        ctx
                    } = chart;
                    const meta = chart.getDatasetMeta(0).data[0];
                    const cx = meta.x,
                        cy = meta.y,
                        ir = meta.innerRadius;

                    if (!chart._logo || !chart._logo.complete) {
                        chart._logo = new Image();
                        chart._logo.src = '{{ asset('images/logo.jpg') }}';
                        chart._logo.onload = () => chart.draw();
                        return;
                    }

                    ctx.save();
                    ctx.beginPath();
                    ctx.arc(cx, cy, ir, 0, 2 * Math.PI);
                    ctx.clip();
                    const size = ir * 1.6;
                    ctx.drawImage(chart._logo, cx - size / 2, cy - size / 2, size, size);
                    ctx.restore();
                }
            });

            // 2) your existing data logic
            const pollData = @json($data);
            const template = pollData[0].results;
            const numPolls = pollData.length;
            const avgs = template.map(t => {
                const sum = pollData.reduce((acc, p) =>
                    acc + (p.results.find(r => r.name === t.name)?.pct || 0), 0
                );
                return parseFloat((sum / numPolls).toFixed(1));
            });
            const legendLabels = template.map((t, i) => `${t.name} (${avgs[i]}%)`);

            // ─── SORT CANDIDATES BY pct DESC ──────────────────────────────────────────
            // build an array of indices [0,1,2,...]
            const indices = avgs.map((_, i) => i);
            // sort indices by corresponding avgs value descending
            indices.sort((a, b) => avgs[b] - avgs[a]);

            // reorder data arrays according to sorted indices
            const sortedAvgs = indices.map(i => avgs[i]);
            const sortedLabels = indices.map(i => legendLabels[i]);
            const sortedColors = indices.map(i => template[i].color);
            // ─────────────────────────────────────────────────────────────────────────

            Chart.register(ChartDataLabels);

            // 3) draw chart as before, but using sorted arrays
            const ctx = document.getElementById('allCandidatesPie').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: sortedLabels,
                    datasets: [{
                        data: sortedAvgs,
                        backgroundColor: sortedColors,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 20
                            }
                        },
                        datalabels: {
                            display: false
                        }
                    }
                },
                plugins: ['centerLogo']
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let chartInitialized = false;

            // Pie tab click
            $('#tab-pie').on('click', () => {
                $('#pane-pie').show();
                $('#pane-trend').hide();
                $('#tab-pie').addClass('active');
                $('#tab-trend').removeClass('active');
            });

            // Trend tab click
            $('#tab-trend').on('click', () => {
                $('#pane-pie').hide();
                $('#pane-trend').show();
                $('#tab-trend').addClass('active');
                $('#tab-pie').removeClass('active');
                if (!chartInitialized) {
                    renderTrend();
                    chartInitialized = true;
                }
            });

            function renderTrend() {
                const raw = @json($trendRecords).map(d => ({
                    date: new Date(d.rawDate + 'T00:00:00'),
                    candidate: d.candidate,
                    pct: +d.pct,
                    color: d.color
                }));
                if (!raw.length) return;

                // Determine window
                const allDates = raw.map(r => r.date.getTime());
                const today = new Date(Math.max(...allDates));
                const startDate = d3.timeDay.offset(today, -29);
                const dates = d3.timeDay.range(startDate, d3.timeDay.offset(today, 1));

                // Group and smooth data
                const grouped = raw.reduce((acc, r) => {
                    (acc[r.candidate] ||= []).push(r);
                    return acc;
                }, {});

                function smooth(rows) {
                    const bw = 7 * 1.2;
                    const sm = dates.map(dt => {
                        let W = 0,
                            S = 0;
                        rows.forEach(p => {
                            const delta = (dt - p.date) / (1000 * 60 * 60 * 24);
                            if (Math.abs(delta) > bw * 3) return;
                            const k = Math.exp(-delta * delta / (2 * bw * bw));
                            W += k;
                            S += p.pct * k;
                        });
                        return {
                            date: dt,
                            pct: W ? S / W : null
                        };
                    });
                    let lastValid = null;
                    return sm.map(d => {
                        if (d.pct !== null) {
                            lastValid = d.pct;
                            return d;
                        }
                        return {
                            date: d.date,
                            pct: lastValid
                        };
                    });
                }

                const smoothed = Object.fromEntries(
                    Object.entries(grouped).map(([k, v]) => [k, smooth(v)])
                );

                // Info bar
                const lasts = Object.values(smoothed).map(s => s.slice(-1)[0].pct);
                const spread = (Math.max(...lasts) - Math.min(...lasts)).toFixed(1);
                $('#latest-spread').text(spread + '%');
                const fmtS = d3.timeFormat("%b %d"),
                    fmtF = d3.timeFormat("%b %d, %Y");
                $('#date-range').text(`${fmtS(startDate)} – ${fmtF(today)}`);

                // Dimensions
                const fullW = $('#pane-trend').width();
                const margin = {
                    top: 20,
                    right: 30,
                    bottom: 50,
                    left: 50
                };
                const W = fullW - margin.left - margin.right;
                const H = 300 - margin.top - margin.bottom;

                // Clear & create SVG
                const svg = d3.select('#d3TrendChart').html('')
                    .append('svg')
                    .attr('width', W + margin.left + margin.right)
                    .attr('height', H + margin.top + margin.bottom);

                // Inner group (translated)
                const g = svg.append('g')
                    .attr('transform', `translate(${margin.left},${margin.top})`);

                // —— TOP‑RIGHT LOGO —— 
                const logoSize = 75; // you requested 75px
                g.append('image')
                    .attr('x', W - logoSize - 5) // 5px padding from right edge
                    .attr('y', 5) // 5px padding from top edge
                    .attr('width', logoSize)
                    .attr('height', logoSize)
                    .attr('href', '{{ asset('images/logo.jpg') }}')
                    .style('pointer-events', 'none'); // so it doesn’t block tooltips

                // Scales
                const x = d3.scaleTime().domain([startDate, today]).range([0, W]);
                const y = d3.scaleLinear().domain([0, 100]).range([H, 0]);

                // Axes
                g.append('g')
                    .attr('transform', `translate(0,${H})`)
                    .call(d3.axisBottom(x).ticks(6).tickFormat(d3.timeFormat("%b %d")))
                    .selectAll('text')
                    .attr('transform', 'rotate(-20)')
                    .attr('text-anchor', 'end');

                g.append('g')
                    .call(d3.axisLeft(y).ticks(5).tickFormat(d => d + '%'));

                // Lines & Legend
                const legend = d3.select('#pane-trend .legend').html('');
                const sortedEntries = Object.entries(smoothed)
                    .sort(([, a], [, b]) => b[b.length - 1].pct - a[a.length - 1].pct);

                sortedEntries.forEach(([name, series]) => {
                    const line = d3.line()
                        .x(d => x(d.date))
                        .y(d => y(d.pct))
                        .curve(d3.curveCatmullRom);

                    g.append('path')
                        .datum(series)
                        .attr('d', line)
                        .attr('fill', 'none')
                        .attr('stroke', grouped[name][0].color)
                        .attr('stroke-width', 3)
                        .attr('stroke-linecap', 'round');

                    const item = legend.append('div')
                        .attr('class', 'legend-item')
                        .style('display', 'flex')
                        .style('align-items', 'center')
                        .style('gap', '6px');

                    item.append('div')
                        .style('width', '20px')
                        .style('height', '4px')
                        .style('border-radius', '2px')
                        .style('background-color', grouped[name][0].color);
                    item.append('span').text(name);
                });

                // Tooltip
                const tooltip = d3.select('#pane-trend .tooltip').style('opacity', 0);
                const bis = d3.bisector(d => d.date).left;

                g.append('rect')
                    .attr('width', W)
                    .attr('height', H)
                    .style('fill', 'none')
                    .style('pointer-events', 'all')
                    .on('mousemove', e => {
                        const [mx] = d3.pointer(e);
                        const dt = d3.timeDay.round(x.invert(mx));
                        const arr = Object.entries(smoothed).map(([n, s]) => {
                            const i = bis(s, dt);
                            let p = i <= 0 ? s[0] : i >= s.length ? s[s.length - 1] :
                                (dt - s[i - 1].date > s[i].date - dt ? s[i] : s[i - 1]);
                            return {
                                name: n,
                                color: grouped[n][0].color,
                                pct: p.pct
                            };
                        }).sort((a, b) => b.pct - a.pct);

                        let html = `<div>${d3.timeFormat("%b %d, %Y")(dt)}</div>`;
                        arr.forEach(c => {
                            html +=
                                `<div><strong style="color:${c.color}">${c.name}:</strong> ${c.pct.toFixed(1)}%</div>`;
                        });

                        tooltip.style('opacity', 1)
                            .style('left', `${e.pageX + 10}px`)
                            .style('top', `${e.pageY - 30}px`)
                            .html(html);
                    })
                    .on('mouseout', () => tooltip.style('opacity', 0));
            }
        });
    </script>

    <script>
        document.getElementById('table-search').addEventListener('keyup', function() {
            const term = this.value.toLowerCase();

            document.querySelectorAll('.polling-row').forEach(row => {

                if (row.classList.contains('polling-header')) {
                    row.style.display = '';
                    return;
                }

                const text = row.textContent.toLowerCase();
                const match = text.includes(term);

                row.style.display = match ? '' : 'none';

                const detail = row.nextElementSibling;
                if (detail && detail.classList.contains('details-row')) {
                    detail.style.display = match ? '' : 'none';
                }
            });
        });
    </script>


@endsection
