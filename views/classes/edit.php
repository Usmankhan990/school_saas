<div class="page-header">
    <h1><?= htmlspecialchars($title) ?></h1>
    <a href="<?= tenant_url('classes') ?>" class="btn btn-outline">← Back</a>
</div>
<div class="card">
    <form method="POST" action="<?= tenant_url('classes/' . $class['id'] . '/update') ?>">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group">
                <label>Class Name *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($class['name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Numeric Name</label>
                <input type="text" name="numeric_name" value="<?= htmlspecialchars($class['numeric_name'] ?? '') ?>">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Update Class</button>
    </form>
</div>
<?php if (!empty($sections)): ?>
<div class="card" style="margin-top:20px">
    <h3>Sections</h3>
    <table>
        <thead><tr><th>Section Name</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($sections as $sec): ?>
            <tr>
                <td><?= htmlspecialchars($sec['name']) ?></td>
                <td>
                    <form method="POST" action="<?= tenant_url('sections/' . $sec['id'] . '/delete') ?>" style="display:inline" onsubmit="return confirm('Delete this section?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
