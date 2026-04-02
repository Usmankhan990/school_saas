<?php
$slug = tenant_slug();
$role = current_role();
$userName = Session::get('user_name', 'User');
$schoolName = Session::get('tenant_name', 'School');
$schoolLogo = Session::get('tenant_logo_url', '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Dashboard') ?> | <?= htmlspecialchars($schoolName) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter',sans-serif;background:#0f172a;color:#e2e8f0;min-height:100vh;display:flex}
        a{color:#818cf8;text-decoration:none}a:hover{text-decoration:underline}
        .sidebar{width:260px;background:#1e293b;border-right:1px solid rgba(99,102,241,.15);padding:20px 0;display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;overflow-y:auto}
        .sidebar .brand{padding:0 20px 20px;border-bottom:1px solid rgba(99,102,241,.1);display:flex;align-items:center;gap:12px}
        .sidebar .brand .logo-icon{width:40px;height:40px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px}
        .sidebar .brand img{width:40px;height:40px;border-radius:10px;object-fit:contain}
        .sidebar .brand span{font-weight:700;font-size:.95rem;color:#e2e8f0}
        .sidebar nav{flex:1;padding:16px 0}
        .sidebar nav a{display:flex;align-items:center;gap:10px;padding:10px 20px;color:#94a3b8;font-size:.88rem;font-weight:500;transition:all .15s;border-left:3px solid transparent}
        .sidebar nav a:hover{background:rgba(99,102,241,.08);color:#c7d2fe;text-decoration:none}
        .sidebar nav a.active{background:rgba(99,102,241,.12);color:#818cf8;border-left-color:#6366f1}
        .sidebar nav .divider{height:1px;background:rgba(99,102,241,.1);margin:12px 20px}
        .sidebar .user-info{padding:16px 20px;border-top:1px solid rgba(99,102,241,.1);font-size:.82rem;color:#64748b}
        .sidebar .user-info strong{display:block;color:#e2e8f0;font-weight:600}
        .sidebar .user-info .role{color:#818cf8;font-size:.75rem;text-transform:uppercase;letter-spacing:.5px}
        .main{margin-left:260px;flex:1;padding:28px 32px;min-height:100vh}
        .page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px}
        .page-header h1{font-size:1.6rem;font-weight:700;background:linear-gradient(135deg,#c7d2fe,#e0e7ff);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
        .btn{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:8px;font-size:.85rem;font-weight:600;font-family:inherit;cursor:pointer;border:none;transition:all .15s;text-decoration:none}
        .btn:hover{text-decoration:none;transform:translateY(-1px)}
        .btn-primary{background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff}
        .btn-primary:hover{box-shadow:0 4px 15px rgba(99,102,241,.35)}
        .btn-sm{padding:6px 12px;font-size:.8rem}
        .btn-danger{background:rgba(239,68,68,.15);color:#fca5a5;border:1px solid rgba(239,68,68,.2)}.btn-danger:hover{background:rgba(239,68,68,.25)}
        .btn-success{background:rgba(34,197,94,.15);color:#86efac;border:1px solid rgba(34,197,94,.2)}.btn-success:hover{background:rgba(34,197,94,.25)}
        .btn-outline{background:transparent;color:#94a3b8;border:1px solid rgba(99,102,241,.2)}.btn-outline:hover{border-color:#6366f1;color:#c7d2fe}
        .card{background:rgba(30,41,59,.8);backdrop-filter:blur(12px);border:1px solid rgba(99,102,241,.15);border-radius:14px;padding:24px;margin-bottom:20px}
        .card h3{font-size:1rem;font-weight:600;color:#c7d2fe;margin-bottom:16px}
        .flash{padding:12px 16px;border-radius:10px;font-size:.85rem;margin-bottom:20px}
        .flash.error{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.25);color:#fca5a5}
        .flash.success{background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.25);color:#86efac}
        table{width:100%;border-collapse:collapse}
        th{text-align:left;padding:10px 14px;font-size:.75rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid rgba(99,102,241,.1)}
        td{padding:12px 14px;font-size:.88rem;border-bottom:1px solid rgba(99,102,241,.06)}
        tr:hover td{background:rgba(99,102,241,.04)}
        .badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:.72rem;font-weight:600;text-transform:uppercase}
        .badge-active{background:rgba(34,197,94,.15);color:#86efac}.badge-inactive{background:rgba(239,68,68,.15);color:#fca5a5}
        .stat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;margin-bottom:24px}
        .stat-card{background:rgba(30,41,59,.8);border:1px solid rgba(99,102,241,.12);border-radius:12px;padding:20px}
        .stat-card .number{font-size:2rem;font-weight:700;background:linear-gradient(135deg,#6366f1,#a78bfa);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
        .stat-card .label{font-size:.8rem;color:#64748b;margin-top:4px}
        .form-group{margin-bottom:18px}
        .form-group label{display:block;font-size:.8rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px}
        .form-group input,.form-group select,.form-group textarea{width:100%;padding:10px 14px;background:rgba(15,23,42,.6);border:1px solid rgba(99,102,241,.2);border-radius:8px;color:#e2e8f0;font-size:.9rem;font-family:inherit;outline:none;transition:border-color .2s}
        .form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,.12)}
        .form-group select option{background:#1e293b;color:#e2e8f0}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
        .actions{display:flex;gap:8px;align-items:center}
        .empty{text-align:center;padding:40px;color:#475569}
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="brand">
            <?php if ($schoolLogo): ?>
                <img src="<?= htmlspecialchars($schoolLogo) ?>" alt="Logo">
            <?php else: ?>
                <div class="logo-icon">🎓</div>
            <?php endif; ?>
            <span><?= htmlspecialchars($schoolName) ?></span>
        </div>
        <nav>
            <a href="<?= url("$slug/dashboard") ?>" class="<?= ($title ?? '') === 'Dashboard' ? 'active' : '' ?>">📊 Dashboard</a>
            <?php if (has_role(['super_admin','admin','staff','teacher'])): ?>
            <a href="<?= url("$slug/students") ?>" class="<?= strpos($title ?? '','Student')!==false?'active':''?>">👨‍🎓 Students</a>
            <?php endif; ?>
            <?php if (has_role(['super_admin','admin','staff'])): ?>
            <a href="<?= url("$slug/teachers") ?>" class="<?= strpos($title ?? '','Teacher')!==false?'active':''?>">👩‍🏫 Teachers</a>
            <?php endif; ?>
            <?php if (has_role(['super_admin','admin'])): ?>
            <a href="<?= url("$slug/staff") ?>" class="<?= strpos($title ?? '','Staff')!==false?'active':''?>">👤 Staff</a>
            <?php endif; ?>
            <?php if (has_role(['super_admin','admin','staff'])): ?>
            <a href="<?= url("$slug/classes") ?>" class="<?= strpos($title ?? '','Class')!==false && strpos($title ?? '','Online')===false?'active':''?>">🏫 Classes</a>
            <a href="<?= url("$slug/subjects") ?>" class="<?= strpos($title ?? '','Subject')!==false?'active':''?>">📚 Subjects</a>
            <?php endif; ?>
            <div class="divider"></div>
            <?php if (has_role(['super_admin','admin','teacher'])): ?>
            <a href="<?= url("$slug/online-classes") ?>" class="<?= strpos($title ?? '','Online')!==false?'active':''?>">📹 Online Classes</a>
            <?php endif; ?>
            <?php if (has_role(['super_admin','admin','staff','teacher'])): ?>
            <a href="<?= url("$slug/attendance") ?>" class="<?= strpos($title ?? '','Attendance')!==false?'active':''?>">📋 Attendance</a>
            <?php endif; ?>
            <?php if (has_role(['super_admin','admin','staff'])): ?>
            <a href="<?= url("$slug/exams") ?>" class="<?= strpos($title ?? '','Exam')!==false?'active':''?>">📝 Exams</a>
            <a href="<?= url("$slug/datesheets") ?>" class="<?= strpos($title ?? '','Date Sheet')!==false?'active':''?>">🗓️ Date Sheets</a>
            <a href="<?= url("$slug/results") ?>" class="<?= strpos($title ?? '','Result')!==false?'active':''?>">📊 Results</a>
            <?php endif; ?>
            <div class="divider"></div>
            <?php if (has_role(['super_admin','admin','staff'])): ?>
            <a href="<?= url("$slug/fees/types") ?>" class="<?= strpos($title ?? '','Fee Type')!==false?'active':''?>">💰 Fee Types</a>
            <a href="<?= url("$slug/fees/structures") ?>" class="<?= strpos($title ?? '','Fee Structure')!==false?'active':''?>">📋 Fee Structures</a>
            <a href="<?= url("$slug/fees/student-fees") ?>" class="<?= strpos($title ?? '','Student Fee')!==false?'active':''?>">🧾 Student Fees</a>
            <a href="<?= url("$slug/fees/defaulters") ?>" class="<?= strpos($title ?? '','Defaulter')!==false?'active':''?>">⚠️ Defaulters</a>
            <?php endif; ?>
            <a href="<?= url("$slug/payments") ?>" class="<?= strpos($title ?? '','Payment')!==false && strpos($title ?? '','History')===false?'active':''?>">💳 Payments</a>
            <?php if (has_role(['super_admin','admin'])): ?>
            <a href="<?= url("$slug/finance") ?>" class="<?= strpos($title ?? '','Finance')!==false?'active':''?>">📊 Finance</a>
            <a href="<?= url("$slug/reports/fees") ?>" class="<?= strpos($title ?? '','Report')!==false?'active':''?>">📋 Reports</a>
            <?php endif; ?>
            <div class="divider"></div>
            <a href="<?= url("$slug/notifications") ?>" class="<?= strpos($title ?? '','Notification')!==false?'active':''?>">🔔 Notifications<?php
                try {
                    $unreadCount = Notification::unreadCount(current_user_id(), current_school_id());
                    if ($unreadCount > 0) echo ' <span style="background:#6366f1;color:#fff;border-radius:10px;padding:1px 7px;font-size:.7rem;margin-left:4px">' . $unreadCount . '</span>';
                } catch (Exception $e) {}
            ?></a>
            <?php if (has_role(['super_admin','admin'])): ?>
            <a href="<?= url("$slug/users") ?>" class="<?= strpos($title ?? '','User')!==false?'active':''?>">🔑 Users</a>
            <a href="<?= url("$slug/settings") ?>" class="<?= strpos($title ?? '','Setting')!==false?'active':''?>">⚙️ Settings</a>
            <?php endif; ?>
        </nav>
        <div class="user-info">
            <strong><?= htmlspecialchars($userName) ?></strong>
            <span class="role"><?= htmlspecialchars($role ?? '') ?></span><br>
            <a href="<?= url("$slug/logout") ?>" style="color:#f87171;font-size:.8rem;margin-top:8px;display:inline-block">Logout</a>
        </div>
    </aside>
    <main class="main">
        <?php $flashType = Session::flash('flash_type'); $flashMsg = Session::flash('flash_message'); ?>
        <?php if ($flashMsg): ?>
            <div class="flash <?= htmlspecialchars($flashType) ?>"><?= $flashMsg ?></div>
        <?php endif; ?>
        <?= $content ?>
    </main>
</body>
</html>
