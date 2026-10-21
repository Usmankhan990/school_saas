<div class="page-header">
    <h1><?= htmlspecialchars($title) ?></h1>
</div>
<div class="card">
    <form method="POST" action="<?= tenant_url('settings') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group">
                <label>School Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($school['name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Principal Name</label>
                <input type="text" name="principal_name" value="<?= htmlspecialchars($school['principal_name'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" value="<?= htmlspecialchars($school['address'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($school['phone'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Academic Session</label>
                <input type="text" name="academic_session" value="<?= htmlspecialchars($school['academic_session'] ?? '') ?>" placeholder="2024-2025">
            </div>
            <div class="form-group">
                <label>Currency</label>
                <input type="text" name="currency" value="<?= htmlspecialchars($school['currency'] ?? DEFAULT_CURRENCY) ?>">
            </div>
        </div>
        <div class="form-group">
            <label>School Logo</label>
            <input type="file" name="logo" accept="image/jpeg,image/png" style="padding:8px">
            <?php if (!empty($school['logo_url'])): ?>
                <p style="margin-top:8px;font-size:.82rem;color:#64748b">Current: <?= htmlspecialchars($school['logo_url']) ?></p>
            <?php endif; ?>
        </div>
        
        <hr style="margin: 20px 0; border: none; border-top: 1px solid #e2e8f0;">
        <h3>System Settings</h3>
        
        <div class="form-row">
            <div class="form-group">
                <label>Timezone</label>
                <input type="text" name="timezone" value="<?= htmlspecialchars($settings['timezone'] ?? 'UTC') ?>" placeholder="e.g., Asia/Karachi">
            </div>
            <div class="form-group">
                <label>Date Format</label>
                <input type="text" name="date_format" value="<?= htmlspecialchars($settings['date_format'] ?? 'Y-m-d') ?>" placeholder="e.g., Y-m-d">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Attendance Type</label>
                <select name="attendance_type" class="form-control">
                    <option value="daily" <?= ($settings['attendance_type'] ?? '') === 'daily' ? 'selected' : '' ?>>Daily (Once a day)</option>
                    <option value="subject" <?= ($settings['attendance_type'] ?? '') === 'subject' ? 'selected' : '' ?>>Subject-wise (Per period)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Receipt Footer</label>
                <input type="text" name="receipt_footer" value="<?= htmlspecialchars($settings['receipt_footer'] ?? '') ?>" placeholder="Thank you for your payment">
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="margin-top: 15px;">Save Settings</button>
    </form>
</div>
