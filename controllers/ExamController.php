<?php
/**
 * ExamController – CRUD for exams
 */
class ExamController extends Controller
{
    public function index(): void
    {
        $schoolId = current_school_id();
        $exams = Exam::allForSchool($schoolId);
        $this->view('exams.index', [
            'title' => 'Exams',
            'exams' => $exams,
        ]);
    }

    public function create(): void
    {
        $schoolId = current_school_id();
        $classes = ClassModel::allForSchool($schoolId);
        $this->view('exams.create', [
            'title'   => 'Create Exam',
            'classes' => $classes,
        ]);
    }

    public function store(): void
    {
        $schoolId = current_school_id();
        $name      = $this->input('name');
        $type      = $this->input('type');
        $classId   = (int) $this->input('class_id');
        $startDate = $this->input('start_date');
        $endDate   = $this->input('end_date');

        if (!$name) {
            flash('error', 'Exam name is required.');
            $this->saveInput();
            redirect(tenant_slug() . '/exams/create');
        }

        $examId = Exam::create([
            'school_id'  => $schoolId,
            'name'       => $name,
            'type'       => $type ?: 'term',
            'class_id'   => $classId ?: null,
            'start_date' => $startDate ?: null,
            'end_date'   => $endDate ?: null,
        ]);

        // Notify school about new exam
        try {
            Notification::notifySchool($schoolId, 'New Exam: ' . $name, "Exam '{$name}' has been scheduled.", 'exam');
        } catch (Exception $e) {}

        $this->audit('create', 'exam', $examId);
        flash('success', 'Exam created successfully.');
        redirect(tenant_slug() . '/exams');
    }

    public function edit($id): void
    {
        $id = (int) $id;
        $schoolId = current_school_id();
        $exam = Exam::findSecure($id, $schoolId);
        if (!$exam) {
            flash('error', 'Exam not found.');
            redirect(tenant_slug() . '/exams');
        }
        $classes = ClassModel::allForSchool($schoolId);
        $this->view('exams.edit', [
            'title'   => 'Edit Exam',
            'exam'    => $exam,
            'classes' => $classes,
        ]);
    }

    public function update($id): void
    {
        $id = (int) $id;
        $schoolId = current_school_id();
        $exam = Exam::findSecure($id, $schoolId);
        if (!$exam) {
            flash('error', 'Exam not found.');
            redirect(tenant_slug() . '/exams');
        }

        $name      = $this->input('name');
        $type      = $this->input('type');
        $classId   = (int) $this->input('class_id');
        $startDate = $this->input('start_date');
        $endDate   = $this->input('end_date');

        Exam::update($id, [
            'name'       => $name,
            'type'       => $type ?: 'term',
            'class_id'   => $classId ?: null,
            'start_date' => $startDate ?: null,
            'end_date'   => $endDate ?: null,
        ]);

        $this->audit('update', 'exam', $id);
        flash('success', 'Exam updated successfully.');
        redirect(tenant_slug() . '/exams');
    }

    public function delete($id): void
    {
        $id = (int) $id;
        $schoolId = current_school_id();
        Exam::delete($id, $schoolId);
        $this->audit('delete', 'exam', $id);
        flash('success', 'Exam deleted.');
        redirect(tenant_slug() . '/exams');
    }
}
