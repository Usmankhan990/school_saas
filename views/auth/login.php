<?php
/**
 * Login Page - Platform Admin & School Login
 * Variables: $title, $isSchool, $schoolName?, $schoolLogo?, $slug?
 */
$formAction = isset($isSchool) && $isSchool ? url(($slug ?? '') . '/login') : url('login');
$flashType  = Session::get('flash_type');
$flashMsg   = Session::get('flash_message');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Login') ?> | <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0f172a;
            color: #e2e8f0;
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(99,102,241,.15), transparent 70%);
            top: -200px; right: -200px;
            border-radius: 50%;
        }
        body::after {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(139,92,246,.12), transparent 70%);
            bottom: -150px; left: -150px;
            border-radius: 50%;
        }
        .login-card {
            position: relative; z-index: 1;
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(99,102,241,.2);
            border-radius: 20px;
            padding: 48px 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 60px rgba(0,0,0,.4);
        }
        .login-card .logo {
            text-align: center;
            margin-bottom: 8px;
        }
        .login-card .logo img {
            width: 64px; height: 64px;
            object-fit: contain;
            border-radius: 14px;
        }
        .login-card .logo .icon {
            width: 64px; height: 64px;
            margin: 0 auto;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }
        .login-card h1 {
            text-align: center;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 4px;
            background: linear-gradient(135deg, #c7d2fe, #e0e7ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .login-card .subtitle {
            text-align: center;
            color: #94a3b8;
            font-size: .875rem;
            margin-bottom: 32px;
        }
        .flash {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: .85rem;
            margin-bottom: 20px;
        }
        .flash.error {
            background: rgba(239,68,68,.15);
            border: 1px solid rgba(239,68,68,.3);
            color: #fca5a5;
        }
        .flash.success {
            background: rgba(34,197,94,.15);
            border: 1px solid rgba(34,197,94,.3);
            color: #86efac;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: .8rem;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 8px;
        }
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(99,102,241,.25);
            border-radius: 10px;
            color: #e2e8f0;
            font-size: .95rem;
            font-family: inherit;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-group input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,.15);
        }
        .form-group input::placeholder { color: #475569; }
        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: transform .15s, box-shadow .15s;
            margin-top: 6px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99,102,241,.35);
        }
        .btn-login:active { transform: translateY(0); }
        .footer-text {
            text-align: center;
            margin-top: 24px;
            font-size: .82rem;
            color: #64748b;
        }
        .footer-text a {
            color: #818cf8;
            text-decoration: none;
            font-weight: 500;
        }
        .footer-text a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">
            <?php if (!empty($schoolLogo)): ?>
                <img src="<?= htmlspecialchars($schoolLogo) ?>" alt="Logo">
            <?php else: ?>
                <div class="icon">🎓</div>
            <?php endif; ?>
        </div>

        <h1><?= htmlspecialchars($title ?? 'Login') ?></h1>
        <p class="subtitle">
            <?php if (!empty($isSchool)): ?>
                <?= htmlspecialchars($schoolName ?? 'School') ?> Portal
            <?php else: ?>
                <?= APP_NAME ?>
            <?php endif; ?>
        </p>

        <?php if ($flashMsg): ?>
            <div class="flash <?= htmlspecialchars($flashType ?? 'error') ?>">
                <?= htmlspecialchars($flashMsg) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= $formAction ?>">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="you@example.com" value="<?= old('email') ?>" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-login">Sign In</button>
        </form>

        <p class="footer-text">
            <?php if (empty($isSchool)): ?>
                Need to register a school? <a href="<?= url('register') ?>">Register here</a>
            <?php else: ?>
                <a href="<?= url($slug . '/login') ?>">← Back</a>
            <?php endif; ?>
        </p>
    </div>
</body>
</html>
