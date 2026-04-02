<?php $slug = tenant_slug(); ?>
<div class="page-header">
    <h1>📝 Exams</h1>
    <a href="<?= url("$slug/exams/create") ?>" class="btn btn-primary">+ Create Exam</a>
</div>

<div class="card">
    <?php if (empty($exams)): ?>
        <div class="empty">No exams created yet.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Exam Name</th>
                    <th>Type</th>
                    <th>Class</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($exams as $i => $e): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><strong><?= htmlspecialchars($e['name']) ?></strong></td>
                    <td><span class="badge badge-active"><?= htmlspecialchars($e['type'] ?? 'term') ?></span></td>
                    <td><?= htmlspecialchars($e['class_name'] ?? 'All') ?></td>
                    <td><?= $e['start_date'] ? date('d M Y', strtotime($e['start_date'])) : '-' ?></td>
                    <td><?= $e['end_date'] ? date('d M Y', strtotime($e['end_date'])) : '-' ?></td>
                    <td class="actions">
                        <a href="<?= url("$slug/exams/{$e['id']}/edit") ?>" class="btn btn-sm btn-outline">Edit</a>
                        <a href="<?= url("$slug/results/{$e['id']}/enter-marks") ?>" class="btn btn-sm btn-success">Marks</a>
                        <a href="<?= url("$slug/datesheets/{$e['id']}/view") ?>" class="btn btn-sm btn-outline">Date Sheet</a>
                        <form method="POST" action="<?= url("$slug/exams/{$e['id']}/delete") ?>" style="display:inline" onsubmit="return confirm('Delete this exam?')">
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
