<?php $slug = tenant_slug(); ?>
<div class="page-header">
    <h1>📄 Result Card — <?= htmlspecialchars($student['name']) ?></h1>
    <div class="actions">
        <a href="<?= url("$slug/results/{$exam['id']}/student/{$student['id']}/pdf") ?>" class="btn btn-success">⬇ Download PDF</a>
        <a href="<?= url("$slug/results") ?>" class="btn btn-outline">← Back</a>
    </div>
</div>

<div class="card" style="text-align:center;padding:30px">
    <h2 style="margin:0;color:#c7d2fe"><?= htmlspecialchars($school['name'] ?? '') ?></h2>
    <p style="color:#64748b;margin-top:4px"><?= htmlspecialchars($school['address'] ?? '') ?></p>
    <h3 style="margin-top:16px;color:#818cf8"><?= htmlspecialchars($exam['name']) ?> — Result Card</h3>
</div>

<div class="card">
    <div class="stat-grid">
        <div class="stat-card">
            <div class="label">Student Name</div>
            <div style="font-size:1.1rem;font-weight:700;color:#e2e8f0;margin-top:4px"><?= htmlspecialchars($student['name']) ?></div>
        </div>
        <div class="stat-card">
            <div class="label">Class</div>
            <div style="font-size:1.1rem;font-weight:700;color:#e2e8f0;margin-top:4px"><?= htmlspecialchars($student['class_name'] ?? '-') ?></div>
        </div>
        <div class="stat-card">
            <div class="label">Admission #</div>
            <div style="font-size:1.1rem;font-weight:700;color:#e2e8f0;margin-top:4px"><?= htmlspecialchars($student['admission_no'] ?? '-') ?></div>
        </div>
    </div>

    <table>
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
                <td><strong><?= htmlspecialchars($m['subject_name'] ?? '-') ?></strong></td>
                <td style="text-align:center"><?= $m['marks'] ?></td>
                <td style="text-align:center"><?= $m['max_marks'] ?></td>
                <td style="text-align:center"><span class="badge badge-active"><?= $m['grade'] ?></span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="border-top:2px solid rgba(99,102,241,.2)">
                <td></td>
                <td><strong>Total</strong></td>
                <td style="text-align:center"><strong><?= $result['total'] ?></strong></td>
                <td style="text-align:center"><strong><?= $result['max_total'] ?></strong></td>
                <td style="text-align:center"><span class="badge badge-active" style="font-size:.85rem"><?= $result['grade'] ?></span></td>
            </tr>
        </tfoot>
    </table>

    <div class="stat-grid" style="margin-top:20px">
        <div class="stat-card">
            <div class="number"><?= $result['percentage'] ?>%</div>
            <div class="label">Percentage</div>
        </div>
        <div class="stat-card">
            <div class="number"><?= $result['grade'] ?></div>
            <div class="label">Overall Grade</div>
        </div>
        <div class="stat-card">
            <div class="number"><?= $result['total'] ?>/<?= $result['max_total'] ?></div>
            <div class="label">Total Marks</div>
        </div>
    </div>
</div>
