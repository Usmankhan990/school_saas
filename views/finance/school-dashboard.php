<?php $slug = tenant_slug(); $currency = Session::get('tenant_currency') ?: 'PKR'; ?>
<div class="page-header"><h1>📊 Finance Dashboard</h1></div>

<!-- Stat Cards -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:24px">
    <div class="card" style="text-align:center;padding:20px;border-left:4px solid #6366f1">
        <small style="color:#94a3b8">Total Students</small>
        <h2 style="margin:6px 0 0;color:#818cf8"><?= number_format($totalStudents) ?></h2>
    </div>
    <div class="card" style="text-align:center;padding:20px;border-left:4px solid #22c55e">
        <small style="color:#94a3b8">Total Revenue</small>
        <h2 style="margin:6px 0 0;color:#22c55e"><?= number_format($totalRevenue, 0) ?></h2>
    </div>
    <div class="card" style="text-align:center;padding:20px;border-left:4px solid #3b82f6">
        <small style="color:#94a3b8">Today's Collection</small>
        <h2 style="margin:6px 0 0;color:#60a5fa"><?= number_format($todayCollection, 0) ?></h2>
    </div>
    <div class="card" style="text-align:center;padding:20px;border-left:4px solid #f59e0b">
        <small style="color:#94a3b8">Monthly Revenue</small>
        <h2 style="margin:6px 0 0;color:#fbbf24"><?= number_format($monthlyRevenue, 0) ?></h2>
    </div>
    <div class="card" style="text-align:center;padding:20px;border-left:4px solid #ef4444">
        <small style="color:#94a3b8">Pending Dues</small>
        <h2 style="margin:6px 0 0;color:#f87171"><?= number_format($totalPending, 0) ?></h2>
    </div>
</div>

<!-- Fee Overview -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:14px;margin-bottom:24px">
    <div class="card" style="padding:20px">
        <h4 style="color:#c7d2fe;margin-bottom:12px">Fee Summary</h4>
        <div style="display:flex;justify-content:space-between;margin-bottom:8px"><span style="color:#94a3b8">Total Assigned</span><strong><?= number_format($totalAssigned, 0) ?></strong></div>
        <div style="display:flex;justify-content:space-between;margin-bottom:8px"><span style="color:#94a3b8">Collected</span><strong style="color:#22c55e"><?= number_format($totalCollected, 0) ?></strong></div>
        <div style="display:flex;justify-content:space-between"><span style="color:#94a3b8">Pending</span><strong style="color:#ef4444"><?= number_format($totalPending, 0) ?></strong></div>
        <?php $pct = $totalAssigned > 0 ? round(($totalCollected / $totalAssigned) * 100) : 0; ?>
        <div style="background:rgba(99,102,241,.1);border-radius:8px;height:8px;margin-top:14px;overflow:hidden">
            <div style="background:linear-gradient(90deg,#22c55e,#10b981);height:100%;width:<?= $pct ?>%;border-radius:8px"></div>
        </div>
        <small style="color:#64748b;margin-top:4px;display:block"><?= $pct ?>% collected</small>
    </div>
    <div class="card" style="padding:20px">
        <h4 style="color:#c7d2fe;margin-bottom:12px">Payment Methods</h4>
        <?php foreach ($methodBreakdown as $m): ?>
        <div style="display:flex;justify-content:space-between;margin-bottom:8px">
            <span class="badge"><?= strtoupper($m['payment_method']) ?></span>
            <strong style="color:#22c55e"><?= number_format($m['total'], 0) ?> (<?= $m['cnt'] ?>x)</strong>
        </div>
        <?php endforeach; ?>
        <?php if (empty($methodBreakdown)): ?><p style="color:#64748b">No payments yet</p><?php endif; ?>
    </div>
</div>

<!-- Charts -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px">
    <div class="card"><h4 style="color:#c7d2fe;margin-bottom:12px">📈 Monthly Revenue (<?= date('Y') ?>)</h4><canvas id="monthlyChart" height="200"></canvas></div>
    <div class="card"><h4 style="color:#c7d2fe;margin-bottom:12px">🏫 Class-wise Collection</h4><canvas id="classChart" height="200"></canvas></div>
</div>

<!-- Class-wise Summary -->
<div class="card">
    <h3>🏫 Class-wise Fee Summary</h3>
    <table>
        <thead><tr><th>Class</th><th>Students</th><th>Total Fees</th><th>Collected</th><th>Pending</th><th>%</th></tr></thead>
        <tbody>
            <?php foreach ($classWise as $cw):
                $cwPct = $cw['total_amount'] > 0 ? round(($cw['total_paid']/$cw['total_amount'])*100) : 0;
            ?>
            <tr>
                <td><strong><?= htmlspecialchars($cw['class_name'] ?? '-') ?></strong></td>
                <td><?= $cw['fee_count'] ?></td>
                <td><?= number_format($cw['total_amount'], 0) ?></td>
                <td style="color:#22c55e"><?= number_format($cw['total_paid'], 0) ?></td>
                <td style="color:#ef4444"><?= number_format($cw['total_pending'], 0) ?></td>
                <td>
                    <div style="background:rgba(99,102,241,.1);border-radius:4px;height:6px;width:80px;display:inline-block;vertical-align:middle">
                        <div style="background:#22c55e;height:100%;width:<?= $cwPct ?>%;border-radius:4px"></div>
                    </div>
                    <small style="color:#94a3b8;margin-left:4px"><?= $cwPct ?>%</small>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Recent Payments -->
<div class="card">
    <h3>💳 Recent Payments</h3>
    <?php if (empty($recentPayments)): ?>
        <p style="color:#64748b">No recent payments.</p>
    <?php else: ?>
    <table>
        <thead><tr><th>Date</th><th>Student</th><th>Fee</th><th>Amount</th><th>Method</th></tr></thead>
        <tbody>
            <?php foreach ($recentPayments as $p): ?>
            <tr>
                <td><?= date('d M', strtotime($p['payment_date'])) ?></td>
                <td><?= htmlspecialchars($p['student_name'] ?? '-') ?></td>
                <td><?= htmlspecialchars($p['fee_type_name'] ?? '-') ?></td>
                <td style="color:#22c55e;font-weight:600"><?= number_format($p['amount'], 0) ?></td>
                <td><span class="badge"><?= strtoupper($p['payment_method']) ?></span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
const monthlyData = <?= json_encode($monthlyChart) ?>;
const mData = new Array(12).fill(0);
monthlyData.forEach(d => mData[d.month - 1] = parseFloat(d.total));
new Chart(document.getElementById('monthlyChart'), {
    type: 'bar', data: { labels: months, datasets: [{ label: 'Revenue', data: mData, backgroundColor: 'rgba(99,102,241,.6)', borderRadius: 6 }] },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(99,102,241,.06)' } }, x: { ticks: { color: '#94a3b8' }, grid: { display: false } } } }
});
const classData = <?= json_encode($classWise) ?>;
new Chart(document.getElementById('classChart'), {
    type: 'doughnut', data: { labels: classData.map(c => c.class_name || 'N/A'), datasets: [{ data: classData.map(c => parseFloat(c.total_paid)), backgroundColor: ['#6366f1','#22c55e','#f59e0b','#ef4444','#3b82f6','#8b5cf6','#ec4899','#14b8a6'] }] },
    options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { color: '#94a3b8' } } } }
});
</script>
