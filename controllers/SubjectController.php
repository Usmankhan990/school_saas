<?php
/**
 * SubjectController – CRUD for subjects
 */
class SubjectController extends Controller
{
    public function index(): void
    {
        $schoolId = current_school_id();
        $subjects = Subject::allForSchool($schoolId);
        $this->view('subjects.index', [
            'title'    => 'Subjects',
            'subjects' => $subjects,
        ]);
    }

    public function create(): void
    {
        $this->view('subjects.create', [
            'title' => 'Add Subject',
        ]);
    }

    public function store(): void
    {
        $schoolId = current_school_id();
        $name = $this->input('name');
        $code = $this->input('code');

        if (!$name) {
            flash('error', 'Subject name is required.');
            $this->saveInput();
            redirect(tenant_slug() . '/subjects/create');
        }

        Subject::create([
            'school_id' => $schoolId,
            'name'      => $name,
            'code'      => $code,
        ]);

        $this->audit('create', 'subject');
        flash('success', 'Subject created successfully.');
        redirect(tenant_slug() . '/subjects');
    }

    public function edit($id): void
    {
        $id = (int) $id;
        $schoolId = current_school_id();
        $subject = Subject::findSecure($id, $schoolId);
        if (!$subject) {
            flash('error', 'Subject not found.');
            redirect(tenant_slug() . '/subjects');
        }

        $this->view('subjects.edit', [
            'title'   => 'Edit Subject',
            'subject' => $subject,
        ]);
    }

    public function update($id): void
    {
        $id = (int) $id;
        $schoolId = current_school_id();
        $subject = Subject::findSecure($id, $schoolId);
        if (!$subject) {
            flash('error', 'Subject not found.');
            redirect(tenant_slug() . '/subjects');
        }

        $name = $this->input('name');
        $code = $this->input('code');

        if (!$name) {
            flash('error', 'Subject name is required.');
            $this->saveInput();
            redirect(tenant_slug() . "/subjects/{$id}/edit");
        }

        Subject::update($id, ['name' => $name, 'code' => $code]);
        $this->audit('update', 'subject', $id);
        flash('success', 'Subject updated successfully.');
        redirect(tenant_slug() . '/subjects');
    }

    public function delete($id): void
    {
        $id = (int) $id;
        $schoolId = current_school_id();
        Subject::delete($id, $schoolId);
        $this->audit('delete', 'subject', $id);
        flash('success', 'Subject deleted.');
        redirect(tenant_slug() . '/subjects');
    }
}
