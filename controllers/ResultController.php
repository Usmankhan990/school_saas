<?php
/**
 * ResultController – Marks entry, result cards, PDF
 */
class ResultController extends Controller
{
    public function index(): void
    {
        $schoolId = current_school_id();
        $exams   = Exam::allForSchool($schoolId);
        $classes = ClassModel::allForSchool($schoolId);
        $this->view('results.index', [
            'title'   => 'Results',
            'exams'   => $exams,
            'classes' => $classes,
        ]);
    }

    public function enterMarks($examId): void
    {
        $examId = (int) $examId;
        $schoolId = current_school_id();
        $exam = Exam::findSecure($examId, $schoolId);
        if (!$exam) {
            flash('error', 'Exam not found.');
            redirect(tenant_slug() . '/results');
        }

        $classId = (int) ($_GET['class_id'] ?? $exam['class_id'] ?? 0);
        if (!$classId) {
            flash('error', 'Please select a class.');
            redirect(tenant_slug() . '/results');
        }

        $students = Database::fetchAll(
            "SELECT * FROM students WHERE school_id = ? AND class_id = ? ORDER BY name ASC",
            [$schoolId, $classId]
        );
        $subjects = Subject::allForSchool($schoolId);

        // Get existing marks
        $existingMarks = Result::getMarksByExamAndClass($examId, $classId, $schoolId);
        $marksMap = [];
        foreach ($existingMarks as $m) {
            $marksMap[$m['student_id']][$m['subject_id']] = $m;
        }

        $class = ClassModel::find($classId);

        $this->view('results.enter-marks', [
            'title'     => 'Enter Marks - ' . $exam['name'],
            'exam'      => $exam,
            'students'  => $students,
            'subjects'  => $subjects,
            'marksMap'  => $marksMap,
            'class'     => $class,
            'classId'   => $classId,
        ]);
    }

    public function storeMarks(): void
    {
        $schoolId  = current_school_id();
        $examId    = (int) $this->input('exam_id');
        $classId   = (int) $this->input('class_id');
        $marks     = $_POST['marks'] ?? [];
        $maxMarks  = $_POST['max_marks'] ?? [];

        foreach ($marks as $studentId => $subjects) {
            foreach ($subjects as $subjectId => $marksVal) {
                $max = (float) ($maxMarks[$subjectId] ?? 100);
                Result::saveMarks([
                    'school_id'  => $schoolId,
                    'exam_id'    => $examId,
                    'student_id' => (int) $studentId,
                    'subject_id' => (int) $subjectId,
                    'marks'      => (float) $marksVal,
                    'max_marks'  => $max,
                ]);
            }
        }

        // Notify about results
        try {
            $exam = Exam::find($examId);
            Notification::notifyClass($schoolId, $classId, 'Results Published: ' . $exam['name'],
                'Results for ' . $exam['name'] . ' have been published.', 'result');
        } catch (Exception $e) {}

        $this->audit('enter_marks', 'result', $examId);
        flash('success', 'Marks saved successfully.');
        redirect(tenant_slug() . "/results/{$examId}/enter-marks?class_id={$classId}");
    }

    public function viewResult($examId, $studentId): void
    {
        $examId = (int) $examId;
        $studentId = (int) $studentId;
        $schoolId = current_school_id();
        $exam    = Exam::findSecure($examId, $schoolId);
        $student = Student::findSecure($studentId, $schoolId);
        if (!$exam || !$student) {
            flash('error', 'Record not found.');
            redirect(tenant_slug() . '/results');
        }

        $result = Result::getStudentResultSummary($examId, $studentId);
        $school = School::find($schoolId);

        $this->view('results.view', [
            'title'   => 'Result Card - ' . $student['name'],
            'exam'    => $exam,
            'student' => $student,
            'result'  => $result,
            'school'  => $school,
        ]);
    }

    public function downloadPdf($examId, $studentId): void
    {
        $examId = (int) $examId;
        $studentId = (int) $studentId;
        $schoolId = current_school_id();
        $exam    = Exam::findSecure($examId, $schoolId);
        $student = Student::findSecure($studentId, $schoolId);
        if (!$exam || !$student) {
            flash('error', 'Record not found.');
            redirect(tenant_slug() . '/results');
        }

        $result = Result::getStudentResultSummary($examId, $studentId);
        $school = School::find($schoolId);

        $html = PdfHelper::renderView('pdf.result-card', [
            'exam'    => $exam,
            'student' => $student,
            'result'  => $result,
            'school'  => $school,
        ]);

        PdfHelper::generate($html, 'result_' . $student['name'] . '_' . $exam['name'] . '.pdf');
    }
}
