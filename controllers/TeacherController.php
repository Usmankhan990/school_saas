<?php
/**
 * TeacherController – Teacher management
 */
class TeacherController extends Controller
{
    public function index(): void
    {
        $this->authorize(['super_admin', 'admin', 'staff']);
        $teachers = Teacher::allForSchool(current_school_id());

        $this->view('teachers.index', [
            'title'    => 'Teachers',
            'teachers' => $teachers,
        ]);
    }

    public function create(): void
    {
        $this->authorize(['super_admin', 'admin']);
        $this->view('teachers.create', [
            'title' => 'Add New Teacher',
        ]);
    }

    public function store(): void
    {
        $this->authorize(['super_admin', 'admin']);

        $name = $this->input('name');
        if (empty($name)) {
            flash('error', 'Teacher name is required.');
            $this->saveInput();
            redirect(tenant_slug() . '/teachers/create');
        }

        // Optionally create a user login
        $userId = null;
        $email = $this->input('email');
        $password = $this->input('password');
        if ($email && $password) {
            if (User::emailExists($email)) {
                flash('error', 'Email already in use.');
                $this->saveInput();
                redirect(tenant_slug() . '/teachers/create');
            }
            $userId = User::create([
                'school_id'     => current_school_id(),
                'name'          => $name,
                'email'         => $email,
                'password_hash' => password_hash($password, PASSWORD_BCRYPT),
                'role'          => 'teacher',
                'status'        => 'active',
            ]);
        }

        $teacherId = Teacher::create([
            'school_id'   => current_school_id(),
            'name'        => $name,
            'phone'       => $this->input('phone'),
            'designation' => $this->input('designation'),
            'user_id'     => $userId,
            'status'      => 'active',
        ]);

        $this->audit('create', 'teacher', $teacherId, ['name' => $name]);
        flash('success', 'Teacher added successfully!');
        redirect(tenant_slug() . '/teachers');
    }

    public function edit(string $id): void
    {
        $this->authorize(['super_admin', 'admin']);
        $teacher = Teacher::findSecure((int) $id, current_school_id());

        if (!$teacher) {
            flash('error', 'Teacher not found.');
            redirect(tenant_slug() . '/teachers');
        }

        $this->view('teachers.edit', [
            'title'   => 'Edit Teacher',
            'teacher' => $teacher,
        ]);
    }

    public function update(string $id): void
    {
        $this->authorize(['super_admin', 'admin']);
        $teacher = Teacher::findSecure((int) $id, current_school_id());

        if (!$teacher) {
            flash('error', 'Teacher not found.');
            redirect(tenant_slug() . '/teachers');
        }

        Teacher::update((int) $id, [
            'name'        => $this->input('name'),
            'phone'       => $this->input('phone'),
            'designation' => $this->input('designation'),
        ]);

        // Update linked user email if exists
        if ($teacher['user_id']) {
            $email = $this->input('email');
            if ($email) {
                if (!User::emailExists($email, $teacher['user_id'])) {
                    User::update($teacher['user_id'], ['email' => $email, 'name' => $this->input('name')]);
                }
            }
        }

        $this->audit('update', 'teacher', (int) $id, ['name' => $this->input('name')]);
        flash('success', 'Teacher updated successfully!');
        redirect(tenant_slug() . '/teachers');
    }

    public function delete(string $id): void
    {
        $this->authorize(['super_admin', 'admin']);
        $teacher = Teacher::findSecure((int) $id, current_school_id());

        if (!$teacher) {
            flash('error', 'Teacher not found.');
            redirect(tenant_slug() . '/teachers');
        }

        // Delete linked user if exists
        if ($teacher['user_id']) {
            Database::delete('users', 'id = ?', [$teacher['user_id']]);
        }

        Teacher::delete((int) $id, current_school_id());
        $this->audit('delete', 'teacher', (int) $id, ['name' => $teacher['name']]);
        flash('success', 'Teacher deleted successfully!');
        redirect(tenant_slug() . '/teachers');
    }
}
