<?php $slug = tenant_slug(); ?>
<div class="page-header">
    <h1>🗓️ Date Sheet — <?= htmlspecialchars($exam['name']) ?></h1>
    <div class="actions">
        <a href="<?= url("$slug/datesheets/{$exam['id']}/pdf" . ($classId ? "?class_id=$classId" : '')) ?>" class="btn btn-success">⬇ Download PDF</a>
        <a href="<?= url("$slug/datesheets") ?>" class="btn btn-outline">← Back</a>
    </div>
</div>

<!-- Class filter tabs -->
<div class="card" style="padding:12px 16px;margin-bottom:16px">
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
        <span style="color:#94a3b8;font-size:.85rem">Filter by Class:</span>
        <a href="<?= url("$slug/datesheets/{$exam['id']}/view") ?>" class="btn btn-sm <?= !$classId ? 'btn-primary' : 'btn-outline' ?>">All Classes</a>
        <?php foreach ($classes as $c): ?>
            <a href="<?= url("$slug/datesheets/{$exam['id']}/view?class_id={$c['id']}") ?>" class="btn btn-sm <?= $classId == $c['id'] ? 'btn-primary' : 'btn-outline' ?>"><?= htmlspecialchars($c['name']) ?></a>
        <?php endforeach; ?>
    </div>
</div>

<?php if (!empty($school)): ?>
<div class="card" style="text-align:center;padding:30px">
    <h2 style="margin:0;color:#c7d2fe"><?= htmlspecialchars($school['name']) ?></h2>
    <p style="color:#64748b;margin-top:4px"><?= htmlspecialchars($school['address'] ?? '') ?></p>
    <h3 style="margin-top:16px;color:#818cf8"><?= htmlspecialchars($exam['name']) ?> — Date Sheet
        <?php if ($classId): ?>
            <?php $selectedClass = null; foreach ($classes as $c) { if ($c['id'] == $classId) $selectedClass = $c; } ?>
            <?php if ($selectedClass): ?>(<?= htmlspecialchars($selectedClass['name']) ?>)<?php endif; ?>
        <?php endif; ?>
    </h3>
</div>
<?php endif; ?>

<div class="card">
    <?php if (empty($entries)): ?>
        <div class="empty">No entries found for this selection.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Subject</th>
                    <?php if (!$classId): ?><th>Class</th><?php endif; ?>
                    <th>Date</th>
                    <th>Day</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($entries as $i => $e): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><strong><?= htmlspecialchars($e['subject_name'] ?? '-') ?></strong></td>
                    <?php if (!$classId): ?><td><?= htmlspecialchars($e['class_name'] ?? '-') ?></td><?php endif; ?>
                    <td><?= date('d M Y', strtotime($e['exam_date'])) ?></td>
                    <td><?= date('l', strtotime($e['exam_date'])) ?></td>
                    <td><?= date('h:i A', strtotime($e['start_time'])) ?> — <?= date('h:i A', strtotime($e['end_time'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
