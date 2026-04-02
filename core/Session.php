<?php
/**
 * Session – Secure session management
 */
class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_samesite', 'Strict');
            ini_set('session.use_strict_mode', '1');
            ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
            session_name(SESSION_NAME);
            session_start();
        }
    }

    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        session_unset();
        session_destroy();
    }

    /**
     * Flash message: set for next request, then clear.
     */
    public static function flash(string $key, $value = null)
    {
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
            return null;
        }
        $val = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $val;
    }

    /**
     * Store old form input for repopulation.
     */
    public static function setOldInput(array $data): void
    {
        $_SESSION['_old_input'] = $data;
    }

    public static function getOldInput(string $key, $default = '')
    {
        $val = $_SESSION['_old_input'][$key] ?? $default;
        return $val;
    }

    public static function clearOldInput(): void
    {
        unset($_SESSION['_old_input']);
    }
}
