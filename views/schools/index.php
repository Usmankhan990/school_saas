<div class="page-header">
    <h1><?= htmlspecialchars($title) ?></h1>
    <a href="<?= url('schools/create') ?>" class="btn btn-primary">+ Add School</a>
</div>
<div class="card">
    <?php if (empty($schools)): ?>
        <div class="empty">No schools registered yet.</div>
    <?php else: ?>
        <table>
            <thead><tr><th>Name</th><th>Slug</th><th>Principal</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($schools as $s): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($s['name']) ?></strong></td>
                    <td><code style="background:rgba(99,102,241,.1);padding:2px 8px;border-radius:4px;font-size:.8rem"><?= htmlspecialchars($s['slug']) ?></code></td>
                    <td><?= htmlspecialchars($s['principal_name'] ?? '—') ?></td>
                    <td><span class="badge badge-<?= $s['status'] ?>"><?= $s['status'] ?></span></td>
                    <td><?= format_date($s['created_at']) ?></td>
                    <td class="actions">
                        <a href="<?= url('schools/' . $s['id'] . '/edit') ?>" class="btn btn-sm btn-outline">Edit</a>
                        <form method="POST" action="<?= url('schools/' . $s['id'] . '/toggle') ?>" style="display:inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm <?= $s['status']==='active'?'btn-danger':'btn-success' ?>">
                                <?= $s['status']==='active'?'Disable':'Enable' ?>
                            </button>
                        </form>
                        <a href="<?= url($s['slug'] . '/login') ?>" class="btn btn-sm btn-outline" target="_blank">🔗 Portal</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
