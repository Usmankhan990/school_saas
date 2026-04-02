<?php $slug = tenant_slug(); ?>
<div class="page-header">
    <h1>🧾 Payment Receipt</h1>
    <div class="actions">
        <a href="<?= url("$slug/payments/{$payment['id']}/receipt-pdf") ?>" class="btn btn-success">⬇ Download PDF</a>
        <a href="<?= url("$slug/payments") ?>" class="btn btn-outline">← Back</a>
    </div>
</div>
<div class="card" style="max-width:600px;margin:0 auto;padding:30px">
    <?php if (!empty($school)): ?>
    <div style="text-align:center;border-bottom:2px solid rgba(99,102,241,.3);padding-bottom:16px;margin-bottom:20px">
        <h2 style="margin:0;color:#c7d2fe"><?= htmlspecialchars($school['name']) ?></h2>
        <p style="color:#64748b;margin:4px 0;font-size:.85rem"><?= htmlspecialchars($school['address'] ?? '') ?></p>
        <p style="color:#64748b;margin:0;font-size:.85rem"><?= htmlspecialchars($school['phone'] ?? '') ?></p>
        <h3 style="margin-top:12px;color:#818cf8">PAYMENT RECEIPT</h3>
    </div>
    <?php endif; ?>
    <table style="width:100%;margin-bottom:20px">
        <tr><td style="color:#94a3b8;padding:6px 0">Receipt #</td><td style="text-align:right;font-weight:600"><?= str_pad($payment['id'], 6, '0', STR_PAD_LEFT) ?></td></tr>
        <tr><td style="color:#94a3b8;padding:6px 0">Date</td><td style="text-align:right"><?= date('d M Y', strtotime($payment['payment_date'])) ?></td></tr>
        <tr><td style="color:#94a3b8;padding:6px 0">Student</td><td style="text-align:right;font-weight:600"><?= htmlspecialchars($payment['student_name'] ?? '-') ?></td></tr>
        <tr><td style="color:#94a3b8;padding:6px 0">Admission#</td><td style="text-align:right"><?= htmlspecialchars($payment['admission_no'] ?? '-') ?></td></tr>
        <tr><td style="color:#94a3b8;padding:6px 0">Father</td><td style="text-align:right"><?= htmlspecialchars($payment['father_name'] ?? '-') ?></td></tr>
        <tr><td style="color:#94a3b8;padding:6px 0">Class</td><td style="text-align:right"><?= htmlspecialchars($payment['class_name'] ?? '-') ?> <?= htmlspecialchars($payment['section_name'] ?? '') ?></td></tr>
        <tr><td colspan="2"><div style="border-top:1px dashed rgba(99,102,241,.2);margin:8px 0"></div></td></tr>
        <tr><td style="color:#94a3b8;padding:6px 0">Fee Type</td><td style="text-align:right"><?= htmlspecialchars($payment['fee_type_name'] ?? '-') ?></td></tr>
        <tr><td style="color:#94a3b8;padding:6px 0">Fee Amount</td><td style="text-align:right"><?= number_format($payment['fee_amount'] ?? 0, 2) ?></td></tr>
        <tr><td style="color:#94a3b8;padding:6px 0">Total Paid</td><td style="text-align:right;color:#22c55e"><?= number_format($payment['paid_amount'] ?? 0, 2) ?></td></tr>
        <tr><td colspan="2"><div style="border-top:1px dashed rgba(99,102,241,.2);margin:8px 0"></div></td></tr>
        <tr><td style="color:#94a3b8;padding:6px 0">This Payment</td><td style="text-align:right;font-size:1.4rem;font-weight:700;color:#22c55e"><?= number_format($payment['amount'], 2) ?></td></tr>
        <tr><td style="color:#94a3b8;padding:6px 0">Method</td><td style="text-align:right"><span class="badge"><?= strtoupper($payment['payment_method']) ?></span></td></tr>
        <?php if ($payment['transaction_id']): ?>
        <tr><td style="color:#94a3b8;padding:6px 0">Transaction ID</td><td style="text-align:right;font-size:.85rem"><?= htmlspecialchars($payment['transaction_id']) ?></td></tr>
        <?php endif; ?>
    </table>
    <div style="text-align:center;color:#475569;font-size:.75rem;border-top:1px solid rgba(99,102,241,.1);padding-top:12px">
        This is a computer-generated receipt. Generated on <?= date('d M Y, h:i A') ?>
    </div>
</div>
