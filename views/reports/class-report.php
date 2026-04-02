<?php $slug = tenant_slug(); ?>
<div class="page-header"><h1>📊 <?= htmlspecialchars($class['name'] ?? '') ?> — Fee Report</h1><a href="<?= url("$slug/reports/fees") ?>" class="btn btn-outline">← Back</a></div>
<div class="card">
    <?php if (empty($fees)): ?>
        <div class="empty">No fee records for this class.</div>
    <?php else: ?>
    <table>
        <thead><tr><th>Student</th><th>Admission#</th><th>Fee Type</th><th>Amount</th><th>Paid</th><th>Balance</th><th>Status</th><th>Due</th><th>Action</th></tr></thead>
        <tbody>
            <?php foreach ($fees as $f): $bal = $f['amount'] - $f['paid_amount']; ?>
            <tr>
                <td><strong><?= htmlspecialchars($f['student_name'] ?? '-') ?></strong></td>
                <td><?= htmlspecialchars($f['admission_no'] ?? '-') ?></td>
                <td><?= htmlspecialchars($f['fee_type_name'] ?? '-') ?></td>
                <td><?= number_format($f['amount'], 2) ?></td>
                <td style="color:#22c55e"><?= number_format($f['paid_amount'], 2) ?></td>
                <td style="color:<?= $bal > 0 ? '#ef4444' : '#22c55e' ?>;font-weight:600"><?= number_format($bal, 2) ?></td>
                <td><span class="badge badge-<?= ['pending'=>'warning','partial'=>'info','paid'=>'success'][$f['status']] ?? '' ?>"><?= $f['status'] ?></span></td>
                <td><?= $f['due_date'] ? date('d M', strtotime($f['due_date'])) : '-' ?></td>
                <td><?php if ($f['status'] !== 'paid'): ?><a href="<?= url("$slug/payments/{$f['id']}/pay") ?>" class="btn btn-sm btn-success">Pay</a><?php endif; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
