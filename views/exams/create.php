<?php $slug = tenant_slug(); ?>
<div class="page-header">
    <h1>Create Exam</h1>
    <a href="<?= url("$slug/exams") ?>" class="btn btn-outline">← Back</a>
</div>

<div class="card">
    <form method="POST" action="<?= url("$slug/exams/store") ?>">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group">
                <label>Exam Name *</label>
                <input type="text" name="name" value="<?= old('name') ?>" required placeholder="e.g. Mid Term 2026">
            </div>
            <div class="form-group">
                <label>Type</label>
                <select name="type">
                    <option value="term" <?= old('type') === 'term' ? 'selected' : '' ?>>Term Exam</option>
                    <option value="midterm" <?= old('type') === 'midterm' ? 'selected' : '' ?>>Mid Term</option>
                    <option value="final" <?= old('type') === 'final' ? 'selected' : '' ?>>Final Exam</option>
                    <option value="test" <?= old('type') === 'test' ? 'selected' : '' ?>>Class Test</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Class (Optional)</label>
                <select name="class_id">
                    <option value="">All Classes</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Start Date</label>
                <input type="date" name="start_date" value="<?= old('start_date') ?>">
            </div>
            <div class="form-group">
                <label>End Date</label>
                <input type="date" name="end_date" value="<?= old('end_date') ?>">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Create Exam</button>
    </form>
</div>
