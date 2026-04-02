<?php $formAction = url('register'); $flashType = Session::get('flash_type'); $flashMsg = Session::get('flash_message'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Your School | <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;background:#0f172a;color:#e2e8f0;position:relative;overflow:hidden}
        body::before{content:'';position:absolute;width:600px;height:600px;background:radial-gradient(circle,rgba(99,102,241,.15),transparent 70%);top:-200px;right:-200px;border-radius:50%}
        .register-card{position:relative;z-index:1;background:rgba(30,41,59,.8);backdrop-filter:blur(20px);border:1px solid rgba(99,102,241,.2);border-radius:20px;padding:40px;width:100%;max-width:480px;box-shadow:0 25px 60px rgba(0,0,0,.4)}
        .register-card .logo{text-align:center;margin-bottom:8px}
        .register-card .logo .icon{width:56px;height:56px;margin:0 auto;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:24px}
        .register-card h1{text-align:center;font-size:1.4rem;font-weight:700;margin-bottom:4px;background:linear-gradient(135deg,#c7d2fe,#e0e7ff);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
        .register-card .subtitle{text-align:center;color:#94a3b8;font-size:.85rem;margin-bottom:28px}
        .flash{padding:12px 16px;border-radius:10px;font-size:.85rem;margin-bottom:16px}
        .flash.error{background:rgba(239,68,68,.15);border:1px solid rgba(239,68,68,.3);color:#fca5a5}
        .flash.success{background:rgba(34,197,94,.15);border:1px solid rgba(34,197,94,.3);color:#86efac}
        .form-group{margin-bottom:16px}
        .form-group label{display:block;font-size:.78rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px}
        .form-group input{width:100%;padding:10px 14px;background:rgba(15,23,42,.6);border:1px solid rgba(99,102,241,.25);border-radius:8px;color:#e2e8f0;font-size:.9rem;font-family:inherit;outline:none;transition:border-color .2s}
        .form-group input:focus{border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,.15)}
        .form-group input::placeholder{color:#475569}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
        .btn-register{width:100%;padding:12px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border:none;border-radius:8px;color:#fff;font-size:.95rem;font-weight:600;font-family:inherit;cursor:pointer;transition:transform .15s,box-shadow .15s;margin-top:4px}
        .btn-register:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(99,102,241,.35)}
        .footer-text{text-align:center;margin-top:20px;font-size:.82rem;color:#64748b}
        .footer-text a{color:#818cf8;text-decoration:none;font-weight:500}
    </style>
</head>
<body>
    <div class="register-card">
        <div class="logo"><div class="icon">🏫</div></div>
        <h1>Register Your School</h1>
        <p class="subtitle">Create a new school on <?= APP_NAME ?></p>
        <?php if ($flashMsg): ?><div class="flash <?= htmlspecialchars($flashType ?? 'error') ?>"><?= $flashMsg ?></div><?php endif; ?>
        <form method="POST" action="<?= $formAction ?>">
            <?= csrf_field() ?>
            <div class="form-group"><label>School Name *</label><input type="text" name="school_name" placeholder="e.g. City Grammar School" value="<?= old('school_name') ?>" required></div>
            <div class="form-row">
                <div class="form-group"><label>URL Slug</label><input type="text" name="slug" placeholder="city-grammar" value="<?= old('slug') ?>"></div>
                <div class="form-group"><label>Phone</label><input type="text" name="phone" placeholder="+92..." value="<?= old('phone') ?>"></div>
            </div>
            <div class="form-group"><label>Admin Name *</label><input type="text" name="admin_name" placeholder="Full Name" value="<?= old('admin_name') ?>" required></div>
            <div class="form-group"><label>Admin Email *</label><input type="email" name="email" placeholder="admin@school.com" value="<?= old('email') ?>" required></div>
            <div class="form-group"><label>Password *</label><input type="password" name="password" placeholder="Min 6 characters" required></div>
            <button type="submit" class="btn-register">Register School</button>
        </form>
        <p class="footer-text">Already registered? <a href="<?= url('login') ?>">Login here</a></p>
    </div>
</body>
</html>
