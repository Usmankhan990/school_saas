<div class="page-header">
    <h1><?= htmlspecialchars($title) ?></h1>
</div>
<div class="card">
    <form method="POST" action="<?= tenant_url('settings') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group">
                <label>School Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($school['name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Principal Name</label>
                <input type="text" name="principal_name" value="<?= htmlspecialchars($school['principal_name'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" value="<?= htmlspecialchars($school['address'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($school['phone'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Academic Session</label>
                <input type="text" name="academic_session" value="<?= htmlspecialchars($school['academic_session'] ?? '') ?>" placeholder="2024-2025">
            </div>
            <div class="form-group">
                <label>Currency</label>
                <input type="text" name="currency" value="<?= htmlspecialchars($school['currency'] ?? DEFAULT_CURRENCY) ?>">
            </div>
        </div>
        <div class="form-group">
            <label>School Logo</label>
            <input type="file" name="logo" accept="image/jpeg,image/png" style="padding:8px">
            <?php if (!empty($school['logo_url'])): ?>
                <p style="margin-top:8px;font-size:.82rem;color:#64748b">Current: <?= htmlspecialchars($school['logo_url']) ?></p>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>
</div>
