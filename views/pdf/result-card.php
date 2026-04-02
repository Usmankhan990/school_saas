<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 25px; }
        .header h1 { margin: 0; font-size: 22px; color: #1a1a2e; }
        .header p { margin: 4px 0 0; font-size: 12px; color: #555; }
        .title { text-align: center; font-size: 18px; color: #333; margin-bottom: 5px; font-weight: bold; }
        .student-info { margin-bottom: 20px; }
        .student-info table { width: 100%; border: none; }
        .student-info td { padding: 4px 8px; font-size: 13px; border: none; }
        .student-info .label { font-weight: bold; color: #555; width: 120px; }
        table.marks { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.marks th { background: #f0f0f0; padding: 10px; text-align: left; font-size: 12px; font-weight: bold; border: 1px solid #ccc; }
        table.marks td { padding: 10px; font-size: 12px; border: 1px solid #ccc; }
        table.marks tfoot td { background: #f8f8f8; font-weight: bold; }
        .summary { margin-top: 20px; text-align: center; }
        .summary .grade { font-size: 36px; font-weight: bold; color: #1a1a2e; }
        .summary .pct { font-size: 18px; color: #555; margin-top: 5px; }
        .footer { text-align: center; margin-top: 40px; padding-top: 10px; border-top: 1px solid #ccc; font-size: 10px; color: #888; }
        .signatures { display: flex; justify-content: space-between; margin-top: 50px; }
        .sig-line { width: 150px; border-top: 1px solid #333; text-align: center; font-size: 11px; padding-top: 5px; }
    </style>
</head>
<body>
    <?= PdfHelper::schoolHeader($school) ?>

    <div class="title"><?= htmlspecialchars($exam['name']) ?> — Result Card</div>

    <div class="student-info">
        <table>
            <tr>
                <td class="label">Student Name:</td>
                <td><?= htmlspecialchars($student['name']) ?></td>
                <td class="label">Admission #:</td>
                <td><?= htmlspecialchars($student['admission_no'] ?? '-') ?></td>
            </tr>
            <tr>
                <td class="label">Class:</td>
                <td><?= htmlspecialchars($student['class_name'] ?? '-') ?></td>
                <td class="label">Section:</td>
                <td><?= htmlspecialchars($student['section_name'] ?? '-') ?></td>
            </tr>
        </table>
    </div>

    <table class="marks">
        <thead>
            <tr>
                <th>#</th>
                <th>Subject</th>
                <th style="text-align:center">Marks Obtained</th>
                <th style="text-align:center">Total Marks</th>
                <th style="text-align:center">Grade</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($result['marks'] as $i => $m): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($m['subject_name'] ?? '-') ?></td>
                <td style="text-align:center"><?= $m['marks'] ?></td>
                <td style="text-align:center"><?= $m['max_marks'] ?></td>
                <td style="text-align:center"><?= $m['grade'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td></td>
                <td>Grand Total</td>
                <td style="text-align:center"><?= $result['total'] ?></td>
                <td style="text-align:center"><?= $result['max_total'] ?></td>
                <td style="text-align:center"><?= $result['grade'] ?></td>
            </tr>
        </tfoot>
    </table>

    <div class="summary">
        <div class="grade"><?= $result['grade'] ?></div>
        <div class="pct">Percentage: <?= $result['percentage'] ?>%</div>
    </div>

    <table style="width:100%;border:none;margin-top:60px">
        <tr>
            <td style="border:none;text-align:center;padding-top:30px;border-top:1px solid #333;width:33%">
                <small>Class Teacher</small>
            </td>
            <td style="border:none;width:34%"></td>
            <td style="border:none;text-align:center;padding-top:30px;border-top:1px solid #333;width:33%">
                <small>Principal</small>
            </td>
        </tr>
    </table>

    <?= PdfHelper::footer() ?>
</body>
</html>
