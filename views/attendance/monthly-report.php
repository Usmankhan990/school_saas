<?php $slug = tenant_slug(); ?>
<div class="page-header">
    <h1>📅 Monthly Attendance Report</h1>
    <a href="<?= url("$slug/attendance") ?>" class="btn btn-outline">← Back</a>
</div>

<div class="card">
    <form method="GET" action="<?= url("$slug/attendance/monthly-report") ?>">
        <div class="form-row">
            <div class="form-group">
                <label>Class *</label>
                <select name="class_id" required>
                    <option value="">Select Class</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $classId == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Month</label>
                <select name="month">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>" <?= $month == $m ? 'selected' : '' ?>><?= date('F', mktime(0,0,0,$m,1)) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Year</label>
                <input type="number" name="year" value="<?= $year ?>" min="2020" max="2030">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">View Report</button>
    </form>
</div>

<?php if ($classId && !empty($report)): ?>
<div class="card" style="margin-top:20px">
    <h3>Report for <?= date('F', mktime(0,0,0,$month,1)) ?> <?= $year ?></h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Student Name</th>
                <th>Admission #</th>
                <th style="text-align:center">Present</th>
                <th style="text-align:center">Absent</th>
                <th style="text-align:center">Late</th>
                <th style="text-align:center">Total Days</th>
                <th>Attendance %</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($report as $i => $r): ?>
            <?php $pct = $r['total_days'] > 0 ? round(($r['present'] / $r['total_days']) * 100, 1) : 0; ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><strong><?= htmlspecialchars($r['student_name']) ?></strong></td>
                <td><?= htmlspecialchars($r['admission_no'] ?? '-') ?></td>
                <td style="text-align:center;color:#86efac"><?= $r['present'] ?></td>
                <td style="text-align:center;color:#fca5a5"><?= $r['absent'] ?></td>
                <td style="text-align:center;color:#fde68a"><?= $r['late'] ?></td>
                <td style="text-align:center"><?= $r['total_days'] ?></td>
                <td><?= $pct ?>%</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php elseif ($classId): ?>
<div class="card" style="margin-top:20px"><div class="empty">No attendance data for selected month.</div></div>
<?php endif; ?>
