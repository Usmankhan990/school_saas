<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 25px; }
        .header h1 { margin: 0; font-size: 22px; color: #1a1a2e; }
        .header p { margin: 4px 0 0; font-size: 12px; color: #555; }
        .exam-title { text-align: center; font-size: 18px; color: #333; margin-bottom: 20px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f0f0f0; padding: 10px; text-align: left; font-size: 12px; font-weight: bold; border: 1px solid #ccc; }
        td { padding: 10px; font-size: 12px; border: 1px solid #ccc; }
        .footer { text-align: center; margin-top: 30px; padding-top: 10px; border-top: 1px solid #ccc; font-size: 10px; color: #888; }
    </style>
</head>
<body>
    <?= PdfHelper::schoolHeader($school) ?>

    <div class="exam-title"><?= htmlspecialchars($exam['name']) ?> — Date Sheet<?= htmlspecialchars($className ?? '') ?></div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Subject</th>
                <th>Date</th>
                <th>Day</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($entries as $i => $e): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($e['subject_name'] ?? '-') ?></td>
                <td><?= date('d M Y', strtotime($e['exam_date'])) ?></td>
                <td><?= date('l', strtotime($e['exam_date'])) ?></td>
                <td><?= date('h:i A', strtotime($e['start_time'])) ?> — <?= date('h:i A', strtotime($e['end_time'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?= PdfHelper::footer() ?>
</body>
</html>
