<?php $slug = tenant_slug(); ?>
<div class="page-header"><h1>Create Fee Structure</h1><a href="<?= url("$slug/fees/structures") ?>" class="btn btn-outline">← Back</a></div>
<div class="card">
    <form method="POST" action="<?= url("$slug/fees/structures/store") ?>">
        <?= csrf_field() ?>
        <div class="form-group"><label>Fee Type *</label>
            <select name="fee_type_id" class="form-control" required>
                <option value="">Select Fee Type</option>
                <?php foreach ($types as $t): ?>
                    <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group"><label>Class *</label>
            <select name="class_id" class="form-control" required>
                <option value="">Select Class</option>
                <?php foreach ($classes as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group"><label>Amount (PKR) *</label><input type="number" name="amount" class="form-control" step="0.01" min="0" required value="<?= old('amount') ?>"></div>
        <div class="form-group"><label>Due Date</label><input type="date" name="due_date" class="form-control" value="<?= old('due_date') ?>"></div>
        <div class="form-group"><label>Academic Year</label><input type="text" name="academic_year" class="form-control" placeholder="2025-2026" value="<?= old('academic_year') ?>"></div>
        <button class="btn btn-primary">Create Structure</button>
    </form>
</div>
