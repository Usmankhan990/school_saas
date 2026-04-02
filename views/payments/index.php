<?php $slug = tenant_slug(); ?>
<div class="page-header">
    <h1>💳 Payments</h1>
    <a href="<?= url("$slug/payments/history") ?>" class="btn btn-outline">📋 Full History</a>
</div>
<div class="card">
    <?php if (empty($payments)): ?>
        <div class="empty">No payments recorded yet.</div>
    <?php else: ?>
    <table>
        <thead><tr><th>Date</th><th>Student</th><th>Class</th><th>Fee</th><th>Amount</th><th>Method</th><th>TxnID</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($payments as $p): ?>
            <tr>
                <td><?= date('d M Y', strtotime($p['payment_date'])) ?></td>
                <td><strong><?= htmlspecialchars($p['student_name'] ?? '-') ?></strong><br><small style="color:#64748b"><?= htmlspecialchars($p['admission_no'] ?? '') ?></small></td>
                <td><?= htmlspecialchars($p['class_name'] ?? '-') ?></td>
                <td><?= htmlspecialchars($p['fee_type_name'] ?? '-') ?></td>
                <td style="font-weight:700;color:#22c55e"><?= number_format($p['amount'], 2) ?></td>
                <td><span class="badge"><?= strtoupper($p['payment_method']) ?></span></td>
                <td style="color:#94a3b8;font-size:.8rem"><?= htmlspecialchars($p['transaction_id'] ?? '-') ?></td>
                <td><a href="<?= url("$slug/payments/{$p['id']}/receipt") ?>" class="btn btn-sm btn-outline">🧾 Receipt</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
