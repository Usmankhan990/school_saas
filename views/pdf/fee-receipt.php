<!DOCTYPE html>
<html><head><meta charset="UTF-8">
<style>
    body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th { background: #f0f0f0; padding: 8px; text-align: left; font-size: 11px; border: 1px solid #ccc; }
    td { padding: 8px; font-size: 11px; border: 1px solid #ccc; }
    .receipt-no { font-size: 12px; color: #666; }
    .amount { font-size: 20px; font-weight: bold; color: #1a1a2e; text-align: center; margin: 15px 0; }
    .footer { text-align: center; margin-top: 30px; font-size: 9px; color: #999; border-top: 1px solid #ccc; padding-top: 10px; }
</style></head>
<body>
    <?= PdfHelper::schoolHeader($school) ?>
    <h2 style="text-align:center;margin-bottom:5px">PAYMENT RECEIPT</h2>
    <p class="receipt-no" style="text-align:center">Receipt # <?= str_pad($payment['id'], 6, '0', STR_PAD_LEFT) ?></p>

    <table>
        <tr><td style="width:35%;font-weight:bold">Student Name</td><td><?= htmlspecialchars($payment['student_name'] ?? '-') ?></td></tr>
        <tr><td style="font-weight:bold">Admission #</td><td><?= htmlspecialchars($payment['admission_no'] ?? '-') ?></td></tr>
        <tr><td style="font-weight:bold">Father Name</td><td><?= htmlspecialchars($payment['father_name'] ?? '-') ?></td></tr>
        <tr><td style="font-weight:bold">Class / Section</td><td><?= htmlspecialchars($payment['class_name'] ?? '-') ?> <?= htmlspecialchars($payment['section_name'] ?? '') ?></td></tr>
        <tr><td style="font-weight:bold">Fee Type</td><td><?= htmlspecialchars($payment['fee_type_name'] ?? '-') ?></td></tr>
        <tr><td style="font-weight:bold">Total Fee</td><td><?= number_format($payment['fee_amount'] ?? 0, 2) ?></td></tr>
        <tr><td style="font-weight:bold">Amount Paid</td><td style="font-weight:bold;font-size:14px;color:#006400"><?= number_format($payment['amount'], 2) ?></td></tr>
        <tr><td style="font-weight:bold">Payment Method</td><td><?= strtoupper($payment['payment_method']) ?></td></tr>
        <?php if ($payment['transaction_id']): ?>
        <tr><td style="font-weight:bold">Transaction ID</td><td><?= htmlspecialchars($payment['transaction_id']) ?></td></tr>
        <?php endif; ?>
        <tr><td style="font-weight:bold">Date</td><td><?= date('d M Y', strtotime($payment['payment_date'])) ?></td></tr>
    </table>

    <table style="width:100%;border:none;margin-top:50px">
        <tr>
            <td style="border:none;text-align:center;padding-top:25px;border-top:1px solid #333;width:40%"><small>Received By</small></td>
            <td style="border:none;width:20%"></td>
            <td style="border:none;text-align:center;padding-top:25px;border-top:1px solid #333;width:40%"><small>Principal</small></td>
        </tr>
    </table>

    <?= PdfHelper::footer() ?>
</body></html>
