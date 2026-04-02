<?php
/**
 * UserController – Manage school user accounts
 */
class UserController extends Controller
{
    public function index(): void
    {
        $this->authorize(['super_admin', 'admin']);
        $users = User::allForSchool(current_school_id());

        $this->view('users.index', [
            'title' => 'Manage Users',
            'users' => $users,
        ]);
    }

    public function create(): void
    {
        $this->authorize(['super_admin', 'admin']);
        $this->view('users.create', [
            'title' => 'Create User',
        ]);
    }

    public function store(): void
    {
        $this->authorize(['super_admin', 'admin']);

        $name     = $this->input('name');
        $email    = $this->input('email');
        $password = $this->input('password');
        $role     = $this->input('role');

        // Validate
        $errors = [];
        if (empty($name))     $errors[] = 'Name is required.';
        if (empty($email))    $errors[] = 'Email is required.';
        if (empty($password)) $errors[] = 'Password is required.';
        if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';

        $allowedRoles = ['admin', 'staff', 'teacher', 'student'];
        if (!in_array($role, $allowedRoles)) $errors[] = 'Invalid role selected.';

        if (User::emailExists($email)) {
            $errors[] = 'Email already in use.';
        }

        if (!empty($errors)) {
            flash('error', implode('<br>', $errors));
            $this->saveInput();
            redirect(tenant_slug() . '/users/create');
        }

        $userId = User::create([
            'school_id'     => current_school_id(),
            'name'          => $name,
            'email'         => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
            'role'          => $role,
            'status'        => 'active',
        ]);

        $this->audit('create', 'user', $userId, ['name' => $name, 'role' => $role]);
        flash('success', 'User created successfully!');
        redirect(tenant_slug() . '/users');
    }

    public function edit(string $id): void
    {
        $this->authorize(['super_admin', 'admin']);
        $user = User::find((int) $id);

        if (!$user || $user['school_id'] != current_school_id()) {
            flash('error', 'User not found.');
            redirect(tenant_slug() . '/users');
        }

        $this->view('users.edit', [
            'title' => 'Edit User',
            'user'  => $user,
        ]);
    }

    public function update(string $id): void
    {
        $this->authorize(['super_admin', 'admin']);
        $user = User::find((int) $id);

        if (!$user || $user['school_id'] != current_school_id()) {
            flash('error', 'User not found.');
            redirect(tenant_slug() . '/users');
        }

        $name  = $this->input('name');
        $email = $this->input('email');
        $role  = $this->input('role');

        if (User::emailExists($email, (int) $id)) {
            flash('error', 'Email already in use.');
            $this->saveInput();
            redirect(tenant_slug() . "/users/{$id}/edit");
        }

        $data = [
            'name'  => $name,
            'email' => $email,
            'role'  => $role,
        ];

        // Update password only if provided
        $password = $this->input('password');
        if (!empty($password)) {
            $data['password_hash'] = password_hash($password, PASSWORD_BCRYPT);
        }

        User::update((int) $id, $data);
        $this->audit('update', 'user', (int) $id, ['name' => $name, 'role' => $role]);
        flash('success', 'User updated successfully!');
        redirect(tenant_slug() . '/users');
    }

    public function toggle(string $id): void
    {
        $this->authorize(['super_admin', 'admin']);
        $user = User::find((int) $id);

        if (!$user || $user['school_id'] != current_school_id()) {
            flash('error', 'User not found.');
            redirect(tenant_slug() . '/users');
        }

        $newStatus = $user['status'] === 'active' ? 'inactive' : 'active';
        User::update((int) $id, ['status' => $newStatus]);
        $this->audit('update', 'user', (int) $id, ['status' => $newStatus]);
        flash('success', "User {$newStatus}d successfully!");
        redirect(tenant_slug() . '/users');
    }

    public function resetPassword(string $id): void
    {
        $this->authorize(['super_admin', 'admin']);
        $user = User::find((int) $id);

        if (!$user || $user['school_id'] != current_school_id()) {
            flash('error', 'User not found.');
            redirect(tenant_slug() . '/users');
        }

        $newPassword = $this->input('new_password');
        if (empty($newPassword) || strlen($newPassword) < 6) {
            flash('error', 'Password must be at least 6 characters.');
            redirect(tenant_slug() . "/users/{$id}/edit");
        }

        User::update((int) $id, [
            'password_hash' => password_hash($newPassword, PASSWORD_BCRYPT),
        ]);

        $this->audit('update', 'user', (int) $id, ['action' => 'password_reset']);
        flash('success', 'Password reset successfully!');
        redirect(tenant_slug() . '/users');
    }
}
