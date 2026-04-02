<?php
/**
 * School Management System (SaaS) - Configuration
 * Reads values from .env file. Update .env for your environment.
 */

// ─── Load .env ───────────────────────────────────────────
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (strpos($line, '=') === false) continue;
        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value);
        $_ENV[$key] = $value;
        putenv("{$key}={$value}");
    }
}

/**
 * Helper to read env with a default fallback.
 */
function env(string $key, $default = '')
{
    return $_ENV[$key] ?? getenv($key) ?: $default;
}

// ─── Database ────────────────────────────────────────────
define('DB_HOST',    env('DB_HOST', 'localhost'));
define('DB_NAME',    env('DB_NAME', 'school_saas'));
define('DB_USER',    env('DB_USER', 'root'));
define('DB_PASS',    env('DB_PASS', ''));
define('DB_CHARSET', env('DB_CHARSET', 'utf8mb4'));

// ─── Application ─────────────────────────────────────────
define('APP_NAME', env('APP_NAME', 'School Management System'));
define('APP_URL',  env('APP_URL', 'http://localhost:8000'));
define('APP_ENV',  env('APP_ENV', 'development'));

// ─── Paths ───────────────────────────────────────────────
define('BASE_PATH',    __DIR__);
define('UPLOAD_PATH',  BASE_PATH . '/public/uploads');
define('VIEWS_PATH',   BASE_PATH . '/views');

// ─── Session ─────────────────────────────────────────────
define('SESSION_NAME',     env('SESSION_NAME', 'school_saas_sess'));
define('SESSION_LIFETIME', (int) env('SESSION_LIFETIME', 7200));

// ─── Security ────────────────────────────────────────────
define('CSRF_TOKEN_NAME', '_csrf_token');
define('MAX_LOGIN_ATTEMPTS', (int) env('MAX_LOGIN_ATTEMPTS', 5));
define('LOGIN_LOCKOUT_TIME', (int) env('LOGIN_LOCKOUT_TIME', 900));

// ─── Uploads ─────────────────────────────────────────────
define('MAX_UPLOAD_SIZE', (int) env('MAX_UPLOAD_SIZE', 2 * 1024 * 1024));
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png']);

// ─── Default Currency ────────────────────────────────────
define('DEFAULT_CURRENCY', env('DEFAULT_CURRENCY', 'PKR'));
