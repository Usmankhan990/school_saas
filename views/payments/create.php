<?php $slug = tenant_slug(); ?>
<div class="page-header"><h1>💳 Record Payment</h1><a href="<?= url("$slug/fees/student-fees") ?>" class="btn btn-outline">← Back</a></div>
<div class="card">
    <div style="background:rgba(99,102,241,.08);border-radius:12px;padding:16px;margin-bottom:20px">
        <h3 style="margin:0;color:#c7d2fe"><?= htmlspecialchars($student['name'] ?? '') ?></h3>
        <p style="color:#94a3b8;margin:4px 0 0">
            Admission: <?= htmlspecialchars($student['admission_no'] ?? '-') ?> · 
            Fee: <?= htmlspecialchars($feeType['name'] ?? '-') ?> · 
            Total: <strong><?= number_format($sf['amount'], 2) ?></strong> · 
            Paid: <strong style="color:#22c55e"><?= number_format($sf['paid_amount'], 2) ?></strong> · 
            Balance: <strong style="color:#ef4444"><?= number_format($balance, 2) ?></strong>
        </p>
    </div>
    <form method="POST" action="<?= url("$slug/payments/store") ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="student_fee_id" value="<?= $sf['id'] ?>">
        <div class="form-group"><label>Amount to Pay (PKR) *</label><input type="number" name="amount" class="form-control" step="0.01" min="1" max="<?= $balance ?>" value="<?= $balance ?>" required></div>
        <div class="form-group"><label>Payment Method *</label>
            <select name="payment_method" class="form-control" required>
                <option value="cash">💵 Cash</option>
                <option value="bank">🏦 Bank Transfer</option>
                <option value="jazzcash">📱 JazzCash</option>
                <option value="easypaisa">📱 EasyPaisa</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div class="form-group"><label>Transaction ID</label><input type="text" name="transaction_id" class="form-control" placeholder="Bank ref / mobile TxnID"></div>
        <div class="form-group"><label>Payment Date</label><input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>"></div>
        <div class="form-group"><label>Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
        <button class="btn btn-primary" style="width:100%;padding:14px;font-size:1.1rem">💳 Record Payment — <?= number_format($balance, 2) ?> PKR</button>
    </form>
</div>
