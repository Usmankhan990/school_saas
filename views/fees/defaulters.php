<?php $slug = tenant_slug(); ?>
<div class="page-header">
    <h1>⚠️ Defaulters</h1>
    <div class="actions">
        <a href="<?= url("$slug/reports/defaulters/csv") ?>" class="btn btn-sm btn-outline">⬇ CSV</a>
        <a href="<?= url("$slug/reports/defaulters/pdf") ?>" class="btn btn-sm btn-outline">⬇ PDF</a>
    </div>
</div>
<div class="card" style="padding:12px 16px;margin-bottom:16px">
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
        <span style="color:#94a3b8;font-size:.85rem">Filter:</span>
        <a href="<?= url("$slug/fees/defaulters") ?>" class="btn btn-sm <?= !$classId ? 'btn-primary' : 'btn-outline' ?>">All Classes</a>
        <?php foreach ($classes as $c): ?>
            <a href="<?= url("$slug/fees/defaulters?class_id={$c['id']}") ?>" class="btn btn-sm <?= $classId == $c['id'] ? 'btn-primary' : 'btn-outline' ?>"><?= htmlspecialchars($c['name']) ?></a>
        <?php endforeach; ?>
    </div>
</div>
<div class="card">
    <?php if (empty($defaulters)): ?>
        <div class="empty">🎉 No defaulters! All fees are paid.</div>
    <?php else: ?>
    <table>
        <thead><tr><th>Student</th><th>Admission#</th><th>Phone</th><th>Class</th><th>Fee</th><th>Amount</th><th>Paid</th><th>Balance</th><th>Due</th><th>Action</th></tr></thead>
        <tbody>
            <?php foreach ($defaulters as $d): $bal = $d['amount'] - $d['paid_amount']; ?>
            <tr>
                <td><strong><?= htmlspecialchars($d['student_name'] ?? '-') ?></strong></td>
                <td><?= htmlspecialchars($d['admission_no'] ?? '-') ?></td>
                <td><?= htmlspecialchars($d['phone'] ?? '-') ?></td>
                <td><?= htmlspecialchars($d['class_name'] ?? '-') ?></td>
                <td><?= htmlspecialchars($d['fee_type_name'] ?? '-') ?></td>
                <td><?= number_format($d['amount'], 2) ?></td>
                <td style="color:#22c55e"><?= number_format($d['paid_amount'], 2) ?></td>
                <td style="color:#ef4444;font-weight:700"><?= number_format($bal, 2) ?></td>
                <td><?= $d['due_date'] ? date('d M', strtotime($d['due_date'])) : '-' ?></td>
                <td><a href="<?= url("$slug/payments/{$d['id']}/pay") ?>" class="btn btn-sm btn-success">💳 Pay</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
