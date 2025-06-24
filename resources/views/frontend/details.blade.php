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

    </style>

    <div class="main-content">
        <!-- Polling Table -->
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-table"></i> Polling Data</div>
            </div>
            <div class="polling-table-container">
                <table class="polling-table">
                    <thead>
                        <tr>
                            <th>race_type</th>
                            <th>race_label</th>
                            <th>Pollster</th>
                            <th>Date</th>
                            <th>Sample</th>
                            @if (!empty($data[0]['results']))
                                @foreach ($data[0]['results'] as $cand)
                                    <th style="color: {{ $cand['color'] }}">
                                        {{ $cand['name'] }}
                                    </th>
                                @endforeach
                            @endif
                            <th>Net</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $poll)
                            <tr>
                                <td>{{ $poll['race_type'] }}</td>
                                <td>{{ $poll['race_label'] }}</td>
                                <td>{{ $poll['pollster'] }}</td>
                                <td>{{ $poll['date'] }}</td>
                                <td>{{ $poll['sample'] }}</td>
                                @foreach ($poll['results'] as $cand)
                                    <td style="color: {{ $cand['color'] }}">
                                        {{ number_format($cand['pct'], 1) }}%
                                    </td>
                                @endforeach
                                <td style="color: {{ $poll['net_color'] }}">
                                    {{ $poll['net'] }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
                        // precompute averages in Blade
                        $num = $data->count();
                        $averages = [];
                        for ($i = 0; $i < count($data[0]['results']); $i++) {
                            $sum = 0;
                            foreach ($data as $poll) {
                                $sum += $poll['results'][$i]['pct'];
                            }
                            $averages[$i] = round($sum / $num, 1);
                        }
                    @endphp

                    @foreach ($data[0]['results'] as $i => $cand)
                        <div class="demographic-item">
                            <div class="group-label">
                                <span>{{ $cand['party'] }}</span>
                                <span>{{ $averages[$i] }}%</span>
                            </div>
                            <div class="demographic-bar">
                                <div class="demographic-fill"
                                    style="width: {{ $averages[$i] }}%; background-color: {{ $cand['color'] }};">
                                </div>
                            </div>
                            <div class="demographic-labels">
                                <span>0%</span>
                                <span>100%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar (unchanged) -->
    <div class="sidebar">
        <!-- Featured Polls -->
        <div class="sidebar-card">
            <div class="sidebar-title"><i class="fas fa-star"></i> Featured Polls</div>
            @if ($featuredPolls->isEmpty())
                <p class="p-3 text-sm text-gray-500">No featured polls at the moment.</p>
            @else
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr>
                            <th class="border-b px-2 py-1">Pollster</th>
                            <th class="border-b px-2 py-1">Sample Size</th>
                            <th class="border-b px-2 py-1">Net Margin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($featuredPolls as $fp)
                            <tr>
                                <td class="border-b px-2 py-1">{{ $fp['pollster'] }}</td>
                                <td class="border-b px-2 py-1">{{ number_format($fp['sample_size']) }}</td>
                                <td class="border-b px-2 py-1">{{ $fp['net_margin'] }}%</td>
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
        document.addEventListener('DOMContentLoaded', function() {
            const pollData = @json($data);
            const numPolls = pollData.length;
            const candidateCount = pollData[0].results.length;

            // compute avg % per candidate
            const avgs = Array(candidateCount).fill(0);
            pollData.forEach(p => {
                p.results.forEach((r, idx) => {
                    avgs[idx] += r.pct;
                });
            });
            for (let i = 0; i < candidateCount; i++) {
                avgs[i] = parseFloat((avgs[i] / numPolls).toFixed(1));
            }

            // prepare labels & colors
            const labels = pollData[0].results.map(r => r.name);
            const colors = pollData[0].results.map(r => r.color);

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
