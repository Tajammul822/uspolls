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
.details-list li{
    list-style: none;
}
.details-row {
    display: none;
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
                            @foreach ($poll['results'] as $r)
                                <li>
                                    <span style="color: {{ $r['color'] }}">{{ $r['name'] }}</span>
                                    : {{ number_format($r['pct'], 1) }}%
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>


        <!-- Demographic Breakdown by Candidate -->
        {{-- <div class="demographic-section">
            <h2 class="section-title">
                <i class="fas fa-chart-bar"></i> Demographic Breakdown by Candidate
            </h2>
            <div class="demographic-grid">
                <div class="demographic-card">
                    <div class="demographic-title">Candidate Avg %</div>

                    @php
                        $num      = $data->count();
                        $averages = [];

                        // Compute average pct per template candidate
                        for ($i = 0; $i < $colCount; $i++) {
                            $sum = 0;
                            foreach ($data as $poll) {
                                $pctMap = collect($poll['results'])
                                          ->keyBy('name')
                                          ->map->pct
                                          ->toArray();
                                $name   = $template[$i]['name'];
                                $sum   += $pctMap[$name] ?? 0;
                            }
                            $averages[$i] = round($sum / max($num, 1), 1);
                        }
                    @endphp

                    @for ($i = 0; $i < $colCount; $i++)
                        @php
                            $cand = $template[$i];
                            $avg  = $averages[$i] ?? 0;
                        @endphp
                        <div class="demographic-item">
                            <div class="group-label">
                                <span>{{ $cand['name'] }}</span>
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
        </div> --}}

        <!-- Result by Candidate Pie Chart -->
        <div class="sidebar-card">
            <div class="sidebar-title"><i class="fas fa-chart-simple"></i> Result by Candidate</div>
            <div class="charts-section">
                <div class="chart-container">
                    <div class="chart-wrapper">
                        <canvas id="allCandidatesPie"></canvas>
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
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

   <script>
    $(function () {
        $('.toggle-control').click(function () {
            var row = $(this).closest('.polling-row');
            var detail = row.next('.details-row');

            var isVisible = detail.is(':visible');

            detail.toggle(!isVisible);
            $(this).text(isVisible ? '+' : '-');
        });
    });
</script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pollData = @json($data);
            const template = pollData[0].results;
            const numPolls = pollData.length;
            const avgs = template.map(t => {
                let sum = pollData.reduce((acc, p) =>
                    acc + (p.results.find(r => r.name === t.name)?.pct || 0), 0);
                return parseFloat((sum / numPolls).toFixed(1));
            });
            const ctx = document.getElementById('allCandidatesPie').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: template.map(t => t.name),
                    datasets: [{
                        data: avgs,
                        backgroundColor: template.map(t => t.color),
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
                        }
                    }
                }
            });
        });
    </script>

@endsection
