<div class="page-header">
    <h1><?= htmlspecialchars($title) ?></h1>
    <a href="<?= tenant_url('users') ?>" class="btn btn-outline">← Back</a>
</div>
<div class="card">
    <form method="POST" action="<?= tenant_url('users/store') ?>">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" value="<?= old('name') ?>" required>
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" value="<?= old('email') ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Password *</label>
                <input type="password" name="password" placeholder="Min 6 characters" required>
            </div>
            <div class="form-group">
                <label>Role *</label>
                <select name="role" required>
                    <option value="">— Select Role —</option>
                    <option value="admin" <?= old('role')==='admin'?'selected':'' ?>>Admin</option>
                    <option value="staff" <?= old('role')==='staff'?'selected':'' ?>>Staff</option>
                    <option value="teacher" <?= old('role')==='teacher'?'selected':'' ?>>Teacher</option>
                    <option value="student" <?= old('role')==='student'?'selected':'' ?>>Student</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Create User</button>
    </form>
</div>
