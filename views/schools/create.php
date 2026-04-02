<div class="page-header">
    <h1><?= htmlspecialchars($title) ?></h1>
    <a href="<?= url('schools') ?>" class="btn btn-outline">← Back</a>
</div>
<div class="card">
    <form method="POST" action="<?= url('schools/store') ?>">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group">
                <label>School Name *</label>
                <input type="text" name="name" value="<?= old('name') ?>" required>
            </div>
            <div class="form-group">
                <label>Slug (auto-generated if empty)</label>
                <input type="text" name="slug" value="<?= old('slug') ?>" placeholder="my-school">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" value="<?= old('address') ?>">
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="<?= old('phone') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Principal Name</label>
                <input type="text" name="principal_name" value="<?= old('principal_name') ?>">
            </div>
            <div class="form-group">
                <label>Currency</label>
                <input type="text" name="currency" value="<?= old('currency', DEFAULT_CURRENCY) ?>">
            </div>
        </div>
        <hr style="border-color:rgba(99,102,241,.15);margin:24px 0">
        <h3 style="color:#c7d2fe;margin-bottom:16px;font-size:.95rem">School Admin (Optional)</h3>
        <div class="form-row">
            <div class="form-group">
                <label>Admin Name</label>
                <input type="text" name="admin_name" value="<?= old('admin_name') ?>">
            </div>
            <div class="form-group">
                <label>Admin Email</label>
                <input type="email" name="admin_email" value="<?= old('admin_email') ?>">
            </div>
        </div>
        <div class="form-group">
            <label>Admin Password</label>
            <input type="password" name="admin_password" placeholder="Min 6 characters">
        </div>
        <button type="submit" class="btn btn-primary">Create School</button>
    </form>
</div>
