<div class="page-header">
    <h1><?= htmlspecialchars($title) ?></h1>
    <a href="<?= tenant_url('users') ?>" class="btn btn-outline">← Back</a>
</div>
<div class="card">
    <form method="POST" action="<?= tenant_url('users/' . $user['id'] . '/update') ?>">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>New Password (leave blank to keep)</label>
                <input type="password" name="password" placeholder="Min 6 characters">
            </div>
            <div class="form-group">
                <label>Role *</label>
                <select name="role" required>
                    <option value="admin" <?= $user['role']==='admin'?'selected':'' ?>>Admin</option>
                    <option value="staff" <?= $user['role']==='staff'?'selected':'' ?>>Staff</option>
                    <option value="teacher" <?= $user['role']==='teacher'?'selected':'' ?>>Teacher</option>
                    <option value="student" <?= $user['role']==='student'?'selected':'' ?>>Student</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Update User</button>
    </form>
</div>
<div class="card" style="margin-top:20px">
    <h3>Reset Password</h3>
    <form method="POST" action="<?= tenant_url('users/' . $user['id'] . '/reset-password') ?>">
        <?= csrf_field() ?>
        <div class="form-group">
            <label>New Password *</label>
            <input type="password" name="new_password" placeholder="Min 6 characters" required>
        </div>
        <button type="submit" class="btn btn-danger">Reset Password</button>
    </form>
</div>
