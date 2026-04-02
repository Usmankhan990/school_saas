<?php
/**
 * RegisterController – Public school registration
 */
class RegisterController extends Controller
{
    /**
     * Show registration form.
     */
    public function showForm(): void
    {
        $this->viewOnly('auth.register', [
            'title' => 'Register Your School',
        ]);
    }

    /**
     * Handle school registration.
     */
    public function register(): void
    {
        $schoolName = $this->input('school_name');
        $slug       = $this->input('slug');
        $email      = $this->input('email');
        $password   = $this->input('password');
        $adminName  = $this->input('admin_name');
        $phone      = $this->input('phone');

        // Auto-generate slug if empty
        if (empty($slug)) {
            $slug = slugify($schoolName);
        } else {
            $slug = slugify($slug);
        }

        // Validate
        $errors = [];
        if (empty($schoolName)) $errors[] = 'School name is required.';
        if (empty($email))      $errors[] = 'Admin email is required.';
        if (empty($password))   $errors[] = 'Password is required.';
        if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
        if (empty($adminName))  $errors[] = 'Admin name is required.';

        if (School::slugExists($slug)) {
            $errors[] = 'This school URL slug is already taken. Try a different one.';
        }
        if (User::emailExists($email)) {
            $errors[] = 'This email is already registered.';
        }

        if (!empty($errors)) {
            flash('error', implode('<br>', $errors));
            $this->saveInput();
            redirect('register');
        }

        // Create school
        $schoolId = School::create([
            'name'   => $schoolName,
            'slug'   => $slug,
            'phone'  => $phone,
            'status' => 'active',
        ]);

        // Create super_admin user
        User::create([
            'school_id'     => $schoolId,
            'name'          => $adminName,
            'email'         => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
            'role'          => 'super_admin',
            'status'        => 'active',
        ]);

        flash('success', 'School registered successfully! Please login with your admin credentials.');
        redirect($slug . '/login');
    }
}
