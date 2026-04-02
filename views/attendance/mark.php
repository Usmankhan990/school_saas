<?php $slug = tenant_slug(); ?>
<div class="page-header">
    <h1>Mark Attendance — <?= htmlspecialchars($class['name'] ?? '') ?> (<?= date('d M Y', strtotime($date)) ?>)</h1>
    <a href="<?= url("$slug/attendance") ?>" class="btn btn-outline">← Back</a>
</div>

<div class="card">
    <?php if (empty($students)): ?>
        <div class="empty">No students found in this class.</div>
    <?php else: ?>
        <form method="POST" action="<?= url("$slug/attendance/store") ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="class_id" value="<?= $classId ?>">
            <input type="hidden" name="section_id" value="<?= $sectionId ?>">
            <input type="hidden" name="date" value="<?= $date ?>">

            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>Admission #</th>
                        <th style="text-align:center">Present</th>
                        <th style="text-align:center">Absent</th>
                        <th style="text-align:center">Late</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $i => $s): ?>
                    <?php $current = $attendanceMap[$s['id']] ?? 'P'; ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><strong><?= htmlspecialchars($s['name']) ?></strong></td>
                        <td><?= htmlspecialchars($s['admission_no'] ?? '-') ?></td>
                        <td style="text-align:center">
                            <input type="radio" name="status[<?= $s['id'] ?>]" value="P" <?= $current === 'P' ? 'checked' : '' ?>>
                        </td>
                        <td style="text-align:center">
                            <input type="radio" name="status[<?= $s['id'] ?>]" value="A" <?= $current === 'A' ? 'checked' : '' ?>>
                        </td>
                        <td style="text-align:center">
                            <input type="radio" name="status[<?= $s['id'] ?>]" value="L" <?= $current === 'L' ? 'checked' : '' ?>>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div style="margin-top:20px">
                <button type="submit" class="btn btn-primary">💾 Save Attendance</button>
            </div>
        </form>
    <?php endif; ?>
</div>
