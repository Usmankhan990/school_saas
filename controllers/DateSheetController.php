<?php
/**
 * DateSheetController – Exam schedule management per class
 */
class DateSheetController extends Controller
{
    public function index(): void
    {
        $schoolId = current_school_id();
        $exams   = Exam::allForSchool($schoolId);
        $classes = ClassModel::allForSchool($schoolId);

        // Get entries grouped by exam + class
        $entries = DateSheet::allForSchool($schoolId);

        $this->view('datesheets.index', [
            'title'   => 'Date Sheets',
            'exams'   => $exams,
            'classes' => $classes,
            'entries' => $entries,
        ]);
    }

    public function create(): void
    {
        $schoolId = current_school_id();
        $exams    = Exam::allForSchool($schoolId);
        $classes  = ClassModel::allForSchool($schoolId);
        $subjects = Subject::allForSchool($schoolId);
        $this->view('datesheets.create', [
            'title'    => 'Create Date Sheet',
            'exams'    => $exams,
            'classes'  => $classes,
            'subjects' => $subjects,
        ]);
    }

    public function store(): void
    {
        $schoolId  = current_school_id();
        $examId    = (int) $this->input('exam_id');
        $classId   = (int) $this->input('class_id');
        $subjectId = (int) $this->input('subject_id');
        $examDate  = $this->input('exam_date');
        $startTime = $this->input('start_time');
        $endTime   = $this->input('end_time');

        if (!$examId || !$classId || !$subjectId || !$examDate) {
            flash('error', 'All fields are required.');
            $this->saveInput();
            redirect(tenant_slug() . '/datesheets/create');
        }

        DateSheet::create([
            'school_id'  => $schoolId,
            'exam_id'    => $examId,
            'class_id'   => $classId,
            'subject_id' => $subjectId,
            'exam_date'  => $examDate,
            'start_time' => $startTime,
            'end_time'   => $endTime,
        ]);

        $this->audit('create', 'datesheet');
        flash('success', 'Date sheet entry added successfully.');
        redirect(tenant_slug() . '/datesheets');
    }

    /**
     * View date sheet for a specific exam — filter by class.
     */
    public function view_sheet($examId): void
    {
        $examId = (int) $examId;
        $schoolId = current_school_id();
        $exam = Exam::findSecure($examId, $schoolId);
        if (!$exam) {
            flash('error', 'Exam not found.');
            redirect(tenant_slug() . '/datesheets');
        }

        $classes = ClassModel::allForSchool($schoolId);
        $classId = (int) ($_GET['class_id'] ?? 0);

        // If class is selected, show that class's date sheet
        if ($classId) {
            $entries = DateSheet::getByExamAndClass($examId, $classId, $schoolId);
        } else {
            $entries = DateSheet::allForExam($examId, $schoolId);
        }
        $school = School::find($schoolId);

        $this->view('datesheets.view', [
            'title'   => 'Date Sheet - ' . $exam['name'],
            'exam'    => $exam,
            'entries' => $entries,
            'school'  => $school,
            'classes' => $classes,
            'classId' => $classId,
        ]);
    }

    /**
     * PDF download — per class
     */
    public function downloadPdf($examId): void
    {
        $examId = (int) $examId;
        $schoolId = current_school_id();
        $exam = Exam::findSecure($examId, $schoolId);
        if (!$exam) {
            flash('error', 'Exam not found.');
            redirect(tenant_slug() . '/datesheets');
        }

        $classId = (int) ($_GET['class_id'] ?? 0);
        $className = '';
        if ($classId) {
            $entries = DateSheet::getByExamAndClass($examId, $classId, $schoolId);
            $classObj = ClassModel::find($classId);
            $className = $classObj ? ' - ' . $classObj['name'] : '';
        } else {
            $entries = DateSheet::allForExam($examId, $schoolId);
        }
        $school = School::find($schoolId);

        $html = PdfHelper::renderView('pdf.datesheet', [
            'exam'      => $exam,
            'entries'   => $entries,
            'school'    => $school,
            'className' => $className,
        ]);

        PdfHelper::generate($html, 'datesheet_' . $exam['name'] . $className . '.pdf');
    }

    public function delete($id): void
    {
        $id = (int) $id;
        $schoolId = current_school_id();
        DateSheet::delete($id, $schoolId);
        $this->audit('delete', 'datesheet', $id);
        flash('success', 'Date sheet entry deleted.');
        redirect(tenant_slug() . '/datesheets');
    }
}
