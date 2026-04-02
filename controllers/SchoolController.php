<?php
/**
 * SchoolController – Platform admin: Manage schools
 */
class SchoolController extends Controller
{
    public function index(): void
    {
        $schools = School::all();
        $this->view('schools.index', [
            'title'   => 'All Schools',
            'schools' => $schools,
            'layout'  => 'platform',
        ], 'platform');
    }

    public function create(): void
    {
        $this->view('schools.create', [
            'title' => 'Create New School',
        ], 'platform');
    }

    public function store(): void
    {
        $name = $this->input('name');
        $slug = slugify($this->input('slug') ?: $name);

        $errors = [];
        if (empty($name)) $errors[] = 'School name is required.';
        if (School::slugExists($slug)) $errors[] = 'Slug already taken.';

        if (!empty($errors)) {
            flash('error', implode('<br>', $errors));
            $this->saveInput();
            redirect('schools/create');
        }

        $schoolId = School::create([
            'name'     => $name,
            'slug'     => $slug,
            'address'  => $this->input('address'),
            'phone'    => $this->input('phone'),
            'principal_name' => $this->input('principal_name'),
            'currency' => $this->input('currency') ?: DEFAULT_CURRENCY,
            'status'   => 'active',
        ]);

        // Optionally create a super_admin for this school
        $adminEmail = $this->input('admin_email');
        $adminPass  = $this->input('admin_password');
        if ($adminEmail && $adminPass) {
            if (!User::emailExists($adminEmail)) {
                User::create([
                    'school_id'     => $schoolId,
                    'name'          => $this->input('admin_name') ?: 'School Admin',
                    'email'         => $adminEmail,
                    'password_hash' => password_hash($adminPass, PASSWORD_BCRYPT),
                    'role'          => 'super_admin',
                    'status'        => 'active',
                ]);
            }
        }

        $this->audit('create', 'school', $schoolId, ['name' => $name, 'slug' => $slug]);
        flash('success', 'School created successfully!');
        redirect('schools');
    }

    public function edit(string $id): void
    {
        $school = School::find((int) $id);
        if (!$school) {
            flash('error', 'School not found.');
            redirect('schools');
        }

        $this->view('schools.edit', [
            'title'  => 'Edit School',
            'school' => $school,
        ], 'platform');
    }

    public function update(string $id): void
    {
        $school = School::find((int) $id);
        if (!$school) {
            flash('error', 'School not found.');
            redirect('schools');
        }

        $name = $this->input('name');
        $slug = slugify($this->input('slug') ?: $name);

        if (School::slugExists($slug, (int) $id)) {
            flash('error', 'Slug already taken.');
            $this->saveInput();
            redirect("schools/{$id}/edit");
        }

        // Handle logo upload
        $logoUrl = $school['logo_url'];
        $uploaded = $this->uploadFile('logo', "schools/{$id}");
        if ($uploaded) {
            $logoUrl = $uploaded;
        }

        School::update((int) $id, [
            'name'           => $name,
            'slug'           => $slug,
            'address'        => $this->input('address'),
            'phone'          => $this->input('phone'),
            'principal_name' => $this->input('principal_name'),
            'currency'       => $this->input('currency') ?: DEFAULT_CURRENCY,
            'academic_session' => $this->input('academic_session'),
            'logo_url'       => $logoUrl,
        ]);

        $this->audit('update', 'school', (int) $id, ['name' => $name]);
        flash('success', 'School updated successfully!');
        redirect('schools');
    }

    public function toggle(string $id): void
    {
        $school = School::find((int) $id);
        if (!$school) {
            flash('error', 'School not found.');
            redirect('schools');
        }

        $newStatus = $school['status'] === 'active' ? 'inactive' : 'active';
        School::update((int) $id, ['status' => $newStatus]);

        $this->audit('update', 'school', (int) $id, ['status' => $newStatus]);
        flash('success', "School {$newStatus}d successfully!");
        redirect('schools');
    }
}
