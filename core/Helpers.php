<?php
/**
 * Helper Functions
 */

/**
 * Generate CSRF token and store in session.
 */
function csrf_token(): string
{
    if (!Session::has(CSRF_TOKEN_NAME)) {
        Session::set(CSRF_TOKEN_NAME, bin2hex(random_bytes(32)));
    }
    return Session::get(CSRF_TOKEN_NAME);
}

/**
 * Output a hidden CSRF input field.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . htmlspecialchars(csrf_token()) . '">';
}

/**
 * Validate CSRF token from POST request.
 */
function csrf_validate(): bool
{
    $token = $_POST[CSRF_TOKEN_NAME] ?? '';
    $sessionToken = Session::get(CSRF_TOKEN_NAME, '');
    if ($token === '' || $sessionToken === '') return false;
    $valid = hash_equals($sessionToken, $token);
    // Regenerate token after validation
    Session::set(CSRF_TOKEN_NAME, bin2hex(random_bytes(32)));
    return $valid;
}

/**
 * Get old form input value.
 */
function old(string $key, string $default = ''): string
{
    return htmlspecialchars(Session::getOldInput($key, $default));
}

/**
 * Sanitize a string.
 */
function sanitize(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to a URL.
 */
function redirect(string $path): void
{
    header('Location: ' . APP_URL . '/' . ltrim($path, '/'));
    exit;
}

/**
 * Generate full URL for a path.
 */
function url(string $path = ''): string
{
    return APP_URL . '/' . ltrim($path, '/');
}

/**
 * Generate tenant-scoped URL.
 */
function tenant_url(string $path = ''): string
{
    $slug = Session::get('tenant_slug', '');
    if ($slug) {
        return APP_URL . '/' . $slug . '/' . ltrim($path, '/');
    }
    return url($path);
}

/**
 * Format date.
 */
function format_date(?string $date, string $format = 'd M Y'): string
{
    if (!$date) return '—';
    return date($format, strtotime($date));
}

/**
 * Check if current user is logged in.
 */
function is_logged_in(): bool
{
    return Session::has('user_id');
}

/**
 * Get current user's role.
 */
function current_role(): ?string
{
    return Session::get('user_role');
}

/**
 * Get current school ID from session.
 */
function current_school_id(): ?int
{
    return Session::get('tenant_school_id');
}

/**
 * Get current user ID.
 */
function current_user_id(): ?int
{
    return Session::get('user_id');
}

/**
 * Check if current user has one of specified roles.
 */
function has_role(array $roles): bool
{
    return in_array(current_role(), $roles, true);
}

/**
 * Generate a URL-friendly slug from text.
 */
function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9-]/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

/**
 * Set flash message.
 */
function flash(string $type, string $message): void
{
    Session::flash('flash_type', $type);
    Session::flash('flash_message', $message);
}

/**
 * Dump and die (debug helper).
 */
function dd($data): void
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    die();
}

/**
 * Get the current tenant slug.
 */
function tenant_slug(): string
{
    return Session::get('tenant_slug', '');
}

/**
 * Asset URL helper.
 */
function asset(string $path): string
{
    return APP_URL . '/public/' . ltrim($path, '/');
}
