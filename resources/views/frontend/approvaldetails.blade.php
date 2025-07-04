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
    const raw = @json($records).map(d => ({
        date: new Date(d.rawDate),
        approve: +d.approve,
        disapprove: +d.disapprove,
        sampleSize: +d.sampleSize.replace(/,/g, '')
    })).filter(d => d.sampleSize >= 500);

    const today       = new Date(),
          startOfYear = new Date(today.getFullYear(), 0, 1);

    d3.select('#date-range')
      .text(
        d3.timeFormat("%b %d, %Y")(startOfYear)
        + ' – ' +
        d3.timeFormat("%b %d, %Y")(today)
      );

    function calcRoll(windowSize, weighted) {
        const dates = d3.timeDay.range(startOfYear, d3.timeDay.offset(today, 1));

        return dates.map(date => {
            const windowStart = d3.timeDay.offset(date, -windowSize);
            const inWin = raw.filter(p => p.date >= windowStart && p.date <= date);

            const w = weighted ? d => d.sampleSize : _ => 1;
            const W = d3.sum(inWin, d => w(d));

            return {
                date,
                approve:    W ? +(d3.sum(inWin, d => d.approve    * w(d)) / W).toFixed(1) : 0,
                disapprove: W ? +(d3.sum(inWin, d => d.disapprove * w(d)) / W).toFixed(1) : 0,
                spread:     W ? +((d3.sum(inWin, d => d.disapprove * w(d))
                                 - d3.sum(inWin, d => d.approve * w(d))) / W).toFixed(1) : 0
            };
        });
    }

    function render(windowSize = 7, weighted = false) {
        d3.select('#d3TrendChart').selectAll('*').remove();

        const data = calcRoll(windowSize, weighted);
        const last = data[data.length - 1];

        d3.select('#latest-spread').text(last.spread);

        const margin = { top:20, right:50, bottom:60, left:60 },
              W = document.getElementById('d3TrendChart').clientWidth - margin.left - margin.right,
              H = 400 - margin.top - margin.bottom;

        const svg = d3.select('#d3TrendChart').append('svg')
            .attr('width', W + margin.left + margin.right)
            .attr('height', H + margin.top + margin.bottom)
          .append('g')
            .attr('transform', `translate(${margin.left},${margin.top})`);

        const x = d3.scaleTime()
            .domain([startOfYear, today])    
            .range([0, W]);

        const y = d3.scaleLinear()
            .domain([
                d3.min(data, d => d.approve) - 2,
                d3.max(data, d => d.disapprove) + 2
            ])
            .range([H, 0]);

        // X-axis
        svg.append('g')
           .attr('transform', `translate(0,${H})`)
           .call(d3.axisBottom(x).ticks(10).tickFormat(d3.timeFormat("%b %d")))
           .selectAll("text")
           .attr("transform", "rotate(-20)")
           .attr("text-anchor", "end");
        svg.append('g').call(d3.axisLeft(y));

  
        const lineA = d3.line().curve(d3.curveMonotoneX)
                          .x(d => x(d.date)).y(d => y(d.approve));
        const lineD = d3.line().curve(d3.curveMonotoneX)
                          .x(d => x(d.date)).y(d => y(d.disapprove));

        svg.append('path').datum(data)
           .attr('fill','none').attr('stroke','#1f77b4').attr('stroke-width',3)
           .attr('d', lineA);

        svg.append('path').datum(data)
           .attr('fill','none').attr('stroke','#ff7f0e').attr('stroke-width',3)
           .attr('d', lineD);

    
        const bis = d3.bisector(d => d.date).left;
        const tooltip = d3.select('.tooltip');

        svg.append('rect')
           .attr('width', W).attr('height', H)
           .style('fill','none').style('pointer-events','all')
           .on('mousemove', event => {
               const [mx] = d3.pointer(event);
               const xm = x.invert(mx);
               const i  = bis(data, xm, 1);
               const d0 = data[i-1], d1 = data[i] || d0;
               const d  = xm - d0.date > d1.date - xm ? d1 : d0;

               tooltip.style('opacity',1)
                      .style('left', event.pageX + 10 + 'px')
                      .style('top',  event.pageY - 28 + 'px')
                      .html(`
                        <div>${d3.timeFormat("%B %d, %Y")(d.date)}</div>
                        <div>Approve: ${d.approve}%</div>
                        <div>Disapprove: ${d.disapprove}%</div>
                      `);
           })
           .on('mouseout', () => tooltip.style('opacity',0));
    }

 
    d3.selectAll('.controls button').on('click', function() {
        d3.selectAll('.controls button').classed('active', false);
        d3.select(this).classed('active', true);
        const win = d3.select('#btn-7day').classed('active') ? 7 : 14;
        const wt  = d3.select('#btn-weight').classed('active');
        render(win, wt);
    });

    render();
});
</script>
@endsection
