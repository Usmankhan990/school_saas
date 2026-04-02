<div class="page-header">
    <h1><?= htmlspecialchars($title) ?></h1>
    <a href="<?= tenant_url('students/create') ?>" class="btn btn-primary">+ Admit Student</a>
</div>
<div class="card">
    <?php if (empty($students)): ?>
        <div class="empty">No students enrolled yet.</div>
    <?php else: ?>
        <table>
            <thead><tr><th>Adm. No</th><th>Name</th><th>Father</th><th>Class</th><th>Section</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($students as $s): ?>
                <tr>
                    <td><code style="background:rgba(99,102,241,.1);padding:2px 8px;border-radius:4px;font-size:.8rem"><?= htmlspecialchars($s['admission_no']) ?></code></td>
                    <td><strong><?= htmlspecialchars($s['name']) ?></strong></td>
                    <td><?= htmlspecialchars($s['father_name'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($s['class_name'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($s['section_name'] ?? '—') ?></td>
                    <td><span class="badge badge-<?= $s['status'] ?? 'active' ?>"><?= $s['status'] ?? 'active' ?></span></td>
                    <td class="actions">
                        <a href="<?= tenant_url('students/' . $s['id'] . '/edit') ?>" class="btn btn-sm btn-outline">Edit</a>
                        <form method="POST" action="<?= tenant_url('students/' . $s['id'] . '/delete') ?>" style="display:inline" onsubmit="return confirm('Delete this student?')">
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
