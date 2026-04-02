<div class="page-header">
    <h1><?= htmlspecialchars($title) ?></h1>
    <a href="<?= tenant_url('teachers') ?>" class="btn btn-outline">← Back</a>
</div>
<div class="card">
    <form method="POST" action="<?= tenant_url('teachers/' . $teacher['id'] . '/update') ?>">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($teacher['name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($teacher['phone'] ?? '') ?>">
            </div>
        </div>
        <div class="form-group">
            <label>Designation</label>
            <input type="text" name="designation" value="<?= htmlspecialchars($teacher['designation'] ?? '') ?>">
        </div>
        <?php if ($teacher['user_id']): ?>
        <div class="form-group">
            <label>Email (linked account)</label>
            <input type="email" name="email" value="<?= htmlspecialchars($teacher['email'] ?? '') ?>">
        </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">Update Teacher</button>
    </form>
</div>
