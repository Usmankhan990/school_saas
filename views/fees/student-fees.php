<?php $slug = tenant_slug(); ?>
<div class="page-header">
    <h1>🧾 Student Fees</h1>
    <div class="actions">
        <a href="<?= url("$slug/fees/student-fees") ?>" class="btn btn-sm <?= !$currentStatus ? 'btn-primary' : 'btn-outline' ?>">All</a>
        <a href="<?= url("$slug/fees/student-fees?status=pending") ?>" class="btn btn-sm <?= $currentStatus === 'pending' ? 'btn-primary' : 'btn-outline' ?>">⏳ Pending</a>
        <a href="<?= url("$slug/fees/student-fees?status=partial") ?>" class="btn btn-sm <?= $currentStatus === 'partial' ? 'btn-primary' : 'btn-outline' ?>">🔶 Partial</a>
        <a href="<?= url("$slug/fees/student-fees?status=paid") ?>" class="btn btn-sm <?= $currentStatus === 'paid' ? 'btn-primary' : 'btn-outline' ?>">✅ Paid</a>
    </div>
</div>
<div class="card">
    <?php if (empty($fees)): ?>
        <div class="empty">No student fees found. Assign fee structures to classes first.</div>
    <?php else: ?>
    <table>
        <thead>
            <tr><th>Student</th><th>Admission#</th><th>Class</th><th>Fee Type</th><th>Amount</th><th>Paid</th><th>Balance</th><th>Status</th><th>Due</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($fees as $f):
                $balance = (float)$f['amount'] - (float)$f['paid_amount'];
                $statusClass = ['pending'=>'warning','partial'=>'info','paid'=>'success'][$f['status']] ?? 'secondary';
            ?>
            <tr>
                <td><strong><?= htmlspecialchars($f['student_name'] ?? '-') ?></strong></td>
                <td><?= htmlspecialchars($f['admission_no'] ?? '-') ?></td>
                <td><?= htmlspecialchars($f['class_name'] ?? '-') ?></td>
                <td><?= htmlspecialchars($f['fee_type_name'] ?? '-') ?></td>
                <td><?= number_format($f['amount'], 2) ?></td>
                <td style="color:#22c55e"><?= number_format($f['paid_amount'], 2) ?></td>
                <td style="color:<?= $balance > 0 ? '#ef4444' : '#22c55e' ?>;font-weight:600"><?= number_format($balance, 2) ?></td>
                <td><span class="badge badge-<?= $statusClass ?>"><?= $f['status'] ?></span></td>
                <td><?= $f['due_date'] ? date('d M', strtotime($f['due_date'])) : '-' ?></td>
                <td>
                    <?php if ($f['status'] !== 'paid'): ?>
                        <a href="<?= url("$slug/payments/{$f['id']}/pay") ?>" class="btn btn-sm btn-success">💳 Pay</a>
                    <?php else: ?>
                        <span style="color:#22c55e">✅</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
