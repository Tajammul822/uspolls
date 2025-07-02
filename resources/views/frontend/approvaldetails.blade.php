{{-- @extends('frontend.layout')
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
            /* padding-bottom: 75%; */
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
                    <div class="approvalfilter">7D</div>
                    <div class="approvalfilter active">1M</div>
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
            renderTable(filterRecords('1M'));

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
@endsection --}}



{{-- @extends('frontend.layout')
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
        }

        /* Rolling Average Chart Styles */
        .rolling-chart-container {
            position: relative;
        }
        
        .rolling-legend {
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .legend-color {
            width: 16px;
            height: 4px;
            border-radius: 2px;
        }
        
        .approve-color {
            background: #1f77b4;
        }
        
        .disapprove-color {
            background: #ff7f0e;
        }
        
        .spread-color {
            background: #2ca02c;
        }
        
        .rolling-controls {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 15px 0;
        }
        
        .rolling-btn {
            padding: 6px 15px;
            border: none;
            border-radius: 20px;
            background: #e0e7ff;
            color: #4f46e5;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 13px;
        }
        
        .rolling-btn:hover {
            background: #4f46e5;
            color: white;
        }
        
        .rolling-btn.active {
            background: #4f46e5;
            color: white;
        }
        
        .average-label {
            font-size: 12px;
            text-align: center;
            margin-top: 10px;
            color: #6c757d;
            font-style: italic;
        }
        
        .rolling-tooltip {
            position: absolute;
            padding: 10px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            border-radius: 4px;
            pointer-events: none;
            font-size: 13px;
            opacity: 0;
            transition: opacity 0.3s;
            z-index: 10;
        }
        
        .tooltip-date {
            font-weight: bold;
            margin-bottom: 5px;
            color: #a5d8ff;
        }
        
        .tooltip-values {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5px;
        }
        
        .tooltip-label {
            text-align: right;
        }
        
        .rolling-info {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 14px;
        }
        
        .spread-value {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
        }
        
        .spread-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #2ca02c;
        }
        
        .date-range {
            font-weight: 500;
            color: #495057;
        }
    </style>

    <div class="container">
        <!-- 1) Approval Table -->
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-table"></i> Latest Approval Data</div>
                <div class="approvalfilters" id="table-filters">
                    <div class="approvalfilter">7D</div>
                    <div class="approvalfilter active">1M</div>
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
            
            <!-- Chart 2: Approval Trend (Rolling Average) -->
            <div class="chart-container">
                <div class="chart-header">
                    <h3 class="chart-title">Approval Trend (Last 30 Days)</h3>
                </div>
                <div class="rolling-chart-container">
                    <div class="rolling-legend">
                        <div class="legend-item">
                            <div class="legend-color approve-color"></div>
                            <span>Approve</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color disapprove-color"></div>
                            <span>Disapprove</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color spread-color"></div>
                            <span>Spread (Disapprove - Approve)</span>
                        </div>
                    </div>
                    
                    <div class="rolling-controls">
                        <button id="btn-7day" class="rolling-btn active">7-Day Average</button>
                        <button id="btn-14day" class="rolling-btn">14-Day Average</button>
                        <button id="btn-weight" class="rolling-btn">Toggle Weighting</button>
                    </div>
                    
                    <div id="trend-chart"></div>
                    <div class="average-label">Poll Average - Smoothed trend reflecting stable public opinion</div>
                    <div class="rolling-tooltip"></div>
                    
                    <div class="rolling-info">
                        <div class="date-range">Loading dates...</div>
                        <div class="spread-value">
                            <div class="spread-indicator"></div>
                            <span>Latest Spread: <strong id="latest-spread">0.0</strong></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://d3js.org/d3.v7.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const records = @json($records)
                .sort((a, b) => new Date(a.rawDate) - new Date(b.rawDate));

            // Preprocess records - convert to numbers
            const numericRecords = records.map(r => {
                return {
                    ...r,
                    date: new Date(r.rawDate),
                    sampleSize: parseInt(r.sampleSize.replace(/,/g, '')),
                    approve: r.approve,
                    disapprove: r.disapprove,
                    net: r.net
                };
            });

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
            renderTable(filterRecords('1M'));

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

            // — Rolling Average Chart (Approval Trend) —
            function calculateRollingAverage(polls, windowSize, weighted) {
                const dates = [];
                const results = [];
                
                // Get all unique dates
                const uniqueDates = [...new Set(polls.map(p => p.date.getTime()))]
                    .sort((a, b) => a - b)
                    .map(t => new Date(t));
                
                for (const date of uniqueDates) {
                    const windowStart = new Date(date);
                    windowStart.setDate(windowStart.getDate() - windowSize);
                    
                    const pollsInWindow = polls.filter(p => 
                        p.date >= windowStart && p.date <= date
                    );
                    
                    if (pollsInWindow.length === 0) continue;
                    
                    let totalApprove = 0;
                    let totalDisapprove = 0;
                    let totalWeight = 0;
                    
                    for (const poll of pollsInWindow) {
                        const weight = weighted ? poll.sampleSize : 1;
                        totalApprove += poll.approve * weight;
                        totalDisapprove += poll.disapprove * weight;
                        totalWeight += weight;
                    }
                    
                    const avgApprove = totalApprove / totalWeight;
                    const avgDisapprove = totalDisapprove / totalWeight;
                    const spread = avgDisapprove - avgApprove;
                    
                    results.push({
                        date: date,
                        approve: parseFloat(avgApprove.toFixed(1)),
                        disapprove: parseFloat(avgDisapprove.toFixed(1)),
                        spread: parseFloat(spread.toFixed(1))
                    });
                }
                
                return results;
            }
            
            // Main function to render the chart
            function renderRollingChart(windowSize = 7, weighted = false) {
                // Remove existing chart
                d3.select("#trend-chart").select("svg").remove();
                
                // Filter to last 45 days to get enough data for rolling average
                const now = new Date();
                const cutoff = new Date(now.getFullYear(), now.getMonth(), now.getDate() - 45);
                const recentData = numericRecords.filter(r => r.date >= cutoff);
                
                if (recentData.length === 0) {
                    document.getElementById('trend-chart').innerHTML = `
                        <div style="text-align:center; padding:40px; color:#666">
                            <i class="fas fa-exclamation-circle" style="font-size:48px; margin-bottom:15px"></i>
                            <h3>No recent polling data available</h3>
                            <p>Try adjusting the date range</p>
                        </div>
                    `;
                    return;
                }
                
                // Calculate rolling averages
                const rollingData = calculateRollingAverage(recentData, windowSize, weighted);
                
                // Update date range display
                const startDate = rollingData[0].date;
                const endDate = rollingData[rollingData.length - 1].date;
                document.querySelector('.date-range').textContent = 
                    `${startDate.toLocaleDateString('en-US', {month: 'short', day: 'numeric'})} - 
                    ${endDate.toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}`;
                
                // Update latest spread
                const latest = rollingData[rollingData.length - 1];
                document.getElementById('latest-spread').textContent = latest.spread.toFixed(1);
                
                // Set dimensions and margins
                const margin = {top: 20, right: 50, bottom: 60, left: 60};
                const width = 650 - margin.left - margin.right;
                const height = 300 - margin.top - margin.bottom;
                
                // Create SVG element
                const svg = d3.select("#trend-chart")
                    .append("svg")
                    .attr("width", width + margin.left + margin.right)
                    .attr("height", height + margin.top + margin.bottom)
                    .append("g")
                    .attr("transform", `translate(${margin.left},${margin.top})`);
                
                // Create scales
                const xScale = d3.scaleTime()
                    .domain(d3.extent(rollingData, d => d.date))
                    .range([0, width]);
                
                const yScale = d3.scaleLinear()
                    .domain([d3.min(rollingData, d => Math.min(d.approve, d.disapprove)) - 2, 
                             d3.max(rollingData, d => Math.max(d.approve, d.disapprove)) + 2])
                    .range([height, 0]);
                
                const ySpreadScale = d3.scaleLinear()
                    .domain([d3.min(rollingData, d => d.spread) - 1, 
                             d3.max(rollingData, d => d.spread) + 1])
                    .range([height, 0]);
                
                // Create axes
                const xAxis = d3.axisBottom(xScale)
                    .tickFormat(d3.timeFormat("%b %d"))
                    .ticks(8);
                
                const yAxis = d3.axisLeft(yScale)
                    .ticks(5);
                
                const ySpreadAxis = d3.axisRight(ySpreadScale)
                    .ticks(5);
                
                // Add X axis
                svg.append("g")
                    .attr("transform", `translate(0,${height})`)
                    .call(xAxis)
                    .selectAll("text")
                    .attr("transform", "rotate(-20)")
                    .attr("text-anchor", "end")
                    .attr("dx", "-0.5em")
                    .attr("dy", "0.5em");
                
                // Add Y axis (approval)
                svg.append("g")
                    .call(yAxis)
                    .append("text")
                    .attr("transform", "rotate(-90)")
                    .attr("y", -45)
                    .attr("x", -height/2)
                    .attr("dy", "0.71em")
                    .attr("fill", "#666")
                    .attr("text-anchor", "middle")
                    .text("Approval Rating (%)");
                
                // Add Y axis (spread)
                svg.append("g")
                    .attr("transform", `translate(${width},0)`)
                    .call(ySpreadAxis)
                    .append("text")
                    .attr("transform", "rotate(90)")
                    .attr("y", 45)
                    .attr("x", height/2)
                    .attr("dy", "0.71em")
                    .attr("fill", "#666")
                    .attr("text-anchor", "middle")
                    .text("Spread (Disapprove - Approve)");
                
                // Add horizontal grid lines
                svg.selectAll("yGrid")
                    .data(yScale.ticks())
                    .join("line")
                    .attr("x1", 0)
                    .attr("x2", width)
                    .attr("y1", d => yScale(d))
                    .attr("y2", d => yScale(d))
                    .attr("stroke", "#f0f0f0")
                    .attr("stroke-width", 0.8);
                
                // Add zero line for spread
                svg.append("line")
                    .attr("class", "zero-line")
                    .attr("x1", 0)
                    .attr("x2", width)
                    .attr("y1", ySpreadScale(0))
                    .attr("y2", ySpreadScale(0))
                    .attr("stroke", "#999")
                    .attr("stroke-dasharray", "4,4")
                    .attr("stroke-width", 1);
                
                // Line generator functions
                const approveLine = d3.line()
                    .x(d => xScale(d.date))
                    .y(d => yScale(d.approve))
                    .curve(d3.curveMonotoneX);
                
                const disapproveLine = d3.line()
                    .x(d => xScale(d.date))
                    .y(d => yScale(d.disapprove))
                    .curve(d3.curveMonotoneX);
                
                const spreadLine = d3.line()
                    .x(d => xScale(d.date))
                    .y(d => ySpreadScale(d.spread))
                    .curve(d3.curveMonotoneX);
                
                // Draw approval line
                svg.append("path")
                    .datum(rollingData)
                    .attr("fill", "none")
                    .attr("stroke", "#1f77b4")
                    .attr("stroke-width", 3)
                    .attr("d", approveLine);
                
                // Draw disapproval line
                svg.append("path")
                    .datum(rollingData)
                    .attr("fill", "none")
                    .attr("stroke", "#ff7f0e")
                    .attr("stroke-width", 3)
                    .attr("d", disapproveLine);
                
                // Draw spread line
                svg.append("path")
                    .datum(rollingData)
                    .attr("fill", "none")
                    .attr("stroke", "#2ca02c")
                    .attr("stroke-width", 2)
                    .attr("stroke-dasharray", "4,2")
                    .attr("d", spreadLine);
                
                // Add points for latest data
                const latestData = rollingData[rollingData.length - 1];
                svg.append("circle")
                    .attr("cx", xScale(latestData.date))
                    .attr("cy", yScale(latestData.approve))
                    .attr("r", 4)
                    .attr("fill", "#1f77b4");
                
                svg.append("circle")
                    .attr("cx", xScale(latestData.date))
                    .attr("cy", yScale(latestData.disapprove))
                    .attr("r", 4)
                    .attr("fill", "#ff7f0e");
                
                svg.append("circle")
                    .attr("cx", xScale(latestData.date))
                    .attr("cy", ySpreadScale(latestData.spread))
                    .attr("r", 4)
                    .attr("fill", "#2ca02c");
                
                // Create tooltip
                const tooltip = d3.select(".rolling-tooltip");
                
                // Add mouse interaction
                const focus = svg.append("g")
                    .attr("class", "focus")
                    .style("display", "none");
                
                focus.append("circle")
                    .attr("r", 4.5);
                
                svg.append("rect")
                    .attr("class", "overlay")
                    .attr("width", width)
                    .attr("height", height)
                    .style("fill", "none")
                    .style("pointer-events", "all")
                    .on("mouseover", () => focus.style("display", null))
                    .on("mouseout", () => {
                        focus.style("display", "none");
                        tooltip.style("opacity", 0);
                    })
                    .on("mousemove", mousemove);
                
                function mousemove(event) {
                    const x0 = xScale.invert(d3.pointer(event)[0]);
                    const i = d3.bisector(d => d.date).left(rollingData, x0, 1);
                    const d0 = rollingData[i - 1];
                    const d1 = rollingData[i];
                    const d = x0 - d0.date > d1.date - x0 ? d1 : d0;
                    
                    focus.attr("transform", `translate(${xScale(d.date)},${yScale(d.approve)})`);
                    
                    tooltip
                        .style("opacity", 1)
                        .style("left", (event.pageX + 10) + "px")
                        .style("top", (event.pageY - 30) + "px")
                        .html(`
                            <div class="tooltip-date">${d3.timeFormat("%B %d, %Y")(d.date)}</div>
                            <div class="tooltip-values">
                                <div class="tooltip-label">Approve:</div>
                                <div>${d.approve}%</div>
                                <div class="tooltip-label">Disapprove:</div>
                                <div>${d.disapprove}%</div>
                                <div class="tooltip-label">Spread:</div>
                                <div>${d.spread >= 0 ? '+' : ''}${d.spread}</div>
                            </div>
                        `);
                }
            }
            
            // Initialize the chart
            renderRollingChart();
            
            // Event listeners for buttons
            document.getElementById("btn-7day").addEventListener("click", function() {
                document.querySelectorAll(".rolling-btn").forEach(btn => btn.classList.remove("active"));
                this.classList.add("active");
                renderRollingChart(7, document.getElementById("btn-weight").classList.contains("active"));
            });
            
            document.getElementById("btn-14day").addEventListener("click", function() {
                document.querySelectorAll(".rolling-btn").forEach(btn => btn.classList.remove("active"));
                this.classList.add("active");
                renderRollingChart(14, document.getElementById("btn-weight").classList.contains("active"));
            });
            
            document.getElementById("btn-weight").addEventListener("click", function() {
                this.classList.toggle("active");
                const windowSize = document.getElementById("btn-7day").classList.contains("active") ? 7 : 14;
                renderRollingChart(windowSize, this.classList.contains("active"));
            });
        });
    </script>
@endsection --}}

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

        /* Avg doughnut wrapper stays the same */
        .chart-wrapper {
            position: relative;
            width: 100%;
        }

        /* --- D3 Trend Chart Styles --- */
        /* height for d3 chart */
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
            gap: 30px;
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
        }

        .approve-color {
            background: #1f77b4;
        }

        .disapprove-color {
            background: #ff7f0e;
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

        .tooltip-date {
            font-weight: bold;
            margin-bottom: 4px;
            color: #a5d8ff;
        }

        .tooltip-values {
            display: grid;
            grid-template-columns: auto auto;
            gap: 4px;
        }

        .tooltip-label {
            text-align: right;
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
    </style>

    <div class="container">
        <!-- 1) Approval Table -->
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-table"></i> Latest Approval Data</div>
                <div class="approvalfilters" id="table-filters">
                    <div class="approvalfilter">7D</div>
                    <div class="approvalfilter active">1M</div>
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
                    <tbody id="polling-body"></tbody>
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

            <!-- Chart 3: Approval Trend (Year to Date) using D3 -->
            <div class="chart-container">
                <div class="chart-header">
                    <h3 class="chart-title">Poll Average Trend (Year to Date)</h3>
                </div>

                {{-- Legend --}}
                <div class="legend">
                    <div class="legend-item">
                        <div class="legend-color approve-color"></div>
                        <span>Approve</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color disapprove-color"></div>
                        <span>Disapprove</span>
                    </div>
                </div>

                {{-- Controls --}}
                <div class="controls">
                    <button id="btn-7day" class="active">7‑Day</button>
                    <button id="btn-14day">14‑Day</button>
                    <button id="btn-weight">Weighted</button>
                </div>

                {{-- Subtitle --}}
                <div class="average-label">
                    Poll Average – Smoothed rolling average of all polls (excludes very small samples)
                </div>

                {{-- D3 chart placeholder --}}
                <div id="d3TrendChart"></div>
                <div class="tooltip"></div>

                {{-- Info bar --}}
                <div class="info-bar">
                    <div class="date-range" id="date-range"></div>
                    <div class="date-range">
                        Latest Spread: <strong id="latest-spread"></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://d3js.org/d3.v7.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- Table & Doughnut (unchanged) ---
            const records = @json($records)
                .sort((a, b) => new Date(a.rawDate) - new Date(b.rawDate));

            function filterRecords(tf) {
                const now = new Date(),
                    oneDay = 24 * 60 * 60 * 1000;
                let thresh;
                if (tf === '7D') thresh = new Date(+now - 7 * oneDay);
                else if (tf === '1M') thresh = new Date(now.getFullYear(), now.getMonth() - 1, now.getDate());
                else if (tf === '1Y') thresh = new Date(now.getFullYear() - 1, now.getMonth(), now.getDate());
                else thresh = new Date(0);
                return records.filter(r => new Date(r.rawDate) >= thresh);
            }

            function renderTable(recs) {
                $('#polling-body').html(recs.map(r => `
        <tr>
          <td>${r.pollster}</td>
          <td>${r.displayDate}</td>
          <td>${r.sampleSize}</td>
          <td class="poll-result ${r.approve>=r.disapprove?'positive':'negative'}">${r.approve}%</td>
          <td class="poll-result ${r.disapprove>r.approve?'negative':'positive'}">${r.disapprove}%</td>
          <td class="poll-result ${r.net>=0?'positive':'negative'}">${r.net>=0? '+'+r.net:r.net}%</td>
        </tr>`).join(''));
            }
            $('#table-filters .approvalfilter').on('click', function() {
                $('#table-filters .approvalfilter').removeClass('active');
                $(this).addClass('active');
                renderTable(filterRecords($(this).text()));
            });
            renderTable(filterRecords('1M'));

            // Doughnut
            const total = records.length;
            const sumA = records.reduce((s, r) => s + r.approve, 0),
                sumD = records.reduce((s, r) => s + r.disapprove, 0);
            const avgA = +(sumA / total).toFixed(1),
                avgD = +(sumD / total).toFixed(1);
            const legendLabels = [
                `Approve (${avgA}%)`,
                `Disapprove (${avgD}%)`
            ];

            new Chart($('#avgChart')[0].getContext('2d'), {
                type: 'doughnut',
                data: {
                    // swap in the labels with percentages
                    labels: legendLabels,
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
                            position: 'bottom',
                            labels: {
                                // optionally, increase box size
                                boxWidth: 12,
                                padding: 20
                            }
                        },
                        datalabels: {
                            color: ctx => ctx.dataset.backgroundColor[ctx.dataIndex],
                            formatter: v => v + '%',
                            font: {
                                weight: 'bold',
                                size: 14
                            }
                        }
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // prepare data, year-to-date, exclude sampleSize < 500
            const raw = @json($records).map(d => ({
                date: new Date(d.rawDate),
                approve: +d.approve,
                disapprove: +d.disapprove,
                sampleSize: +d.sampleSize.replace(/,/g, '')
            })).filter(d => d.sampleSize >= 500);

            const today = new Date(),
                startOfYear = new Date(today.getFullYear(), 0, 1),
                polls = raw.filter(d => d.date >= startOfYear && d.date <= today);

            // show date range
            d3.select('#date-range')
                .text(d3.timeFormat("%b %d, %Y")(startOfYear) + ' – ' + d3.timeFormat("%b %d, %Y")(today));

            // rolling average calc
            function calcRoll(windowSize, weighted) {
                const dates = Array.from(new Set(polls.map(p => +p.date))).sort().map(t => new Date(t));
                return dates.map(date => {
                    const windowStart = d3.timeDay.offset(date, -windowSize);
                    const inWin = polls.filter(p => p.date >= windowStart && p.date <= date);
                    const w = weighted ? d => d.sampleSize : _ => 1;
                    const W = d3.sum(inWin, d => w(d));
                    return {
                        date,
                        approve: +(d3.sum(inWin, d => d.approve * w(d)) / W).toFixed(1),
                        disapprove: +(d3.sum(inWin, d => d.disapprove * w(d)) / W).toFixed(1),
                        spread: +((d3.sum(inWin, d => d.disapprove * w(d)) - d3.sum(inWin, d => d.approve *
                            w(d))) / W).toFixed(1)
                    };
                }).filter(d => !isNaN(d.approve));
            }

            // render function
            function render(windowSize = 7, weighted = false) {
                d3.select('#d3TrendChart').selectAll('*').remove();
                const data = calcRoll(windowSize, weighted);
                const last = data[data.length - 1];
                d3.select('#latest-spread').text(last.spread);

                const margin = {
                        top: 20,
                        right: 50,
                        bottom: 60,
                        left: 60
                    },
                    W = document.getElementById('d3TrendChart').clientWidth - margin.left - margin.right,
                    H = 400 - margin.top - margin.bottom;

                const svg = d3.select('#d3TrendChart').append('svg')
                    .attr('width', W + margin.left + margin.right)
                    .attr('height', H + margin.top + margin.bottom)
                    .append('g')
                    .attr('transform', `translate(${margin.left},${margin.top})`);

                const x = d3.scaleTime()
                    .domain(d3.extent(data, d => d.date))
                    .range([0, W]);
                const y = d3.scaleLinear()
                    .domain([
                        d3.min(data, d => d.approve) - 2,
                        d3.max(data, d => d.disapprove) + 2
                    ])
                    .range([H, 0]);

                svg.append('g')
                    .attr('transform', `translate(0,${H})`)
                    .call(d3.axisBottom(x).ticks(10).tickFormat(d3.timeFormat("%b %d")))
                    .selectAll("text")
                    .attr("transform", "rotate(-20)")
                    .attr("text-anchor", "end")
                    .attr("dx", "-0.5em")
                    .attr("dy", "0.5em");

                svg.append('g').call(d3.axisLeft(y));

                const lineA = d3.line().curve(d3.curveMonotoneX).x(d => x(d.date)).y(d => y(d.approve));
                const lineD = d3.line().curve(d3.curveMonotoneX).x(d => x(d.date)).y(d => y(d.disapprove));

                svg.append('path').datum(data)
                    .attr('fill', 'none').attr('stroke', '#1f77b4').attr('stroke-width', 3)
                    .attr('d', lineA);

                svg.append('path').datum(data)
                    .attr('fill', 'none').attr('stroke', '#ff7f0e').attr('stroke-width', 3)
                    .attr('d', lineD);

                // tooltip overlay
                const bis = d3.bisector(d => d.date).left;
                const tooltip = d3.select('.tooltip');
                svg.append('rect')
                    .attr('width', W).attr('height', H)
                    .style('fill', 'none').style('pointer-events', 'all')
                    .on('mousemove', event => {
                        const [mx] = d3.pointer(event);
                        const xm = x.invert(mx);
                        const i = bis(data, xm, 1);
                        const d0 = data[i - 1],
                            d1 = data[i] || d0;
                        const d = xm - d0.date > d1.date - xm ? d1 : d0;
                        tooltip
                            .style('opacity', 1)
                            .style('left', event.pageX + 10 + 'px')
                            .style('top', event.pageY - 28 + 'px')
                            .html(`
                       <div class="tooltip-date">${d3.timeFormat("%B %d, %Y")(d.date)}</div>
                       <div class="tooltip-values">
                         <div class="tooltip-label">Approve:</div><div>${d.approve}%</div>
                         <div class="tooltip-label">Disapprove:</div><div>${d.disapprove}%</div>
                       </div>`);
                    })
                    .on('mouseout', () => tooltip.style('opacity', 0));
            }

            // wire buttons
            d3.selectAll('.controls button').on('click', function() {
                d3.selectAll('.controls button').classed('active', false);
                d3.select(this).classed('active', true);
                const win = d3.select('#btn-7day').classed('active') ? 7 : 14;
                const wt = d3.select('#btn-weight').classed('active');
                render(win, wt);
            });

            // initial render
            render();
        });
    </script>
@endsection
