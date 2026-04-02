<!DOCTYPE html>
<html><head><meta charset="UTF-8">
<style>
    body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th { background: #f0f0f0; padding: 8px; text-align: left; font-size: 11px; border: 1px solid #ccc; }
    td { padding: 8px; font-size: 11px; border: 1px solid #ccc; }
    .footer { text-align: center; margin-top: 30px; font-size: 9px; color: #999; border-top: 1px solid #ccc; padding-top: 10px; }
    h2 { text-align: center; margin-bottom: 15px; }
    .red { color: #d00; font-weight: bold; }
</style></head>
<body>
    <?= PdfHelper::schoolHeader($school) ?>
    <h2>DEFAULTERS REPORT</h2>
    <p style="text-align:center;font-size:11px;color:#666">Generated on <?= date('d M Y') ?> · <?= count($defaulters) ?> students</p>

    <table>
        <thead>
            <tr><th>#</th><th>Student</th><th>Admission#</th><th>Phone</th><th>Fee Type</th><th>Amount</th><th>Paid</th><th>Balance</th><th>Due Date</th></tr>
        </thead>
        <tbody>
            <?php foreach ($defaulters as $i => $d): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($d['student_name'] ?? '-') ?></td>
                <td><?= htmlspecialchars($d['admission_no'] ?? '-') ?></td>
                <td><?= htmlspecialchars($d['phone'] ?? '-') ?></td>
                <td><?= htmlspecialchars($d['fee_type_name'] ?? '-') ?></td>
                <td><?= number_format($d['amount'], 2) ?></td>
                <td><?= number_format($d['paid_amount'], 2) ?></td>
                <td class="red"><?= number_format($d['amount'] - $d['paid_amount'], 2) ?></td>
                <td><?= $d['due_date'] ? date('d M Y', strtotime($d['due_date'])) : '-' ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?= PdfHelper::footer() ?>
</body></html>
