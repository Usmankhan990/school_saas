<?php $slug = tenant_slug(); ?>
<div class="page-header">
    <h1>🔔 Notifications</h1>
    <form method="POST" action="<?= url("$slug/notifications/mark-all-read") ?>" style="display:inline">
        <?= csrf_field() ?>
        <button class="btn btn-outline">✓ Mark All Read</button>
    </form>
</div>

<div class="card">
    <?php if (empty($notifications)): ?>
        <div class="empty">No notifications yet.</div>
    <?php else: ?>
        <?php foreach ($notifications as $n): ?>
        <div style="padding:14px 16px;border-bottom:1px solid rgba(99,102,241,.08);display:flex;align-items:center;gap:14px;<?= !$n['is_read'] ? 'background:rgba(99,102,241,.06)' : '' ?>">
            <div style="width:10px;height:10px;border-radius:50%;<?= !$n['is_read'] ? 'background:#6366f1' : 'background:transparent' ?>;flex-shrink:0"></div>
            <div style="flex:1">
                <strong style="color:#e2e8f0"><?= htmlspecialchars($n['title']) ?></strong>
                <?php if ($n['body']): ?>
                    <p style="margin:4px 0 0;font-size:.85rem;color:#94a3b8"><?= htmlspecialchars($n['body']) ?></p>
                <?php endif; ?>
                <small style="color:#475569"><?= date('d M Y, h:i A', strtotime($n['created_at'])) ?> · <?= htmlspecialchars($n['type']) ?></small>
            </div>
            <?php if (!$n['is_read']): ?>
            <form method="POST" action="<?= url("$slug/notifications/{$n['id']}/read") ?>">
                <?= csrf_field() ?>
                <button class="btn btn-sm btn-outline">✓</button>
            </form>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
