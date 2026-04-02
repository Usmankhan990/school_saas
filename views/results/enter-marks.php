<?php $slug = tenant_slug(); ?>
<div class="page-header">
    <h1>Enter Marks — <?= htmlspecialchars($exam['name']) ?> | <?= htmlspecialchars($class['name'] ?? '') ?></h1>
    <a href="<?= url("$slug/results") ?>" class="btn btn-outline">← Back</a>
</div>

<div class="card">
    <?php if (empty($students) || empty($subjects)): ?>
        <div class="empty">No students or subjects found. Please add students and subjects first.</div>
    <?php else: ?>
        <form method="POST" action="<?= url("$slug/results/store-marks") ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="exam_id" value="<?= $exam['id'] ?>">
            <input type="hidden" name="class_id" value="<?= $classId ?>">

            <div style="margin-bottom:16px">
                <h3>Max Marks per Subject</h3>
                <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:16px">
                    <?php foreach ($subjects as $sub): ?>
                    <div class="form-group" style="margin-bottom:0;width:120px">
                        <label style="font-size:.7rem"><?= htmlspecialchars($sub['name']) ?></label>
                        <input type="number" name="max_marks[<?= $sub['id'] ?>]"
                               value="<?= isset($marksMap) && !empty($marksMap) ? ($marksMap[array_key_first($marksMap)][$sub['id']]['max_marks'] ?? 100) : 100 ?>"
                               min="0" max="500" style="padding:6px 8px;font-size:.8rem">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div style="overflow-x:auto">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <?php foreach ($subjects as $sub): ?>
                                <th style="text-align:center;min-width:80px"><?= htmlspecialchars($sub['name']) ?></th>
                            <?php endforeach; ?>
                            <th style="text-align:center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $i => $s): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><strong><?= htmlspecialchars($s['name']) ?></strong><br><small style="color:#64748b"><?= htmlspecialchars($s['admission_no'] ?? '') ?></small></td>
                            <?php foreach ($subjects as $sub): ?>
                                <td style="text-align:center">
                                    <input type="number" name="marks[<?= $s['id'] ?>][<?= $sub['id'] ?>]"
                                           value="<?= $marksMap[$s['id']][$sub['id']]['marks'] ?? '' ?>"
                                           min="0" max="500" step="0.5"
                                           style="width:70px;padding:6px;text-align:center;background:rgba(15,23,42,.6);border:1px solid rgba(99,102,241,.2);border-radius:6px;color:#e2e8f0;font-size:.85rem">
                                </td>
                            <?php endforeach; ?>
                            <td style="text-align:center">
                                <a href="<?= url("$slug/results/{$exam['id']}/student/{$s['id']}") ?>" class="btn btn-sm btn-outline">📄 View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div style="margin-top:20px">
                <button type="submit" class="btn btn-primary">💾 Save Marks</button>
            </div>
        </form>
    <?php endif; ?>
</div>
