<?php
/**
 * StudentController – Student admission & management
 */
class StudentController extends Controller
{
    public function index(): void
    {
        $this->authorize(['super_admin', 'admin', 'staff', 'teacher']);
        $students = Student::allForSchool(current_school_id());
        $classes = ClassModel::allForSchool(current_school_id());

        $this->view('students.index', [
            'title'    => 'Students',
            'students' => $students,
            'classes'  => $classes,
        ]);
    }

    public function create(): void
    {
        $this->authorize(['super_admin', 'admin', 'staff']);
        $classes  = ClassModel::allForSchool(current_school_id());
        $sections = SectionModel::allForSchool(current_school_id());
        $nextAdmNo = Student::generateAdmissionNo(current_school_id());

        $this->view('students.create', [
            'title'     => 'Admit New Student',
            'classes'   => $classes,
            'sections'  => $sections,
            'nextAdmNo' => $nextAdmNo,
        ]);
    }

    public function store(): void
    {
        $this->authorize(['super_admin', 'admin', 'staff']);

        $admissionNo = $this->input('admission_no');
        $name        = $this->input('name');

        // Validate
        $errors = [];
        if (empty($name))        $errors[] = 'Student name is required.';
        if (empty($admissionNo)) $errors[] = 'Admission number is required.';

        if (Student::admissionNoExists(current_school_id(), $admissionNo)) {
            $errors[] = 'Admission number already exists.';
        }

        if (!empty($errors)) {
            flash('error', implode('<br>', $errors));
            $this->saveInput();
            redirect(tenant_slug() . '/students/create');
        }

        $studentId = Student::create([
            'school_id'    => current_school_id(),
            'admission_no' => $admissionNo,
            'name'         => $name,
            'father_name'  => $this->input('father_name'),
            'phone'        => $this->input('phone'),
            'address'      => $this->input('address'),
            'class_id'     => $this->input('class_id') ?: null,
            'section_id'   => $this->input('section_id') ?: null,
            'dob'          => $this->input('dob') ?: null,
            'gender'       => $this->input('gender') ?: null,
            'status'       => 'active',
        ]);

        $this->audit('create', 'student', $studentId, ['name' => $name, 'admission_no' => $admissionNo]);
        flash('success', 'Student admitted successfully!');
        redirect(tenant_slug() . '/students');
    }

    public function edit(string $id): void
    {
        $this->authorize(['super_admin', 'admin', 'staff']);
        $student = Student::findSecure((int) $id, current_school_id());

        if (!$student) {
            flash('error', 'Student not found.');
            redirect(tenant_slug() . '/students');
        }

        $classes  = ClassModel::allForSchool(current_school_id());
        $sections = SectionModel::allForSchool(current_school_id());

        $this->view('students.edit', [
            'title'    => 'Edit Student',
            'student'  => $student,
            'classes'  => $classes,
            'sections' => $sections,
        ]);
    }

    public function update(string $id): void
    {
        $this->authorize(['super_admin', 'admin', 'staff']);
        $student = Student::findSecure((int) $id, current_school_id());

        if (!$student) {
            flash('error', 'Student not found.');
            redirect(tenant_slug() . '/students');
        }

        $admissionNo = $this->input('admission_no');
        if (Student::admissionNoExists(current_school_id(), $admissionNo, (int) $id)) {
            flash('error', 'Admission number already exists.');
            $this->saveInput();
            redirect(tenant_slug() . "/students/{$id}/edit");
        }

        Student::update((int) $id, [
            'admission_no' => $admissionNo,
            'name'         => $this->input('name'),
            'father_name'  => $this->input('father_name'),
            'phone'        => $this->input('phone'),
            'address'      => $this->input('address'),
            'class_id'     => $this->input('class_id') ?: null,
            'section_id'   => $this->input('section_id') ?: null,
            'dob'          => $this->input('dob') ?: null,
            'gender'       => $this->input('gender') ?: null,
            'status'       => $this->input('status') ?: 'active',
        ]);

        $this->audit('update', 'student', (int) $id, ['name' => $this->input('name')]);
        flash('success', 'Student updated successfully!');
        redirect(tenant_slug() . '/students');
    }

    public function delete(string $id): void
    {
        $this->authorize(['super_admin', 'admin']);
        $student = Student::findSecure((int) $id, current_school_id());

        if (!$student) {
            flash('error', 'Student not found.');
            redirect(tenant_slug() . '/students');
        }

        Student::delete((int) $id, current_school_id());
        $this->audit('delete', 'student', (int) $id, ['name' => $student['name']]);
        flash('success', 'Student deleted successfully!');
        redirect(tenant_slug() . '/students');
    }
}
