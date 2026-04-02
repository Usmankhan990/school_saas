<?php $slug = tenant_slug(); ?>
<div class="page-header">
    <h1>Edit Subject</h1>
    <a href="<?= url("$slug/subjects") ?>" class="btn btn-outline">← Back</a>
</div>

<div class="card">
    <form method="POST" action="<?= url("$slug/subjects/{$subject['id']}/update") ?>">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group">
                <label>Subject Name *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($subject['name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Subject Code</label>
                <input type="text" name="code" value="<?= htmlspecialchars($subject['code'] ?? '') ?>">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Update Subject</button>
    </form>
</div>
