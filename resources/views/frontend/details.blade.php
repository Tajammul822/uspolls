@extends('frontend.layout')
@section('content')

    <style>
        html,
        body {
            overflow-x: hidden !important;
        }

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
            height: 300px;
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
            text-align: center;
        }

        .toggle-control {
            cursor: pointer;
            font-weight: bold;
            width: 20px;
            text-align: left;
        }

        .details-row {
            display: none;
            background-color: #f9f9f9;
            padding: 10px 15px;
        }

        .polling-details {
            display: flex;
            justify-content: flex-start;
            gap: 50px;
            flex-wrap: wrap;
        }

        .details-list li {
            list-style: none;
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
        }

        /* .polling-table-container {
            display: none !important;
        } */

        .polling-responsive-table {
            display: block !important;
            overflow-x: auto !important;
        }

        .polling-responsive-table table {
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 14px !important;
        }

        .polling-responsive-table th,
        .polling-responsive-table td {
            border: 1px solid #ccc !important;
            padding: 6px 10px !important;
            text-align: left !important;
        }

        .polling-responsive-table .toggle-control {
            cursor: pointer !important;
            font-weight: bold !important;
            width: 20px !important;
            text-align: center !important;
        }

        .polling-responsive-table .details-row td {
            background: #f9f9f9 !important;
        }

        .polling-responsive-table ul {
            margin: 0 !important;
        }

        */ @media screen and (max-width: 590px) {

            /* Hide the header row so labels don’t collide */
            .polling-header {
                display: none;
            }

            /* Stack each row’s cells */
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
        }
    </style>

    <div class="main-content">

        @php
            // Build candidate “template” once from the first poll
            $template = $data[0]['results'] ?? [];
            $colCount = count($template);
        @endphp

        @php
            // Prepare top 3 candidates by average pct
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

        {{-- <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-table"></i> Polling Data</div>
            </div>
            <div class="polling-table-container">

                <!-- Header -->
                <div class="polling-row polling-header">
                    <div class="polling-cell toggle-control"></div>
                    <div class="polling-cell">Pollster</div>
                    <div class="polling-cell">Date</div>
                    @foreach ($topCandidates as $name)
                        @php $c = collect($template)->first(fn($t) => $t['name'] === $name); @endphp
                        <div class="polling-cell" style="color: {{ $c['color'] }}">{{ $name }}</div>
                    @endforeach
                    <div class="polling-cell">Net</div>
                </div>

                <!-- Body -->
                @foreach ($data as $idx => $poll)
                    @php
                        $pctMap = collect($poll['results'])->pluck('pct', 'name')->toArray();
                        $colorMap = collect($poll['results'])->pluck('color', 'name')->toArray();
                    @endphp
                    <div class="polling-row" data-idx="{{ $idx }}">
                        <div class="polling-cell toggle-control">+</div>
                        <div class="polling-cell">{{ $poll['pollster'] }}</div>
                        <div class="polling-cell">{{ $poll['date'] }}</div>

                        @foreach ($topCandidates as $name)
                            <div class="polling-cell" style="color: {{ $colorMap[$name] ?? 'gray' }}">
                                {{ number_format($pctMap[$name] ?? 0, 1) }}%
                            </div>
                        @endforeach

                        <div class="polling-cell" style="color: {{ $poll['net_color'] }}">
                            ▲{{ number_format($poll['net'], 1) }}%</div>
                    </div>

                    <div class="details-row">
                        <div class="polling-details">
                            <div><strong>Race Type:</strong> {{ $poll['race_type'] }}</div>
                            <div><strong>Sample:</strong> {{ $poll['sample'] }}</div>
                            <div>
                                <ul class="details-list">
                                    @foreach ($poll['results'] as $r)
                                        <li>
                                            <span style="color: {{ $r['color'] }}">{{ $r['name'] }}</span>:
                                            {{ number_format($r['pct'], 1) }}%
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div> --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-table"></i> Polling Data</div>
            </div>

            <div class="polling-table-container">
                {{-- HEADER --}}
                <div class="polling-row polling-header">
                    <div class="polling-cell toggle-control"></div>
                    <div class="polling-cell">Pollster</div>
                    <div class="polling-cell">Date</div>
                    @foreach ($topCandidates as $name)
                        @php $c = collect($template)->first(fn($t) => $t['name'] === $name); @endphp
                        <div class="polling-cell" style="color: {{ $c['color'] }}">{{ $name }}</div>
                    @endforeach
                    <div class="polling-cell">Net</div>
                </div>

                {{-- BODY --}}
                @foreach ($data as $idx => $poll)
                    @php
                        $pctMap = collect($poll['results'])->pluck('pct', 'name')->toArray();
                        $colorMap = collect($poll['results'])->pluck('color', 'name')->toArray();
                    @endphp
                    <div class="polling-row" data-idx="{{ $idx }}">
                        <div class="polling-cell toggle-control">+</div>
                        <div class="polling-cell">{{ $poll['pollster'] }}</div>
                        <div class="polling-cell">{{ $poll['date'] }}</div>

                        @foreach ($topCandidates as $name)
                            <div class="polling-cell" style="color: {{ $colorMap[$name] ?? 'gray' }}">
                                {{ number_format($pctMap[$name] ?? 0, 1) }}%
                            </div>
                        @endforeach

                        <div class="polling-cell" style="color: {{ $poll['net_color'] }}">
                            ▲{{ number_format($poll['net'], 1) }}%
                        </div>
                    </div>

                    <div class="details-row">
                        <div class="polling-details">
                            <div><strong>Race Type:</strong> {{ $poll['race_type'] }}</div>
                            <div><strong>Sample:</strong> {{ $poll['sample'] }}</div>
                            <div>
                                <ul class="details-list">
                                    @foreach ($poll['results'] as $r)
                                        <li>
                                            @if ($r['image'])
                                                <img src="{{ asset($r['image']) }}" alt="Candidate Image"
                                                    class="rounded-circle"
                                                    style="width: 35px; height: 35px; background-color: #e2e2e2;">
                                            @else
                                                <img src="{{ asset('images/default-avatar.jpg') }}" alt="Default Image"
                                                    class="rounded-circle"
                                                    style="width: 35px; height: 35px; background-color: #e2e2e2;">
                                            @endif
                                            <span style="color: {{ $r['color'] }}">{{ $r['name'] }}</span>:
                                            {{ number_format($r['pct'], 1) }}%
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>


        <!-- Demographic Breakdown by Party -->
        <div class="demographic-section">
            <h2 class="section-title">
                <i class="fas fa-chart-bar"></i> Demographic Breakdown by Party
            </h2>
            <div class="demographic-grid">
                <div class="demographic-card">
                    <div class="demographic-title">Party Avg %</div>

                    @php
                        $num = $data->count();
                        $averages = [];

                        // Compute average pct per template candidate
                        for ($i = 0; $i < $colCount; $i++) {
                            $sum = 0;
                            foreach ($data as $poll) {
                                // look up in the map you just built
                                $pctMap = collect($poll['results'])->keyBy('name')->map->pct->toArray();
                                $name = $template[$i]['name'];
                                $sum += $pctMap[$name] ?? 0;
                            }
                            $averages[$i] = round($sum / max($num, 1), 1);
                        }
                    @endphp

                    @for ($i = 0; $i < $colCount; $i++)
                        @php
                            $cand = $template[$i];
                            $avg = $averages[$i] ?? 0;
                        @endphp
                        <div class="demographic-item">
                            <div class="group-label">
                                <span>{{ $cand['party'] }}</span>
                                <span>{{ number_format($avg, 1) }}%</span>
                            </div>
                            <div class="demographic-bar">
                                <div class="demographic-fill"
                                    style="width: {{ $avg }}%; background-color: {{ $cand['color'] }};">
                                </div>
                            </div>
                            <div class="demographic-labels">
                                <span>0%</span>
                                <span>100%</span>
                            </div>
                        </div>
                    @endfor

                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar (unchanged) -->
    <div class="sidebar">
        <!-- Featured Races -->
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
                                <td class="border-b px-2 py-1">{{ $fp->state->name ?? 'N/A' }}</td>

                                <td class="border-b px-2 py-1">
                                    @if ($fp->race == 'election')
                                        <a href="/details?race_id={{ $fp->id }}" class="arrow-link"
                                            title="View Details">➔</a>
                                    @elseif($fp->race == 'approval')
                                        <a href="{{ route('approval.details', ['race_id' => $fp->id]) }}"
                                            class="arrow-link" title="View Details">➔</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="sidebar-card">
            <div class="sidebar-title"><i class="fas fa-chart-simple"></i> Result by Candidate</div>
            <!-- Single Pie Chart for All Candidates -->
            <div class="charts-section">
                <div class="chart-container">
                    <div class="chart-wrapper">
                        <canvas id="allCandidatesPie"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        $(function() {
            $('.toggle-control').click(function() {
                var detail = $(this).closest('.polling-row').next('.details-row');
                var visible = detail.is(':visible');
                detail.toggle(!visible);
                $(this).text(visible ? '+' : '-');
            });
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pollData = @json($data);
            const numPolls = pollData.length;
            const template = pollData[0].results;
            const candidateCount = template.length;

            // compute avg % per candidate
            const avgs = Array(candidateCount).fill(0);
            pollData.forEach(p => {
                // map by name for this poll
                const map = p.results.reduce((m, r) => {
                    m[r.name] = r.pct;
                    return m;
                }, {});
                template.forEach((t, idx) => {
                    avgs[idx] += (map[t.name] ?? 0);
                });
            });
            for (let i = 0; i < candidateCount; i++) {
                avgs[i] = parseFloat((avgs[i] / numPolls).toFixed(1));
            }

            // labels & colors from template
            const labels = template.map(r => r.name);
            const colors = template.map(r => r.color);

            // render combined pie
            const ctx = document.getElementById('allCandidatesPie').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: avgs,
                        backgroundColor: colors,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => `${ctx.label}: ${ctx.parsed}%`
                            }
                        }
                    }
                }
            });
        });
    </script>

@endsection
