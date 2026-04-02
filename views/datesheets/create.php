<?php $slug = tenant_slug(); ?>
<div class="page-header">
    <h1>Add Date Sheet Entry</h1>
    <a href="<?= url("$slug/datesheets") ?>" class="btn btn-outline">← Back</a>
</div>

<div class="card">
    <form method="POST" action="<?= url("$slug/datesheets/store") ?>">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group">
                <label>Exam *</label>
                <select name="exam_id" required>
                    <option value="">Select Exam</option>
                    <?php foreach ($exams as $e): ?>
                        <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Class *</label>
                <select name="class_id" required>
                    <option value="">Select Class</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>Subject *</label>
            <select name="subject_id" required>
                <option value="">Select Subject</option>
                <?php foreach ($subjects as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Exam Date *</label>
                <input type="date" name="exam_date" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Start Time *</label>
                <input type="time" name="start_time" required>
            </div>
            <div class="form-group">
                <label>End Time *</label>
                <input type="time" name="end_time" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Add to Date Sheet</button>
    </form>
</div>
