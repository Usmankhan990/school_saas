<div class="page-header">
    <h1><?= htmlspecialchars($title) ?></h1>
    <a href="<?= tenant_url('teachers/create') ?>" class="btn btn-primary">+ Add Teacher</a>
</div>
<div class="card">
    <?php if (empty($teachers)): ?>
        <div class="empty">No teachers added yet.</div>
    <?php else: ?>
        <table>
            <thead><tr><th>Name</th><th>Phone</th><th>Designation</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($teachers as $t): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($t['name']) ?></strong></td>
                    <td><?= htmlspecialchars($t['phone'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($t['designation'] ?? '—') ?></td>
                    <td><span class="badge badge-<?= $t['status'] ?? 'active' ?>"><?= $t['status'] ?? 'active' ?></span></td>
                    <td class="actions">
                        <a href="<?= tenant_url('teachers/' . $t['id'] . '/edit') ?>" class="btn btn-sm btn-outline">Edit</a>
                        <form method="POST" action="<?= tenant_url('teachers/' . $t['id'] . '/delete') ?>" style="display:inline" onsubmit="return confirm('Delete this teacher?')">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
