<div class="page-header">
    <h1><?= htmlspecialchars($title) ?></h1>
    <a href="<?= tenant_url('classes') ?>" class="btn btn-outline">← Back</a>
</div>
<div class="card">
    <form method="POST" action="<?= tenant_url('classes/store') ?>">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group">
                <label>Class Name *</label>
                <input type="text" name="name" value="<?= old('name') ?>" required placeholder="e.g. Grade 1, Class 10">
            </div>
            <div class="form-group">
                <label>Numeric Name</label>
                <input type="text" name="numeric_name" value="<?= old('numeric_name') ?>" placeholder="e.g. 1, 10">
            </div>
        </div>
        <div class="form-group">
            <label>Sections (comma-separated)</label>
            <input type="text" name="sections" value="<?= old('sections') ?>" placeholder="e.g. A, B, C">
        </div>
        <button type="submit" class="btn btn-primary">Create Class</button>
    </form>
</div>
