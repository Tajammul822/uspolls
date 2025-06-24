@extends('frontend.layout')
@section('content')
    <style>
        .container {
            display: flex;
            flex-direction: column;
            padding: 20px;
            gap: 30px;
            max-width: 100%;
            margin: 0 auto;
        }

        /* Table Card */
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 20px;
            font-weight: 600;
        }

        .approvalfilters {
            display: flex;
            gap: 10px;
        }

        .approvalfilter {
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
            user-select: none;
        }

        .approvalfilter.active {
            background: #3182ce;
            color: white;
        }

        .polling-table-container {
            overflow-x: auto;
            padding: 15px;
        }

        .polling-table {
            width: 100%;
            border-collapse: collapse;
        }

        .polling-table th,
        .polling-table td {
            padding: 10px;
            border-bottom: 1px solid #edf2f7;
            text-align: left;
        }

        .polling-table th {
            background: #f7fafc;
            font-weight: 600;
        }

        .poll-result.positive {
            color: #38a169;
        }

        .poll-result.negative {
            color: #e53e3e;
        }

        /* Charts Section */
        .charts-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .chart-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 20px;
        }

        .chart-header {
            margin-bottom: 15px;
        }

        .chart-title {
            font-size: 18px;
            font-weight: 600;
        }

        .chart-wrapper {
            position: relative;
            width: 100%;
            padding-bottom: 75%;
        }

        .chart-wrapper canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100% !important;
            height: 100% !important;
        }
    </style>

    <div class="container">
        <!-- 1) Approval Table -->
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-table"></i> Latest Approval Data</div>
                <div class="approvalfilters" id="table-filters">
                    <div class="approvalfilter active">7D</div>
                    <div class="approvalfilter">1M</div>
                    <div class="approvalfilter">1Y</div>
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

        <!-- 2) Charts -->
        <div class="charts-section">
            <!-- Chart 1: Avg Approve vs Disapprove -->
            <div class="chart-container">
                <div class="chart-header">
                    <h3 class="chart-title">Average Approve vs Disapprove</h3>
                </div>
                <div class="chart-wrapper">
                    <canvas id="avgChart"></canvas>
                </div>
            </div>

            <!-- Chart 2: Approve/Disapprove by Pollster (BAR) -->
            <div class="chart-container">
                <div class="chart-header">
                    <h3 class="chart-title">Approve & Disapprove by Pollster</h3>
                </div>
                <div class="chart-wrapper">
                    <canvas id="trendBarChart"></canvas>
                </div>
            </div>
        </div>
        <!-- Chart 3: Approval Trend (Last 30 Days) (LINE) -->
        <div class="chart-container">
            <div class="chart-header">
                <h3 class="chart-title">Approval Trend (Last 30 Days)</h3>
            </div>
            <div class="chart-wrapper">
                <canvas id="trendLineChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const records = @json($records)
                .sort((a, b) => new Date(a.rawDate) - new Date(b.rawDate));

            // — Table Filtering & Rendering —
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
                return records.filter(r => new Date(r.rawDate) >= thresh);
            }

            function renderTable(recs) {
                document.getElementById('polling-body').innerHTML = recs.map(r => `
                <tr>
                  <td>${r.pollster}</td>
                  <td>${r.displayDate}</td>
                  <td>${r.sampleSize}</td>
                  <td class="poll-result ${r.approve>=r.disapprove?'positive':'negative'}">${r.approve}%</td>
                  <td class="poll-result ${r.disapprove>r.approve?'negative':'positive'}">${r.disapprove}%</td>
                  <td class="poll-result ${r.net>=0?'positive':'negative'}">${r.net>=0? '+'+r.net : r.net}%</td>
                </tr>`).join('');
            }
            // Wire filter buttons
            document.querySelectorAll('#table-filters .approvalfilter').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('#table-filters .approvalfilter').forEach(b => b
                        .classList.remove('active'));
                    btn.classList.add('active');
                    renderTable(filterRecords(btn.textContent));
                });
            });
            renderTable(filterRecords('7D'));

            // — Chart 1: Doughnut —
            const total = records.length;
            const sumA = records.reduce((s, r) => s + r.approve, 0);
            const sumD = records.reduce((s, r) => s + r.disapprove, 0);
            const avgA = +(sumA / total).toFixed(1),
                avgD = +(sumD / total).toFixed(1);
            new Chart(document.getElementById('avgChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Approve', 'Disapprove'],
                    datasets: [{
                        data: [avgA, avgD],
                        backgroundColor: ['#38a169', '#e53e3e'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
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

            // — Chart 2: Bar —
            const pollsters = records.map(r => r.pollster),
                approves = records.map(r => r.approve),
                disapproves = records.map(r => r.disapprove);
            new Chart(document.getElementById('trendBarChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: pollsters,
                    datasets: [{
                            label: 'Approve',
                            data: approves,
                            backgroundColor: '#38a169',
                            borderRadius: 4
                        },
                        {
                            label: 'Disapprove',
                            data: disapproves,
                            backgroundColor: '#e53e3e',
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => `${ctx.dataset.label}: ${ctx.parsed.y}%`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: v => v + '%'
                            }
                        }
                    }
                }
            });

            // — Chart 3: Line (Last 30 days) —
            const now = new Date(),
                cutoff = new Date(+now - 30 * 24 * 60 * 60 * 1000);
            const last30 = records.filter(r => new Date(r.rawDate) >= cutoff),
                dates = last30.map(r => r.displayDate),
                apprs = last30.map(r => r.approve),
                disps = last30.map(r => r.disapprove);
            new Chart(document.getElementById('trendLineChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [{
                            label: 'Approve',
                            data: apprs,
                            borderColor: '#38a169',
                            backgroundColor: 'rgba(56,161,105,0.1)',
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Disapprove',
                            data: disps,
                            borderColor: '#e53e3e',
                            backgroundColor: 'rgba(229,62,62,0.1)',
                            tension: 0.3,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: v => v + '%'
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
