<?php $slug = tenant_slug(); ?>
<div class="page-header">
    <h1>📊 Fee Report</h1>
    <a href="<?= url("$slug/reports/fee-csv") ?>" class="btn btn-outline">⬇ Export CSV</a>
</div>
<div class="card">
    <h3>Class-wise Summary</h3>
    <table>
        <thead><tr><th>Class</th><th>Students</th><th>Total Fees</th><th>Collected</th><th>Pending</th><th>Collection %</th><th>Details</th></tr></thead>
        <tbody>
            <?php $grandTotal = 0; $grandCollected = 0; $grandPending = 0;
            foreach ($classWise as $cw):
                $grandTotal += (float)$cw['total_amount'];
                $grandCollected += (float)$cw['total_paid'];
                $grandPending += (float)$cw['total_pending'];
                $pct = $cw['total_amount'] > 0 ? round(($cw['total_paid']/$cw['total_amount'])*100) : 0;
            ?>
            <tr>
                <td><strong><?= htmlspecialchars($cw['class_name'] ?? '-') ?></strong></td>
                <td><?= $cw['fee_count'] ?></td>
                <td><?= number_format($cw['total_amount'], 0) ?></td>
                <td style="color:#22c55e"><?= number_format($cw['total_paid'], 0) ?></td>
                <td style="color:#ef4444"><?= number_format($cw['total_pending'], 0) ?></td>
                <td>
                    <div style="background:rgba(99,102,241,.1);border-radius:4px;height:6px;width:80px;display:inline-block;vertical-align:middle">
                        <div style="background:<?= $pct >= 80 ? '#22c55e' : ($pct >= 50 ? '#f59e0b' : '#ef4444') ?>;height:100%;width:<?= $pct ?>%;border-radius:4px"></div>
                    </div>
                    <small style="margin-left:4px"><?= $pct ?>%</small>
                </td>
                <td><a href="<?= url("$slug/reports/class/{$cw['class_id']}") ?>" class="btn btn-sm btn-outline">View</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="font-weight:700;border-top:2px solid rgba(99,102,241,.2)">
                <td>TOTAL</td><td></td>
                <td><?= number_format($grandTotal, 0) ?></td>
                <td style="color:#22c55e"><?= number_format($grandCollected, 0) ?></td>
                <td style="color:#ef4444"><?= number_format($grandPending, 0) ?></td>
                <td><?= $grandTotal > 0 ? round(($grandCollected/$grandTotal)*100) : 0 ?>%</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>
