<?php $slug = tenant_slug(); ?>
<div class="page-header">
    <h1>📋 Fee Structures</h1>
    <a href="<?= url("$slug/fees/structures/create") ?>" class="btn btn-primary">+ Create Structure</a>
</div>
<div class="card">
    <?php if (empty($structures)): ?>
        <div class="empty">No fee structures yet. Create one to assign fees to classes.</div>
    <?php else: ?>
    <table>
        <thead>
            <tr><th>Fee Type</th><th>Class</th><th>Amount</th><th>Due Date</th><th>Year</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($structures as $s): ?>
            <tr>
                <td><strong><?= htmlspecialchars($s['fee_type_name'] ?? '-') ?></strong></td>
                <td><?= htmlspecialchars($s['class_name'] ?? 'All') ?></td>
                <td style="font-weight:600;color:#22c55e"><?= number_format($s['amount'], 2) ?></td>
                <td><?= $s['due_date'] ? date('d M Y', strtotime($s['due_date'])) : '-' ?></td>
                <td><?= htmlspecialchars($s['academic_year'] ?? '-') ?></td>
                <td><span class="badge badge-<?= $s['status'] === 'active' ? 'success' : 'secondary' ?>"><?= $s['status'] ?></span></td>
                <td class="actions">
                    <form method="POST" action="<?= url("$slug/fees/structures/{$s['id']}/assign") ?>" style="display:inline" onsubmit="return confirm('Assign to all students in this class?')">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm btn-success" title="Assign to all students">⚡ Assign</button>
                    </form>
                    <form method="POST" action="<?= url("$slug/fees/structures/{$s['id']}/delete") ?>" style="display:inline" onsubmit="return confirm('Delete?')">
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
