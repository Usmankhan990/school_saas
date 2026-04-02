<?php $slug = tenant_slug(); ?>
<div class="page-header">
    <h1>📹 Online Classes</h1>
    <a href="<?= url("$slug/online-classes/create") ?>" class="btn btn-primary">+ Schedule Class</a>
</div>

<div class="card">
    <?php if (empty($classes)): ?>
        <div class="empty">No online classes scheduled yet.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Teacher</th>
                    <th>Class</th>
                    <th>Schedule</th>
                    <th>Platform</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($classes as $c): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($c['title']) ?></strong></td>
                    <td><?= htmlspecialchars($c['teacher_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($c['class_name'] ?? '') ?> <?= $c['section_name'] ? '(' . htmlspecialchars($c['section_name']) . ')' : '' ?></td>
                    <td><?= date('d M Y, h:i A', strtotime($c['scheduled_at'])) ?></td>
                    <td><span class="badge badge-active"><?= htmlspecialchars($c['platform']) ?></span></td>
                    <td>
                        <?php if ($c['status'] === 'live'): ?>
                            <span class="badge" style="background:rgba(239,68,68,.15);color:#fca5a5">🔴 LIVE</span>
                        <?php elseif ($c['status'] === 'ended'): ?>
                            <span class="badge badge-inactive">Ended</span>
                        <?php else: ?>
                            <span class="badge badge-active">Scheduled</span>
                        <?php endif; ?>
                    </td>
                    <td class="actions">
                        <?php if ($c['status'] !== 'ended'): ?>
                            <a href="<?= url("$slug/online-classes/{$c['id']}/join") ?>" class="btn btn-sm btn-success">🎥 Join</a>
                        <?php endif; ?>
                        <a href="<?= url("$slug/online-classes/{$c['id']}/participants") ?>" class="btn btn-sm btn-outline">👥 Participants</a>
                        <?php if ($c['status'] === 'scheduled'): ?>
                            <form method="POST" action="<?= url("$slug/online-classes/{$c['id']}/status") ?>" style="display:inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="status" value="live">
                                <button class="btn btn-sm btn-primary">▶ Start</button>
                            </form>
                        <?php elseif ($c['status'] === 'live'): ?>
                            <form method="POST" action="<?= url("$slug/online-classes/{$c['id']}/status") ?>" style="display:inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="status" value="ended">
                                <button class="btn btn-sm btn-danger">⏹ End</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
