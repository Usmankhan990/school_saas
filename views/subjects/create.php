<?php $slug = tenant_slug(); ?>
<div class="page-header">
    <h1>Add Subject</h1>
    <a href="<?= url("$slug/subjects") ?>" class="btn btn-outline">← Back</a>
</div>

<div class="card">
    <form method="POST" action="<?= url("$slug/subjects/store") ?>">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group">
                <label>Subject Name *</label>
                <input type="text" name="name" value="<?= old('name') ?>" required placeholder="e.g. Mathematics">
            </div>
            <div class="form-group">
                <label>Subject Code</label>
                <input type="text" name="code" value="<?= old('code') ?>" placeholder="e.g. MATH">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Save Subject</button>
    </form>
</div>
