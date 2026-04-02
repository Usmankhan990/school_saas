<?php
/**
 * ClassController – Manage classes
 */
class ClassController extends Controller
{
    public function index(): void
    {
        $this->authorize(['super_admin', 'admin', 'staff']);
        $classes = ClassModel::allForSchool(current_school_id());
        $sections = SectionModel::allForSchool(current_school_id());

        $this->view('classes.index', [
            'title'    => 'Classes & Sections',
            'classes'  => $classes,
            'sections' => $sections,
        ]);
    }

    public function create(): void
    {
        $this->authorize(['super_admin', 'admin']);
        $this->view('classes.create', [
            'title' => 'Add New Class',
        ]);
    }

    public function store(): void
    {
        $this->authorize(['super_admin', 'admin']);

        $name = $this->input('name');
        if (empty($name)) {
            flash('error', 'Class name is required.');
            $this->saveInput();
            redirect(tenant_slug() . '/classes/create');
        }

        $classId = ClassModel::create([
            'school_id'    => current_school_id(),
            'name'         => $name,
            'numeric_name' => $this->input('numeric_name') ?: null,
        ]);

        // Auto-create sections if provided
        $sectionNames = $this->input('sections');
        if ($sectionNames) {
            $parts = array_map('trim', explode(',', $sectionNames));
            foreach ($parts as $sName) {
                if ($sName) {
                    SectionModel::create([
                        'school_id' => current_school_id(),
                        'class_id'  => $classId,
                        'name'      => $sName,
                    ]);
                }
            }
        }

        $this->audit('create', 'class', $classId, ['name' => $name]);
        flash('success', 'Class created successfully!');
        redirect(tenant_slug() . '/classes');
    }

    public function edit(string $id): void
    {
        $this->authorize(['super_admin', 'admin']);
        $class = ClassModel::findSecure((int) $id, current_school_id());

        if (!$class) {
            flash('error', 'Class not found.');
            redirect(tenant_slug() . '/classes');
        }

        $sections = SectionModel::allForClass((int) $id, current_school_id());

        $this->view('classes.edit', [
            'title'    => 'Edit Class',
            'class'    => $class,
            'sections' => $sections,
        ]);
    }

    public function update(string $id): void
    {
        $this->authorize(['super_admin', 'admin']);
        $class = ClassModel::findSecure((int) $id, current_school_id());

        if (!$class) {
            flash('error', 'Class not found.');
            redirect(tenant_slug() . '/classes');
        }

        ClassModel::update((int) $id, [
            'name'         => $this->input('name'),
            'numeric_name' => $this->input('numeric_name') ?: null,
        ]);

        $this->audit('update', 'class', (int) $id, ['name' => $this->input('name')]);
        flash('success', 'Class updated successfully!');
        redirect(tenant_slug() . '/classes');
    }

    public function delete(string $id): void
    {
        $this->authorize(['super_admin', 'admin']);
        $class = ClassModel::findSecure((int) $id, current_school_id());

        if (!$class) {
            flash('error', 'Class not found.');
            redirect(tenant_slug() . '/classes');
        }

        ClassModel::delete((int) $id, current_school_id());
        $this->audit('delete', 'class', (int) $id, ['name' => $class['name']]);
        flash('success', 'Class deleted successfully!');
        redirect(tenant_slug() . '/classes');
    }
}
