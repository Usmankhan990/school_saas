<div class="page-header">
    <h1><?= htmlspecialchars($title) ?></h1>
    <a href="<?= url('schools') ?>" class="btn btn-outline">← Back</a>
</div>
<div class="card">
    <form method="POST" action="<?= url('schools/' . $school['id'] . '/update') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group">
                <label>School Name *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($school['name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Slug</label>
                <input type="text" name="slug" value="<?= htmlspecialchars($school['slug']) ?>">
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
                <label>Principal Name</label>
                <input type="text" name="principal_name" value="<?= htmlspecialchars($school['principal_name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Currency</label>
                <input type="text" name="currency" value="<?= htmlspecialchars($school['currency'] ?? DEFAULT_CURRENCY) ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Academic Session</label>
                <input type="text" name="academic_session" value="<?= htmlspecialchars($school['academic_session'] ?? '') ?>" placeholder="2024-2025">
            </div>
            <div class="form-group">
                <label>School Logo</label>
                <input type="file" name="logo" accept="image/jpeg,image/png" style="padding:8px">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Update School</button>
    </form>
</div>
