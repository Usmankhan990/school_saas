<?php
/**
 * SectionController – Manage sections (sub-resource of classes)
 */
class SectionController extends Controller
{
    public function index(): void
    {
        $this->authorize(['super_admin', 'admin', 'staff']);
        redirect(tenant_slug() . '/classes');
    }

    public function store(): void
    {
        $this->authorize(['super_admin', 'admin']);

        $classId = $this->input('class_id');
        $name    = $this->input('name');

        if (empty($classId) || empty($name)) {
            flash('error', 'Class and section name are required.');
            redirect(tenant_slug() . '/classes');
        }

        // Verify class belongs to this school
        $class = ClassModel::findSecure((int) $classId, current_school_id());
        if (!$class) {
            flash('error', 'Class not found.');
            redirect(tenant_slug() . '/classes');
        }

        $sectionId = SectionModel::create([
            'school_id' => current_school_id(),
            'class_id'  => (int) $classId,
            'name'      => $name,
        ]);

        $this->audit('create', 'section', $sectionId, ['name' => $name, 'class' => $class['name']]);
        flash('success', 'Section added successfully!');
        redirect(tenant_slug() . '/classes');
    }

    public function update(string $id): void
    {
        $this->authorize(['super_admin', 'admin']);
        $section = SectionModel::findSecure((int) $id, current_school_id());

        if (!$section) {
            flash('error', 'Section not found.');
            redirect(tenant_slug() . '/classes');
        }

        SectionModel::update((int) $id, [
            'name' => $this->input('name'),
        ]);

        $this->audit('update', 'section', (int) $id, ['name' => $this->input('name')]);
        flash('success', 'Section updated successfully!');
        redirect(tenant_slug() . '/classes');
    }

    public function delete(string $id): void
    {
        $this->authorize(['super_admin', 'admin']);
        $section = SectionModel::findSecure((int) $id, current_school_id());

        if (!$section) {
            flash('error', 'Section not found.');
            redirect(tenant_slug() . '/classes');
        }

        SectionModel::delete((int) $id, current_school_id());
        $this->audit('delete', 'section', (int) $id);
        flash('success', 'Section deleted successfully!');
        redirect(tenant_slug() . '/classes');
    }
}
