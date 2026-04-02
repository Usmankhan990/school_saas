<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Finance Dashboard — School SaaS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',sans-serif;background:#0f172a;color:#e2e8f0;min-height:100vh}
        .top-bar{background:rgba(15,23,42,.95);border-bottom:1px solid rgba(99,102,241,.15);padding:16px 30px;display:flex;align-items:center;justify-content:space-between}
        .top-bar h1{font-size:1.3rem;background:linear-gradient(135deg,#6366f1,#8b5cf6);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
        .top-bar a{color:#818cf8;text-decoration:none;font-size:.9rem}
        .container{max-width:1200px;margin:0 auto;padding:20px 30px}
        .stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:30px}
        .stat{background:rgba(30,41,59,.7);border:1px solid rgba(99,102,241,.12);border-radius:16px;padding:20px;position:relative;overflow:hidden}
        .stat::after{content:'';position:absolute;top:0;right:0;width:80px;height:80px;border-radius:0 16px 0 80px;opacity:.08}
        .stat.green::after{background:#22c55e} .stat.blue::after{background:#3b82f6} .stat.orange::after{background:#f59e0b} .stat.purple::after{background:#8b5cf6} .stat.red::after{background:#ef4444}
        .stat small{color:#94a3b8;font-size:.8rem;text-transform:uppercase;letter-spacing:1px}
        .stat h2{font-size:1.8rem;margin-top:6px;font-weight:700}
        .stat.green h2{color:#22c55e} .stat.blue h2{color:#60a5fa} .stat.orange h2{color:#fbbf24} .stat.purple h2{color:#a78bfa} .stat.red h2{color:#f87171}
        .card{background:rgba(30,41,59,.7);border:1px solid rgba(99,102,241,.12);border-radius:16px;padding:24px;margin-bottom:20px}
        .card h3{margin-bottom:16px;color:#c7d2fe;font-size:1.1rem}
        table{width:100%;border-collapse:collapse}
        th{text-align:left;padding:10px 12px;color:#94a3b8;font-size:.8rem;text-transform:uppercase;border-bottom:1px solid rgba(99,102,241,.1)}
        td{padding:10px 12px;border-bottom:1px solid rgba(99,102,241,.06)}
        .badge{background:rgba(99,102,241,.15);color:#818cf8;padding:3px 10px;border-radius:8px;font-size:.75rem}
        canvas{max-height:300px}
    </style>
</head>
<body>
    <div class="top-bar">
        <h1>🌐 Global Finance Dashboard</h1>
        <a href="<?= url('schools') ?>">← Back to Schools</a>
    </div>
    <div class="container">
        <div class="stats">
            <div class="stat green"><small>Total Revenue</small><h2><?= number_format($totalRevenue, 0) ?></h2></div>
            <div class="stat blue"><small>Today's Collection</small><h2><?= number_format($todayCollection, 0) ?></h2></div>
            <div class="stat purple"><small>Monthly Revenue</small><h2><?= number_format($monthlyRevenue, 0) ?></h2></div>
            <div class="stat orange"><small>Paid Fees</small><h2><?= number_format($paidAmount, 0) ?></h2></div>
            <div class="stat red"><small>Pending Fees</small><h2><?= number_format($pendingAmount, 0) ?></h2></div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
            <div class="card">
                <h3>📈 Monthly Revenue (<?= date('Y') ?>)</h3>
                <canvas id="revenueChart"></canvas>
            </div>
            <div class="card">
                <h3>🏫 School-wise Revenue</h3>
                <canvas id="schoolChart"></canvas>
            </div>
        </div>

        <div class="card">
            <h3>🏫 School-wise Breakdown</h3>
            <table>
                <thead><tr><th>School</th><th>Total Revenue</th><th>Today</th><th>Pending</th></tr></thead>
                <tbody>
                    <?php foreach ($schoolWise as $sw): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($sw['school_name']) ?></strong></td>
                        <td style="color:#22c55e;font-weight:600"><?= number_format($sw['total_revenue'], 0) ?></td>
                        <td style="color:#60a5fa"><?= number_format($sw['today_collection'], 0) ?></td>
                        <td style="color:#f87171"><?= number_format($sw['pending_amount'], 0) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($schoolWise)): ?>
                    <tr><td colspan="4" style="text-align:center;color:#64748b">No data yet</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        const monthlyData = <?= json_encode($monthlyChart) ?>;
        const chartData = new Array(12).fill(0);
        monthlyData.forEach(d => chartData[d.month - 1] = parseFloat(d.total));

        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: { labels: months, datasets: [{ label: 'Revenue', data: chartData, borderColor: '#6366f1', backgroundColor: 'rgba(99,102,241,.1)', fill: true, tension: .4 }] },
            options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(99,102,241,.06)' } }, x: { ticks: { color: '#94a3b8' }, grid: { display: false } } } }
        });

        const schoolData = <?= json_encode($schoolWise) ?>;
        new Chart(document.getElementById('schoolChart'), {
            type: 'doughnut',
            data: { labels: schoolData.map(s => s.school_name), datasets: [{ data: schoolData.map(s => parseFloat(s.total_revenue)), backgroundColor: ['#6366f1','#22c55e','#f59e0b','#ef4444','#3b82f6','#8b5cf6','#ec4899'] }] },
            options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { color: '#94a3b8' } } } }
        });
    </script>
</body>
</html>
