<div class="page-header">
    <h1><?= htmlspecialchars($title) ?></h1>
    <a href="<?= tenant_url('classes/create') ?>" class="btn btn-primary">+ Add Class</a>
</div>
<div class="card">
    <?php if (empty($classes)): ?>
        <div class="empty">No classes created yet.</div>
    <?php else: ?>
        <table>
            <thead><tr><th>Class Name</th><th>Numeric</th><th>Sections</th><th>Created</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($classes as $c): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($c['name']) ?></strong></td>
                    <td><?= htmlspecialchars($c['numeric_name'] ?? '—') ?></td>
                    <td>
                        <?php
                        $classSections = array_filter($sections, function($s) use ($c) { return $s['class_id'] == $c['id']; });
                        if (empty($classSections)):
                        ?>
                            <span style="color:#475569">None</span>
                        <?php else: ?>
                            <?php foreach ($classSections as $sec): ?>
                                <span class="badge" style="background:rgba(99,102,241,.12);color:#a5b4fc;margin:2px"><?= htmlspecialchars($sec['name']) ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </td>
                    <td><?= format_date($c['created_at'] ?? null) ?></td>
                    <td class="actions">
                        <a href="<?= tenant_url('classes/' . $c['id'] . '/edit') ?>" class="btn btn-sm btn-outline">Edit</a>
                        <form method="POST" action="<?= tenant_url('classes/' . $c['id'] . '/delete') ?>" style="display:inline" onsubmit="return confirm('Delete this class and its sections?')">
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
