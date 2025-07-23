@extends('frontend.layout')
@section('content')
    <style>
        .container {
            display: flex;
            flex-direction: column;
            padding: 10px;
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

        .average-label {
            text-align: center;
        }



        @media (max-width:590px) {}



        @media (max-width:768px) {

            .charts-section {
                grid-template-columns: 1fr;

            }

            .charts-section {
                display: grid;
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .card-title {
                font-size: 20px;
                font-weight: 600;
                color: var(--text-dark);
                width: 60%;
                display: flex;
                align-items: center !important;
                gap: 20px;
            }

            .time-filters,
            .approvalfilters {
                display: flex !important;
                /* gap: 1rem; */
                margin-bottom: 1rem;
                width: 40%;
                justify-content: flex-end !important;
                flex-direction: row;

            }

            .card-title h3 {
                margin-bottom: 0em !important;
            }

        }
    </style>

    {{-- <div class="container"> --}}
    <!-- 1) Approval Table -->
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fas fa-table"></i>
                <h3>Latest Approval Data</h3>
            </div>
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

        <!-- Chart 3: Approval Trend (Last 30 Days) using D3 -->
        <div class="chart-container">
            <div class="chart-header">
                <h3 class="chart-title">Poll Average Trend (Last 30 Days)</h3>
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
    {{-- </div> --}}

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script src="https://d3js.org/d3.v7.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            function adjustColorBrightness(hex, percent) {
                let num = parseInt(hex.replace("#", ""), 16),
                    amt = Math.round(2.55 * percent),
                    R = (num >> 16) + amt,
                    G = (num >> 8 & 0x00FF) + amt,
                    B = (num & 0x0000FF) + amt;

                return "#" + (
                    0x1000000 +
                    (R < 255 ? (R < 1 ? 0 : R) : 255) * 0x10000 +
                    (G < 255 ? (G < 1 ? 0 : G) : 255) * 0x100 +
                    (B < 255 ? (B < 1 ? 0 : B) : 255)
                ).toString(16).slice(1);
            }

            // --- Table & Doughnut (unchanged) ---
            const records = @json($records)
                .sort((a, b) => new Date(b.rawDate) - new Date(a.rawDate));

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
                $('#polling-body').html(
                    recs.map(r => `
                    <tr>
                        <td>${r.pollster}</td>
                        <td>${r.displayDate}</td>
                        <td>${r.sampleSize}</td>
                        <td class="poll-result positive">${r.approve}%</td>
                        <td class="poll-result negative">${r.disapprove}%</td>
                        <td class="poll-result ${r.net >= 0 ? 'positive' : 'negative'}">
                            ${r.net >= 0 ? '+' + r.net : r.net}%
                        </td>
                    </tr>
                `).join('')
                );
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

            // 1) register the center‑logo plugin
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

            Chart.register(ChartDataLabels);

            new Chart($('#avgChart')[0].getContext('2d'), {
                type: 'doughnut',
                data: {
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
            const records = @json($records);

            // Parse and enrich
            const raw = records.map(d => ({
                date: new Date(d.rawDate),
                approve: +d.approve,
                disapprove: +d.disapprove,
                sampleSize: +d.sampleSize.replace(/,/g, ''),
                pollsterRank: d.pollsterRank
            }));

            if (!raw.length) return console.error("No data");

            // Determine window endpoints
            const today = new Date();
            const startDate = d3.timeDay.offset(today, -29);

            // Pollster quality multipliers
            const qualityWeights = {
                'A+': 1.5,
                'A': 1.25,
                'B': 1,
                'C': 0.75,
                'D': 0.5
            };

            // Rolling + Gaussian smoothing
            function calcRoll(windowSize, weighted) {
                const dates = d3.timeDay.range(startDate, d3.timeDay.offset(today, 1));
                const bandwidth = windowSize * 1.2; // a bit wider

                return dates.map(target => {
                    let W = 0,
                        A = 0,
                        D = 0;
                    raw.forEach(p => {
                        const delta = (target - p.date) / (1000 * 60 * 60 * 24);
                        if (delta < 0 || Math.abs(delta) > bandwidth * 3) return;
                        const kernel = Math.exp(-delta * delta / (2 * bandwidth * bandwidth));
                        const baseW = weighted ?
                            p.sampleSize * (qualityWeights[p.pollsterRank] || 1) :
                            1;
                        const w = kernel * baseW;
                        W += w;
                        A += p.approve * w;
                        D += p.disapprove * w;
                    });
                    return W ? {
                        date: target,
                        approve: A / W,
                        disapprove: D / W,
                        spread: (D - A) / W
                    } : null;
                }).filter(d => d);
            }

            function render(windowSize = 7, weighted = false) {
                const data = calcRoll(windowSize, weighted);
                const container = d3.select('#d3TrendChart').html('');

                if (!data.length) {
                    container.html('<div class="no-data">No data for this period.</div>');
                    return;
                }

                // Update info bar
                d3.select('#latest-spread').text(data.slice(-1)[0].spread.toFixed(1) + '%');
                d3.select('#date-range')
                    .text(`${d3.timeFormat("%b %d")(startDate)} – ${d3.timeFormat("%b %d, %Y")(today)}`);

                // Dimensions
                const margin = {
                    top: 20,
                    right: 30,
                    bottom: 50,
                    left: 50
                };
                const W = container.node().clientWidth - margin.left - margin.right;
                const H = 300 - margin.top - margin.bottom;

                // SVG + group
                const svg = container.append('svg')
                    .attr('width', W + margin.left + margin.right)
                    .attr('height', H + margin.top + margin.bottom);

                const g = svg.append('g')
                    .attr('transform', `translate(${margin.left},${margin.top})`);

                // ── TOP‑RIGHT LOGO ──
                const logoSize = 75; // your requested size
                g.append('image')
                    .attr('x', W - logoSize - 5) // 5px from right edge
                    .attr('y', 5) // 5px from top edge
                    .attr('width', logoSize)
                    .attr('height', logoSize)
                    .attr('href', '{{ asset('images/logo.jpg') }}')
                    .style('pointer-events', 'none');

                // Scales
                const x = d3.scaleTime().domain([startDate, today]).range([0, W]);
                const allVals = raw.flatMap(d => [d.approve, d.disapprove]);
                const y = d3.scaleLinear()
                    .domain([Math.max(0, d3.min(allVals) - 5), Math.min(100, d3.max(allVals) + 5)])
                    .range([H, 0]);

                // Axes
                g.append('g')
                    .attr('transform', `translate(0,${H})`)
                    .call(d3.axisBottom(x).ticks(6).tickFormat(d3.timeFormat("%b %d")))
                    .selectAll('text')
                    .attr('transform', 'rotate(-20)')
                    .attr('text-anchor', 'end');

                g.append('g')
                    .call(d3.axisLeft(y).ticks(5).tickFormat(d => d + '%'));

                // Line generators
                const lineApprove = d3.line()
                    .x(d => x(d.date))
                    .y(d => y(d.approve))
                    .curve(d3.curveCatmullRom);
                const lineDisap = d3.line()
                    .x(d => x(d.date))
                    .y(d => y(d.disapprove))
                    .curve(d3.curveCatmullRom);

                // Draw lines
                g.append('path')
                    .datum(data)
                    .attr('d', lineApprove)
                    .attr('fill', 'none')
                    .attr('stroke', '#1f77b4')
                    .attr('stroke-width', 3)
                    .attr('stroke-linecap', 'round');

                g.append('path')
                    .datum(data)
                    .attr('d', lineDisap)
                    .attr('fill', 'none')
                    .attr('stroke', '#ff7f0e')
                    .attr('stroke-width', 3)
                    .attr('stroke-linecap', 'round');

                // Tooltip overlay
                const tooltip = d3.select('.tooltip');
                const bis = d3.bisector(d => d.date).left;

                g.append('rect')
                    .attr('width', W)
                    .attr('height', H)
                    .style('fill', 'none')
                    .style('pointer-events', 'all')
                    .on('mousemove', (e) => {
                        const mx = d3.pointer(e)[0];
                        const date = x.invert(mx);
                        const i = bis(data, date, 1);
                        const d0 = data[i - 1],
                            d1 = data[i] || d0;
                        const d = (date - d0.date > d1.date - date) ? d1 : d0;

                        tooltip.style('opacity', 1)
                            .style('left', `${e.pageX + 10}px`)
                            .style('top', `${e.pageY - 30}px`)
                            .html(`
                                    <div>${d3.timeFormat("%b %d, %Y")(d.date)}</div>
                                    <div>Approve: <strong>${d.approve.toFixed(1)}%</strong></div>
                                    <div>Disapprove: <strong>${d.disapprove.toFixed(1)}%</strong></div>
                                    <div>Spread: <strong>${d.spread.toFixed(1)}%</strong></div>
                                `);
                    })
                    .on('mouseout', () => tooltip.style('opacity', 0));
            }

            // Controls
            d3.selectAll('.controls button').on('click', function() {
                d3.selectAll('.controls button').classed('active', false);
                d3.select(this).classed('active', true);
                const win = d3.select('#btn-14day').classed('active') ? 14 : 7;
                const wt = d3.select('#btn-weight').classed('active');
                render(win, wt);
            });

            // Kick it off
            render();
        });
    </script>
@endsection
