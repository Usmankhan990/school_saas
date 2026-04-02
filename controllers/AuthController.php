<?php
/**
 * AuthController – Login/Logout for platform and school users
 */
class AuthController extends Controller
{
    /**
     * Show platform admin login page.
     */
    public function showLogin(): void
    {
        $this->viewOnly('auth.login', [
            'title'    => 'Platform Admin Login',
            'isSchool' => false,
        ]);
    }

    /**
     * Handle platform admin login.
     */
    public function login(): void
    {
        $email    = $this->input('email');
        $password = $this->input('password');

        // Rate limiting check
        if ($this->isRateLimited()) {
            flash('error', 'Too many login attempts. Please try again in 15 minutes.');
            $this->saveInput();
            redirect('login');
        }

        $user = User::findByEmail($email);

        if (!$user || $user['role'] !== 'platform_admin' || !password_verify($password, $user['password_hash'])) {
            $this->incrementLoginAttempts();
            flash('error', 'Invalid email or password.');
            $this->saveInput();
            redirect('login');
        }

        if ($user['status'] !== 'active') {
            flash('error', 'Your account is inactive.');
            redirect('login');
        }

        // Set session
        $this->setUserSession($user);
        $this->clearLoginAttempts();

        // Update last login
        User::update($user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);

        $this->audit('login', 'user', $user['id']);
        redirect('schools');
    }

    /**
     * Show school login page.
     */
    public function showSchoolLogin(): void
    {
        $this->viewOnly('auth.login', [
            'title'      => Session::get('tenant_name', 'School') . ' – Login',
            'isSchool'   => true,
            'schoolName' => Session::get('tenant_name', ''),
            'schoolLogo' => Session::get('tenant_logo_url', ''),
            'slug'       => Session::get('tenant_slug', ''),
        ]);
    }

    /**
     * Handle school user login.
     */
    public function schoolLogin(): void
    {
        $email    = $this->input('email');
        $password = $this->input('password');
        $slug     = Session::get('tenant_slug');
        $schoolId = Session::get('tenant_school_id');

        if ($this->isRateLimited()) {
            flash('error', 'Too many login attempts. Please try again in 15 minutes.');
            $this->saveInput();
            redirect($slug . '/login');
        }

        // Find user belonging to this school
        $user = User::findByEmailAndSchool($email, $schoolId);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->incrementLoginAttempts();
            flash('error', 'Invalid email or password.');
            $this->saveInput();
            redirect($slug . '/login');
        }

        if ($user['status'] !== 'active') {
            flash('error', 'Your account is inactive. Contact your administrator.');
            redirect($slug . '/login');
        }

        // Set session
        $this->setUserSession($user);
        $this->clearLoginAttempts();

        User::update($user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);
        $this->audit('login', 'user', $user['id']);

        redirect($slug . '/dashboard');
    }

    /**
     * Logout.
     */
    public function logout(): void
    {
        $slug = Session::get('tenant_slug');
        $this->audit('logout', 'user', current_user_id());
        Session::destroy();

        if ($slug) {
            redirect($slug . '/login');
        } else {
            redirect('login');
        }
    }

    // ── Rate Limiting Helpers ──

    private function isRateLimited(): bool
    {
        $attempts = Session::get('login_attempts', 0);
        $lastAttempt = Session::get('login_last_attempt', 0);

        if ($attempts >= MAX_LOGIN_ATTEMPTS) {
            if (time() - $lastAttempt < LOGIN_LOCKOUT_TIME) {
                return true;
            }
            $this->clearLoginAttempts();
        }
        return false;
    }

    private function incrementLoginAttempts(): void
    {
        $attempts = Session::get('login_attempts', 0);
        Session::set('login_attempts', $attempts + 1);
        Session::set('login_last_attempt', time());
    }

    private function clearLoginAttempts(): void
    {
        Session::remove('login_attempts');
        Session::remove('login_last_attempt');
    }

    private function setUserSession(array $user): void
    {
        Session::set('user_id', (int) $user['id']);
        Session::set('user_name', $user['name']);
        Session::set('user_email', $user['email']);
        Session::set('user_role', $user['role']);

        if ($user['school_id']) {
            Session::set('tenant_school_id', (int) $user['school_id']);
            // Load school data if not already set
            if (!Session::has('tenant_slug')) {
                $school = School::find($user['school_id']);
                if ($school) {
                    Session::set('tenant_slug', $school['slug']);
                    Session::set('tenant_name', $school['name']);
                    Session::set('tenant_logo_url', $school['logo_url']);
                }
            }
        }
    }
}
