<?php $slug = tenant_slug(); ?>
<div class="page-header">
    <h1>💰 Fee Types</h1>
    <a href="<?= url("$slug/fees/types/create") ?>" class="btn btn-primary">+ Add Fee Type</a>
</div>
<div class="card">
    <?php if (empty($types)): ?>
        <div class="empty">No fee types yet. Add Tuition, Admission, Exam, Transport fees etc.</div>
    <?php else: ?>
    <table>
        <thead><tr><th>Name</th><th>Description</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($types as $t): ?>
            <tr>
                <td><strong><?= htmlspecialchars($t['name']) ?></strong></td>
                <td style="color:#94a3b8"><?= htmlspecialchars($t['description'] ?? '-') ?></td>
                <td class="actions">
                    <a href="<?= url("$slug/fees/types/{$t['id']}/edit") ?>" class="btn btn-sm btn-outline">Edit</a>
                    <form method="POST" action="<?= url("$slug/fees/types/{$t['id']}/delete") ?>" style="display:inline" onsubmit="return confirm('Delete?')">
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
