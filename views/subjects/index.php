<?php $slug = tenant_slug(); ?>
<div class="page-header">
    <h1>📚 Subjects</h1>
    <a href="<?= url("$slug/subjects/create") ?>" class="btn btn-primary">+ Add Subject</a>
</div>

<div class="card">
    <?php if (empty($subjects)): ?>
        <div class="empty">No subjects found. Add your first subject.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subjects as $i => $s): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><strong><?= htmlspecialchars($s['name']) ?></strong></td>
                    <td><?= htmlspecialchars($s['code'] ?? '-') ?></td>
                    <td class="actions">
                        <a href="<?= url("$slug/subjects/{$s['id']}/edit") ?>" class="btn btn-sm btn-outline">Edit</a>
                        <form method="POST" action="<?= url("$slug/subjects/{$s['id']}/delete") ?>" style="display:inline" onsubmit="return confirm('Delete this subject?')">
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
