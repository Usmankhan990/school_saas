<div class="page-header">
    <h1><?= htmlspecialchars($title) ?></h1>
    <a href="<?= tenant_url('staff') ?>" class="btn btn-outline">← Back</a>
</div>
<div class="card">
    <form method="POST" action="<?= tenant_url('staff/' . $member['id'] . '/update') ?>">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($member['name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($member['phone'] ?? '') ?>">
            </div>
        </div>
        <div class="form-group">
            <label>Designation</label>
            <input type="text" name="designation" value="<?= htmlspecialchars($member['designation'] ?? '') ?>">
        </div>
        <?php if ($member['user_id']): ?>
        <div class="form-group">
            <label>Email (linked account)</label>
            <input type="email" name="email" value="<?= htmlspecialchars($member['email'] ?? '') ?>">
        </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">Update Staff</button>
    </form>
</div>
