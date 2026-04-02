<div class="page-header">
    <h1><?= htmlspecialchars($title) ?></h1>
    <a href="<?= tenant_url('staff/create') ?>" class="btn btn-primary">+ Add Staff</a>
</div>
<div class="card">
    <?php if (empty($staff)): ?>
        <div class="empty">No staff members added yet.</div>
    <?php else: ?>
        <table>
            <thead><tr><th>Name</th><th>Phone</th><th>Designation</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($staff as $m): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($m['name']) ?></strong></td>
                    <td><?= htmlspecialchars($m['phone'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($m['designation'] ?? '—') ?></td>
                    <td><span class="badge badge-<?= $m['status'] ?? 'active' ?>"><?= $m['status'] ?? 'active' ?></span></td>
                    <td class="actions">
                        <a href="<?= tenant_url('staff/' . $m['id'] . '/edit') ?>" class="btn btn-sm btn-outline">Edit</a>
                        <form method="POST" action="<?= tenant_url('staff/' . $m['id'] . '/delete') ?>" style="display:inline" onsubmit="return confirm('Delete this staff member?')">
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
