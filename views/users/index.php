<div class="page-header">
    <h1><?= htmlspecialchars($title) ?></h1>
    <a href="<?= tenant_url('users/create') ?>" class="btn btn-primary">+ Create User</a>
</div>
<div class="card">
    <?php if (empty($users)): ?>
        <div class="empty">No users found.</div>
    <?php else: ?>
        <table>
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Last Login</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($u['name']) ?></strong></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><span class="badge" style="background:rgba(99,102,241,.15);color:#a5b4fc"><?= htmlspecialchars($u['role']) ?></span></td>
                    <td><span class="badge badge-<?= $u['status'] ?>"><?= $u['status'] ?></span></td>
                    <td><?= format_date($u['last_login_at'] ?? null, 'd M Y H:i') ?></td>
                    <td class="actions">
                        <a href="<?= tenant_url('users/' . $u['id'] . '/edit') ?>" class="btn btn-sm btn-outline">Edit</a>
                        <form method="POST" action="<?= tenant_url('users/' . $u['id'] . '/toggle') ?>" style="display:inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm <?= $u['status']==='active'?'btn-danger':'btn-success' ?>">
                                <?= $u['status']==='active'?'Disable':'Enable' ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
