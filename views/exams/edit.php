<?php $slug = tenant_slug(); ?>
<div class="page-header">
    <h1>Edit Exam</h1>
    <a href="<?= url("$slug/exams") ?>" class="btn btn-outline">← Back</a>
</div>

<div class="card">
    <form method="POST" action="<?= url("$slug/exams/{$exam['id']}/update") ?>">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group">
                <label>Exam Name *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($exam['name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Type</label>
                <select name="type">
                    <option value="term" <?= ($exam['type'] ?? '') === 'term' ? 'selected' : '' ?>>Term Exam</option>
                    <option value="midterm" <?= ($exam['type'] ?? '') === 'midterm' ? 'selected' : '' ?>>Mid Term</option>
                    <option value="final" <?= ($exam['type'] ?? '') === 'final' ? 'selected' : '' ?>>Final Exam</option>
                    <option value="test" <?= ($exam['type'] ?? '') === 'test' ? 'selected' : '' ?>>Class Test</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Class</label>
                <select name="class_id">
                    <option value="">All Classes</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($exam['class_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Start Date</label>
                <input type="date" name="start_date" value="<?= $exam['start_date'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>End Date</label>
                <input type="date" name="end_date" value="<?= $exam['end_date'] ?? '' ?>">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Update Exam</button>
    </form>
</div>
