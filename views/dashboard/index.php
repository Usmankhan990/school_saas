<div class="page-header">
    <h1><?= htmlspecialchars($title) ?></h1>
</div>
<?php if (has_role(['super_admin','admin','staff'])): ?>
<div class="stat-grid">
    <div class="stat-card">
        <div class="number"><?= $totalStudents ?? 0 ?></div>
        <div class="label">Total Students</div>
    </div>
    <div class="stat-card">
        <div class="number"><?= $activeStudents ?? 0 ?></div>
        <div class="label">Active Students</div>
    </div>
    <div class="stat-card">
        <div class="number"><?= $totalTeachers ?? 0 ?></div>
        <div class="label">Teachers</div>
    </div>
    <div class="stat-card">
        <div class="number"><?= $totalStaff ?? 0 ?></div>
        <div class="label">Staff</div>
    </div>
    <div class="stat-card">
        <div class="number"><?= $totalClasses ?? 0 ?></div>
        <div class="label">Classes</div>
    </div>
    <div class="stat-card">
        <div class="number"><?= $totalSections ?? 0 ?></div>
        <div class="label">Sections</div>
    </div>
    <div class="stat-card" style="border-top: 3px solid #22c55e;">
        <div class="number"><?= $todayPresent ?? 0 ?></div>
        <div class="label">Today's Present</div>
    </div>
    <div class="stat-card" style="border-top: 3px solid #ef4444;">
        <div class="number"><?= $todayAbsent ?? 0 ?></div>
        <div class="label">Today's Absent</div>
    </div>
    <div class="stat-card" style="border-top: 3px solid #3b82f6;">
        <div class="number"><?= isset($totalFeesCollected) ? number_format($totalFeesCollected, 2) : '0.00' ?></div>
        <div class="label">Total Fees Collected</div>
    </div>
    <div class="stat-card" style="border-top: 3px solid #f59e0b;">
        <div class="number"><?= isset($totalFeesPending) ? number_format($totalFeesPending, 2) : '0.00' ?></div>
        <div class="label">Pending Fees</div>
    </div>
    <div class="stat-card">
        <div class="number"><?= $totalUsers ?? 0 ?></div>
        <div class="label">Users</div>
    </div>
</div>
<?php endif; ?>

<?php if ($role === 'teacher'): ?>
<div class="stat-grid">
    <div class="stat-card">
        <div class="number"><?= $assignedClasses ?? 0 ?></div>
        <div class="label">Assigned Classes</div>
    </div>
    <div class="stat-card">
        <div class="number"><?= $totalStudents ?? 0 ?></div>
        <div class="label">My Students</div>
    </div>
</div>
<?php endif; ?>

<?php if ($role === 'student'): ?>
<div class="card">
    <h3>Welcome!</h3>
    <p style="color:#94a3b8">Your student dashboard will be available soon.</p>
</div>
<?php endif; ?>

<div class="card">
    <h3>Quick Actions</h3>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
        <?php if (has_role(['super_admin','admin','staff'])): ?>
        <a href="<?= tenant_url('students') ?>" class="btn btn-outline">👨‍🎓 Students</a>
        <a href="<?= tenant_url('teachers') ?>" class="btn btn-outline">👩‍🏫 Teachers</a>
        <a href="<?= tenant_url('classes') ?>" class="btn btn-outline">🏫 Classes</a>
        <?php endif; ?>
        <?php if (has_role(['super_admin','admin'])): ?>
        <a href="<?= tenant_url('users') ?>" class="btn btn-outline">🔑 Users</a>
        <a href="<?= tenant_url('settings') ?>" class="btn btn-outline">⚙️ Settings</a>
        <?php endif; ?>
    </div>
</div>
