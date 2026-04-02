<?php $slug = tenant_slug(); ?>
<div class="page-header">
    <h1>📋 Payment History</h1>
    <a href="<?= url("$slug/payments") ?>" class="btn btn-outline">← Back</a>
</div>
<div class="card" style="padding:12px 16px;margin-bottom:16px">
    <form method="GET" action="<?= url("$slug/payments/history") ?>" style="display:flex;gap:12px;flex-wrap:wrap;align-items:end">
        <div><label style="font-size:.8rem;color:#94a3b8">From</label><input type="date" name="from" class="form-control" value="<?= htmlspecialchars($dateFrom) ?>"></div>
        <div><label style="font-size:.8rem;color:#94a3b8">To</label><input type="date" name="to" class="form-control" value="<?= htmlspecialchars($dateTo) ?>"></div>
        <div><label style="font-size:.8rem;color:#94a3b8">Method</label>
            <select name="method" class="form-control">
                <option value="">All</option>
                <option value="cash" <?= $method === 'cash' ? 'selected' : '' ?>>Cash</option>
                <option value="bank" <?= $method === 'bank' ? 'selected' : '' ?>>Bank</option>
                <option value="jazzcash" <?= $method === 'jazzcash' ? 'selected' : '' ?>>JazzCash</option>
                <option value="easypaisa" <?= $method === 'easypaisa' ? 'selected' : '' ?>>EasyPaisa</option>
            </select>
        </div>
        <button class="btn btn-primary">🔍 Filter</button>
    </form>
</div>
<?php if ($totalFiltered > 0): ?>
<div class="stat-card" style="background:linear-gradient(135deg,#059669,#10b981);margin-bottom:16px;padding:16px 20px;border-radius:12px;color:#fff">
    <span style="font-size:.85rem;opacity:.8">Filtered Total</span>
    <h2 style="margin:4px 0 0;font-size:1.8rem"><?= number_format($totalFiltered, 2) ?> PKR</h2>
</div>
<?php endif; ?>
<div class="card">
    <?php if (empty($payments)): ?>
        <div class="empty">No payments found for selected filters.</div>
    <?php else: ?>
    <table>
        <thead><tr><th>Date</th><th>Student</th><th>Admission#</th><th>Class</th><th>Fee</th><th>Amount</th><th>Method</th><th>TxnID</th></tr></thead>
        <tbody>
            <?php foreach ($payments as $p): ?>
            <tr>
                <td><?= date('d M Y', strtotime($p['payment_date'])) ?></td>
                <td><strong><?= htmlspecialchars($p['student_name'] ?? '-') ?></strong></td>
                <td><?= htmlspecialchars($p['admission_no'] ?? '-') ?></td>
                <td><?= htmlspecialchars($p['class_name'] ?? '-') ?></td>
                <td><?= htmlspecialchars($p['fee_type_name'] ?? '-') ?></td>
                <td style="color:#22c55e;font-weight:700"><?= number_format($p['amount'], 2) ?></td>
                <td><span class="badge"><?= strtoupper($p['payment_method']) ?></span></td>
                <td style="color:#94a3b8;font-size:.8rem"><?= htmlspecialchars($p['transaction_id'] ?? '-') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
