<?php $slug = tenant_slug(); ?>
<div class="page-header">
    <h1>🗓️ Date Sheets</h1>
    <a href="<?= url("$slug/datesheets/create") ?>" class="btn btn-primary">+ Add Entry</a>
</div>

<?php if (!empty($exams) && !empty($classes)): ?>
<div class="card">
    <h3>View / Download Date Sheet by Exam & Class</h3>
    <p style="color:#94a3b8;margin-bottom:16px">Select an exam and then pick a class to view/download its date sheet.</p>
    <?php foreach ($exams as $ex): ?>
    <div style="border:1px solid rgba(99,102,241,.15);border-radius:12px;padding:16px;margin-bottom:12px">
        <h4 style="margin:0 0 12px;color:#c7d2fe">📝 <?= htmlspecialchars($ex['name']) ?></h4>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <a href="<?= url("$slug/datesheets/{$ex['id']}/view") ?>" class="btn btn-sm btn-outline">📋 All Classes</a>
            <?php foreach ($classes as $c): ?>
                <a href="<?= url("$slug/datesheets/{$ex['id']}/view?class_id={$c['id']}") ?>" class="btn btn-sm btn-outline"><?= htmlspecialchars($c['name']) ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="card" style="margin-top:20px">
    <h3>All Entries</h3>
    <?php if (empty($entries)): ?>
        <div class="empty">No date sheet entries yet. Create an exam first, then add entries.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Exam</th>
                    <th>Class</th>
                    <th>Subject</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($entries as $e): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($e['exam_name'] ?? '-') ?></strong></td>
                    <td><?= htmlspecialchars($e['class_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($e['subject_name'] ?? '-') ?></td>
                    <td><?= date('d M Y', strtotime($e['exam_date'])) ?></td>
                    <td><?= date('h:i A', strtotime($e['start_time'])) ?> — <?= date('h:i A', strtotime($e['end_time'])) ?></td>
                    <td class="actions">
                        <form method="POST" action="<?= url("$slug/datesheets/{$e['id']}/delete") ?>" style="display:inline" onsubmit="return confirm('Delete?')">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
