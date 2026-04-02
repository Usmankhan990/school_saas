<?php
/**
 * StaffController – Staff management
 */
class StaffController extends Controller
{
    public function index(): void
    {
        $this->authorize(['super_admin', 'admin']);
        $staff = Staff::allForSchool(current_school_id());

        $this->view('staff.index', [
            'title' => 'Staff Members',
            'staff' => $staff,
        ]);
    }

    public function create(): void
    {
        $this->authorize(['super_admin', 'admin']);
        $this->view('staff.create', [
            'title' => 'Add New Staff',
        ]);
    }

    public function store(): void
    {
        $this->authorize(['super_admin', 'admin']);

        $name = $this->input('name');
        if (empty($name)) {
            flash('error', 'Staff name is required.');
            $this->saveInput();
            redirect(tenant_slug() . '/staff/create');
        }

        // Optionally create a user login
        $userId = null;
        $email = $this->input('email');
        $password = $this->input('password');
        if ($email && $password) {
            if (User::emailExists($email)) {
                flash('error', 'Email already in use.');
                $this->saveInput();
                redirect(tenant_slug() . '/staff/create');
            }
            $userId = User::create([
                'school_id'     => current_school_id(),
                'name'          => $name,
                'email'         => $email,
                'password_hash' => password_hash($password, PASSWORD_BCRYPT),
                'role'          => 'staff',
                'status'        => 'active',
            ]);
        }

        $staffId = Staff::create([
            'school_id'   => current_school_id(),
            'name'        => $name,
            'phone'       => $this->input('phone'),
            'designation' => $this->input('designation'),
            'user_id'     => $userId,
            'status'      => 'active',
        ]);

        $this->audit('create', 'staff', $staffId, ['name' => $name]);
        flash('success', 'Staff member added successfully!');
        redirect(tenant_slug() . '/staff');
    }

    public function edit(string $id): void
    {
        $this->authorize(['super_admin', 'admin']);
        $member = Staff::findSecure((int) $id, current_school_id());

        if (!$member) {
            flash('error', 'Staff member not found.');
            redirect(tenant_slug() . '/staff');
        }

        $this->view('staff.edit', [
            'title'  => 'Edit Staff',
            'member' => $member,
        ]);
    }

    public function update(string $id): void
    {
        $this->authorize(['super_admin', 'admin']);
        $member = Staff::findSecure((int) $id, current_school_id());

        if (!$member) {
            flash('error', 'Staff member not found.');
            redirect(tenant_slug() . '/staff');
        }

        Staff::update((int) $id, [
            'name'        => $this->input('name'),
            'phone'       => $this->input('phone'),
            'designation' => $this->input('designation'),
        ]);

        if ($member['user_id']) {
            $email = $this->input('email');
            if ($email && !User::emailExists($email, $member['user_id'])) {
                User::update($member['user_id'], ['email' => $email, 'name' => $this->input('name')]);
            }
        }

        $this->audit('update', 'staff', (int) $id, ['name' => $this->input('name')]);
        flash('success', 'Staff member updated successfully!');
        redirect(tenant_slug() . '/staff');
    }

    public function delete(string $id): void
    {
        $this->authorize(['super_admin', 'admin']);
        $member = Staff::findSecure((int) $id, current_school_id());

        if (!$member) {
            flash('error', 'Staff member not found.');
            redirect(tenant_slug() . '/staff');
        }

        if ($member['user_id']) {
            Database::delete('users', 'id = ?', [$member['user_id']]);
        }

        Staff::delete((int) $id, current_school_id());
        $this->audit('delete', 'staff', (int) $id, ['name' => $member['name']]);
        flash('success', 'Staff member deleted successfully!');
        redirect(tenant_slug() . '/staff');
    }
}
