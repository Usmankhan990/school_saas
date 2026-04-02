<?php
/**
 * Middleware - Auth, RBAC, CSRF, Tenant checks
 */
class Middleware
{
    /**
     * Run a named middleware.
     */
    public static function run(string $name): void
    {
        if ($name === 'auth') {
            self::authCheck();
        } elseif ($name === 'guest') {
            self::guestCheck();
        } elseif ($name === 'csrf') {
            self::csrfCheck();
        } elseif ($name === 'platform_admin') {
            self::platformAdminCheck();
        } elseif ($name === 'school_user') {
            self::schoolUserCheck();
        } elseif ($name === 'tenant') {
            self::tenantCheck();
        }
    }

    /**
     * Must be logged in.
     */
    public static function authCheck(): void
    {
        if (!is_logged_in()) {
            flash('error', 'Please login to continue.');
            redirect('login');
        }
    }

    /**
     * Must NOT be logged in (for login/register pages).
     */
    public static function guestCheck(): void
    {
        if (is_logged_in()) {
            if (current_role() === 'platform_admin') {
                redirect('schools');
            } else {
                redirect(tenant_slug() . '/dashboard');
            }
        }
    }

    /**
     * CSRF token validation for POST requests.
     */
    public static function csrfCheck(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_validate()) {
                flash('error', 'Invalid security token. Please try again.');
                header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? APP_URL));
                exit;
            }
        }
    }

    /**
     * Must be platform_admin.
     */
    public static function platformAdminCheck(): void
    {
        self::authCheck();
        if (current_role() !== 'platform_admin') {
            http_response_code(403);
            flash('error', 'Access denied. Platform admin only.');
            redirect('login');
        }
    }

    /**
     * Must be a school-scoped user (not platform_admin).
     */
    public static function schoolUserCheck(): void
    {
        self::authCheck();
        if (!current_school_id()) {
            flash('error', 'No school context found.');
            redirect('login');
        }
    }

    /**
     * Tenant must be resolved (school slug valid).
     */
    public static function tenantCheck(): void
    {
        if (!Session::has('tenant_school_id')) {
            http_response_code(404);
            echo 'School not found.';
            exit;
        }
    }

    /**
     * Role-based access: user must have one of the specified roles.
     */
    public static function roleCheck(array $allowedRoles): void
    {
        self::authCheck();
        if (!has_role($allowedRoles)) {
            http_response_code(403);
            flash('error', 'You do not have permission to access this page.');
            if (current_role() === 'platform_admin') {
                redirect('schools');
            } else {
                redirect(tenant_slug() . '/dashboard');
            }
        }
    }
}
