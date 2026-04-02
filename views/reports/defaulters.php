<?php $slug = tenant_slug(); ?>
<div class="page-header">
    <h1>⚠️ Defaulters Report</h1>
    <div class="actions">
        <a href="<?= url("$slug/reports/defaulters/csv") ?>" class="btn btn-sm btn-outline">⬇ CSV</a>
        <a href="<?= url("$slug/reports/defaulters/pdf") ?>" class="btn btn-sm btn-success">⬇ PDF</a>
    </div>
</div>
<div class="card" style="padding:12px 16px;margin-bottom:16px">
    <form method="GET" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
        <span style="color:#94a3b8;font-size:.85rem">Filter by Class:</span>
        <a href="<?= url("$slug/reports/defaulters") ?>" class="btn btn-sm <?= !$classId ? 'btn-primary' : 'btn-outline' ?>">All</a>
        <?php foreach ($classes as $c): ?>
            <a href="<?= url("$slug/reports/defaulters?class_id={$c['id']}") ?>" class="btn btn-sm <?= $classId == $c['id'] ? 'btn-primary' : 'btn-outline' ?>"><?= htmlspecialchars($c['name']) ?></a>
        <?php endforeach; ?>
    </form>
</div>
<div class="card">
    <?php if (empty($defaulters)): ?>
        <div class="empty">🎉 No defaulters!</div>
    <?php else: ?>
        <p style="color:#f87171;margin-bottom:12px"><strong><?= count($defaulters) ?></strong> students with pending fees</p>
        <table>
            <thead><tr><th>Student</th><th>Phone</th><th>Class</th><th>Fee</th><th>Amount</th><th>Paid</th><th>Balance</th><th>Due</th></tr></thead>
            <tbody>
                <?php foreach ($defaulters as $d): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($d['student_name'] ?? '-') ?></strong><br><small><?= htmlspecialchars($d['admission_no'] ?? '') ?></small></td>
                    <td><?= htmlspecialchars($d['phone'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($d['class_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($d['fee_type_name'] ?? '-') ?></td>
                    <td><?= number_format($d['amount'], 0) ?></td>
                    <td style="color:#22c55e"><?= number_format($d['paid_amount'], 0) ?></td>
                    <td style="color:#ef4444;font-weight:700"><?= number_format($d['amount'] - $d['paid_amount'], 0) ?></td>
                    <td><?= $d['due_date'] ? date('d M', strtotime($d['due_date'])) : '-' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
