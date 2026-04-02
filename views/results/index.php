<?php $slug = tenant_slug(); ?>
<div class="page-header">
    <h1>📊 Results</h1>
</div>

<div class="card">
    <h3>Enter / View Marks</h3>
    <p style="color:#64748b;margin-bottom:16px">Select an exam and class to enter marks or view results.</p>

    <div class="form-row">
        <?php if (!empty($exams)): ?>
        <div style="flex:1">
            <h3 style="color:#818cf8;margin-bottom:12px">By Exam</h3>
            <?php foreach ($exams as $e): ?>
                <div style="display:flex;gap:8px;align-items:center;margin-bottom:8px">
                    <span style="flex:1"><?= htmlspecialchars($e['name']) ?></span>
                    <?php if (!empty($classes)): ?>
                        <?php foreach ($classes as $c): ?>
                            <a href="<?= url("$slug/results/{$e['id']}/enter-marks?class_id={$c['id']}") ?>" class="btn btn-sm btn-outline"><?= htmlspecialchars($c['name']) ?></a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <div class="empty">No exams created yet. <a href="<?= url("$slug/exams/create") ?>">Create one</a>.</div>
        <?php endif; ?>
    </div>
</div>
