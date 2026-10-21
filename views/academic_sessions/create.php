<?php include 'views/layouts/school.php'; ?>

<div class="header">
    <div class="header-content">
        <h2>Add Academic Session</h2>
        <p>Create a new academic session/year.</p>
    </div>
    <a href="/<?= $tenantSlug ?>/academic-sessions" class="btn btn-secondary">Back</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="/<?= $tenantSlug ?>/academic-sessions/store" method="POST">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="form-group">
                <label class="form-label">Session Name (e.g., 2026-2027)</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>
            </div>

            <div class="form-group" style="margin-top: 15px;">
                <label style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="is_current" value="1">
                    Set as Current Academic Session
                </label>
            </div>
            
            <div style="margin-top: 20px;">
                <button type="submit" class="btn btn-primary">Save Session</button>
            </div>
        </form>
    </div>
</div>

<?php include 'views/layouts/footer.php'; ?>
