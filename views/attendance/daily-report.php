<?php $slug = tenant_slug(); ?>
<div class="page-header">
    <h1>📊 Daily Attendance Report — <?= date('d M Y', strtotime($date)) ?></h1>
    <div class="actions">
        <form method="GET" action="<?= url("$slug/attendance/daily-report") ?>" style="display:flex;gap:8px;align-items:flex-end">
            <input type="date" name="date" value="<?= $date ?>" class="btn btn-outline" style="padding:8px 12px;background:rgba(15,23,42,.6);border:1px solid rgba(99,102,241,.2);color:#e2e8f0">
            <button class="btn btn-primary">View</button>
        </form>
        <a href="<?= url("$slug/attendance") ?>" class="btn btn-outline">← Back</a>
    </div>
</div>

<div class="card">
    <?php if (empty($report)): ?>
        <div class="empty">No attendance data for this date.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Class</th>
                    <th style="text-align:center">✅ Present</th>
                    <th style="text-align:center">❌ Absent</th>
                    <th style="text-align:center">⏰ Late</th>
                    <th style="text-align:center">Total</th>
                    <th>Attendance %</th>
                </tr>
            </thead>
            <tbody>
                <?php $totalP = 0; $totalA = 0; $totalL = 0; $totalAll = 0; ?>
                <?php foreach ($report as $r): ?>
                <?php
                    $totalP += $r['present'];
                    $totalA += $r['absent'];
                    $totalL += $r['late'];
                    $totalAll += $r['total'];
                    $pct = $r['total'] > 0 ? round(($r['present'] / $r['total']) * 100, 1) : 0;
                ?>
                <tr>
                    <td><strong><?= htmlspecialchars($r['class_name']) ?></strong></td>
                    <td style="text-align:center;color:#86efac"><?= $r['present'] ?></td>
                    <td style="text-align:center;color:#fca5a5"><?= $r['absent'] ?></td>
                    <td style="text-align:center;color:#fde68a"><?= $r['late'] ?></td>
                    <td style="text-align:center"><?= $r['total'] ?></td>
                    <td>
                        <div style="background:rgba(99,102,241,.1);border-radius:20px;height:8px;width:100px;display:inline-block;vertical-align:middle">
                            <div style="background:linear-gradient(135deg,#6366f1,#8b5cf6);height:100%;border-radius:20px;width:<?= $pct ?>%"></div>
                        </div>
                        <span style="margin-left:6px"><?= $pct ?>%</span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="border-top:2px solid rgba(99,102,241,.2)">
                    <td><strong>Total</strong></td>
                    <td style="text-align:center;color:#86efac"><strong><?= $totalP ?></strong></td>
                    <td style="text-align:center;color:#fca5a5"><strong><?= $totalA ?></strong></td>
                    <td style="text-align:center;color:#fde68a"><strong><?= $totalL ?></strong></td>
                    <td style="text-align:center"><strong><?= $totalAll ?></strong></td>
                    <td><strong><?= $totalAll > 0 ? round(($totalP / $totalAll) * 100, 1) : 0 ?>%</strong></td>
                </tr>
            </tfoot>
        </table>
    <?php endif; ?>
</div>
