<div class="page-header">
    <h1><?= htmlspecialchars($title) ?></h1>
    <a href="<?= tenant_url('students') ?>" class="btn btn-outline">← Back</a>
</div>
<div class="card">
    <form method="POST" action="<?= tenant_url('students/store') ?>">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group">
                <label>Admission No *</label>
                <input type="text" name="admission_no" value="<?= old('admission_no', $nextAdmNo ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" value="<?= old('name') ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Father's Name</label>
                <input type="text" name="father_name" value="<?= old('father_name') ?>">
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="<?= old('phone') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Class</label>
                <select name="class_id">
                    <option value="">— Select —</option>
                    <?php foreach ($classes as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= old('class_id')==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Section</label>
                <select name="section_id">
                    <option value="">— Select —</option>
                    <?php foreach ($sections as $sec): ?>
                    <option value="<?= $sec['id'] ?>" <?= old('section_id')==$sec['id']?'selected':'' ?>><?= htmlspecialchars($sec['name']) ?> (<?= htmlspecialchars($sec['class_name'] ?? '') ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" name="dob" value="<?= old('dob') ?>">
            </div>
            <div class="form-group">
                <label>Gender</label>
                <select name="gender">
                    <option value="">— Select —</option>
                    <option value="male" <?= old('gender')=='male'?'selected':'' ?>>Male</option>
                    <option value="female" <?= old('gender')=='female'?'selected':'' ?>>Female</option>
                    <option value="other" <?= old('gender')=='other'?'selected':'' ?>>Other</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>Address</label>
            <input type="text" name="address" value="<?= old('address') ?>">
        </div>
        <button type="submit" class="btn btn-primary">Admit Student</button>
    </form>
</div>
