<?php $slug = tenant_slug(); ?>
<div class="page-header"><h1>Edit Fee Type</h1><a href="<?= url("$slug/fees/types") ?>" class="btn btn-outline">← Back</a></div>
<div class="card">
    <form method="POST" action="<?= url("$slug/fees/types/{$type['id']}/update") ?>">
        <?= csrf_field() ?>
        <div class="form-group"><label>Fee Type Name *</label><input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($type['name']) ?>"></div>
        <div class="form-group"><label>Description</label><textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($type['description'] ?? '') ?></textarea></div>
        <button class="btn btn-primary">Update</button>
    </form>
</div>
