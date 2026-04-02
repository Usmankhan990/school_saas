<?php $slug = tenant_slug(); ?>
<div class="page-header">
    <h1>📋 Attendance</h1>
</div>

<div class="card">
    <h3>Select Class & Date to Mark Attendance</h3>
    <form method="GET" action="<?= url("$slug/attendance/mark") ?>">
        <div class="form-row">
            <div class="form-group">
                <label>Class *</label>
                <select name="class_id" required>
                    <option value="">Select Class</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Date *</label>
                <input type="date" name="date" value="<?= date('Y-m-d') ?>" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">📋 Mark Attendance</button>
    </form>
</div>

<div class="card" style="margin-top:20px">
    <h3>Reports</h3>
    <div class="actions" style="gap:12px">
        <a href="<?= url("$slug/attendance/daily-report?date=" . date('Y-m-d')) ?>" class="btn btn-outline">📊 Today's Report</a>
        <a href="<?= url("$slug/attendance/monthly-report") ?>" class="btn btn-outline">📅 Monthly Report</a>
    </div>
</div>
