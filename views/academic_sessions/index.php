<?php include 'views/layouts/school.php'; ?>

<div class="header">
    <div class="header-content">
        <h2>Academic Sessions</h2>
        <p>Manage the academic years/sessions for your school.</p>
    </div>
    <a href="/<?= $tenantSlug ?>/academic-sessions/create" class="btn btn-primary">Add Session</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sessions as $session): ?>
                    <tr>
                        <td><?= htmlspecialchars($session['name']) ?></td>
                        <td><?= htmlspecialchars($session['start_date']) ?></td>
                        <td><?= htmlspecialchars($session['end_date']) ?></td>
                        <td>
                            <?php if ($session['is_current']): ?>
                                <span class="badge badge-success">Current Session</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="/<?= $tenantSlug ?>/academic-sessions/<?= $session['id'] ?>/edit" class="btn btn-sm btn-secondary">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($sessions)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No academic sessions found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'views/layouts/footer.php'; ?>
