<?php $slug = tenant_slug(); ?>
<div class="page-header">
    <h1>Schedule Online Class</h1>
    <a href="<?= url("$slug/online-classes") ?>" class="btn btn-outline">← Back</a>
</div>

<div class="card">
    <form method="POST" action="<?= url("$slug/online-classes/store") ?>">
        <?= csrf_field() ?>
        <div class="form-group">
            <label>Title *</label>
            <input type="text" name="title" value="<?= old('title') ?>" required placeholder="e.g. Mathematics - Chapter 5">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Class *</label>
                <select name="class_id" required>
                    <option value="">Select Class</option>
                    <?php foreach ($classList as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Section</label>
                <select name="section_id">
                    <option value="">All Sections</option>
                    <?php foreach ($sections as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?> (<?= htmlspecialchars($s['class_name'] ?? '') ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Teacher *</label>
                <select name="teacher_id" required>
                    <option value="">Select Teacher</option>
                    <?php foreach ($teachers as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Platform</label>
                <select name="platform">
                    <option value="zoom">Zoom</option>
                    <option value="google_meet">Google Meet</option>
                    <option value="jitsi">Jitsi (Free)</option>
                    <option value="webrtc">WebRTC</option>
                    <option value="other">Other</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Date & Time *</label>
                <input type="datetime-local" name="scheduled_at" value="<?= old('scheduled_at') ?>" required>
            </div>
            <div class="form-group">
                <label>Duration (minutes)</label>
                <input type="number" name="duration_minutes" value="<?= old('duration_minutes') ?: '60' ?>" min="15" max="180">
            </div>
        </div>
        <div class="form-group">
            <label>Meeting Link *</label>
            <input type="url" name="meeting_link" value="<?= old('meeting_link') ?>" required placeholder="https://zoom.us/j/...">
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3" placeholder="Class description..."><?= old('description') ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">📹 Schedule Class</button>
    </form>
</div>
