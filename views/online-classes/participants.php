<?php $slug = tenant_slug(); ?>
<div class="page-header">
    <h1>👥 <?= htmlspecialchars($class['title']) ?> — Participants</h1>
    <a href="<?= url("$slug/online-classes") ?>" class="btn btn-outline">← Back</a>
</div>

<div class="stat-grid">
    <div class="stat-card"><div class="number"><?= count($participants) ?></div><div class="label">Total Joined</div></div>
    <div class="stat-card"><div class="number"><?= htmlspecialchars($class['status']) ?></div><div class="label">Status</div></div>
    <div class="stat-card"><div class="number"><?= date('h:i A', strtotime($class['scheduled_at'])) ?></div><div class="label">Scheduled Time</div></div>
</div>

<div class="card">
    <?php if (empty($participants)): ?>
        <div class="empty">No students have joined yet.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>Admission #</th>
                    <th>Joined At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($participants as $i => $p): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><strong><?= htmlspecialchars($p['student_name'] ?? 'Unknown') ?></strong></td>
                    <td><?= htmlspecialchars($p['admission_no'] ?? '-') ?></td>
                    <td><?= date('d M Y, h:i A', strtotime($p['joined_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
