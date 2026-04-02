<div class="page-header">
    <h1><?= htmlspecialchars($title) ?></h1>
    <a href="<?= tenant_url('staff') ?>" class="btn btn-outline">← Back</a>
</div>
<div class="card">
    <form method="POST" action="<?= tenant_url('staff/store') ?>">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" value="<?= old('name') ?>" required>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="<?= old('phone') ?>">
            </div>
        </div>
        <div class="form-group">
            <label>Designation</label>
            <input type="text" name="designation" value="<?= old('designation') ?>" placeholder="e.g. Accountant, Clerk">
        </div>
        <hr style="border-color:rgba(99,102,241,.15);margin:24px 0">
        <h3 style="color:#c7d2fe;margin-bottom:16px;font-size:.95rem">Login Account (Optional)</h3>
        <div class="form-row">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= old('email') ?>">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Min 6 characters">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Add Staff</button>
    </form>
</div>
