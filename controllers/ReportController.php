<?php
/**
 * ReportController – Fee reports, defaulters, CSV export
 */
class ReportController extends Controller
{
    /**
     * Class-wise fee report.
     */
    public function feeReport(): void
    {
        $schoolId = current_school_id();
        $classWise = StudentFee::getClassWiseSummary($schoolId);
        $classes   = ClassModel::allForSchool($schoolId);

        $this->view('reports.fee-report', [
            'title'     => 'Fee Report',
            'classWise' => $classWise,
            'classes'   => $classes,
        ]);
    }

    /**
     * Class-wise detailed report (student-level).
     */
    public function classReport($classId): void
    {
        $classId = (int) $classId;
        $schoolId = current_school_id();
        $class = ClassModel::find($classId);

        $fees = Database::fetchAll(
            "SELECT sf.*, ft.name as fee_type_name, s.name as student_name, s.admission_no
             FROM student_fees sf
             LEFT JOIN fee_types ft ON sf.fee_type_id = ft.id
             LEFT JOIN students s ON sf.student_id = s.id
             WHERE sf.school_id = ? AND s.class_id = ?
             ORDER BY s.name, sf.due_date",
            [$schoolId, $classId]
        );

        $this->view('reports.class-report', [
            'title' => 'Class Report — ' . ($class['name'] ?? ''),
            'class' => $class,
            'fees'  => $fees,
        ]);
    }

    /**
     * Defaulters list report.
     */
    public function defaulters(): void
    {
        $schoolId = current_school_id();
        $classId = (int)($_GET['class_id'] ?? 0);
        $classes = ClassModel::allForSchool($schoolId);
        $defaulters = StudentFee::getDefaulters($schoolId, $classId);

        $this->view('reports.defaulters', [
            'title'      => 'Defaulters Report',
            'defaulters' => $defaulters,
            'classes'    => $classes,
            'classId'    => $classId,
        ]);
    }

    /**
     * Export fee report to CSV.
     */
    public function exportCsv(): void
    {
        $schoolId = current_school_id();
        $fees = StudentFee::allForSchool($schoolId);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="fee_report_' . date('Y-m-d') . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Student', 'Admission#', 'Class', 'Fee Type', 'Amount', 'Paid', 'Balance', 'Status', 'Due Date']);
        foreach ($fees as $f) {
            fputcsv($out, [
                $f['student_name'], $f['admission_no'], $f['class_name'],
                $f['fee_type_name'], $f['amount'], $f['paid_amount'],
                $f['amount'] - $f['paid_amount'], $f['status'], $f['due_date']
            ]);
        }
        fclose($out);
        exit;
    }

    /**
     * Export defaulters to CSV.
     */
    public function exportDefaultersCsv(): void
    {
        $schoolId = current_school_id();
        $defaulters = StudentFee::getDefaulters($schoolId);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="defaulters_' . date('Y-m-d') . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Student', 'Admission#', 'Phone', 'Class', 'Fee Type', 'Amount', 'Paid', 'Balance', 'Due Date']);
        foreach ($defaulters as $d) {
            fputcsv($out, [
                $d['student_name'], $d['admission_no'], $d['phone'], $d['class_name'],
                $d['fee_type_name'], $d['amount'], $d['paid_amount'],
                $d['amount'] - $d['paid_amount'], $d['due_date']
            ]);
        }
        fclose($out);
        exit;
    }

    /**
     * Export defaulters to PDF.
     */
    public function exportDefaultersPdf(): void
    {
        $schoolId = current_school_id();
        $school = School::find($schoolId);
        $defaulters = StudentFee::getDefaulters($schoolId);

        $html = PdfHelper::renderView('pdf.defaulters', [
            'school'     => $school,
            'defaulters' => $defaulters,
        ]);
        PdfHelper::generate($html, 'defaulters_' . date('Y-m-d') . '.pdf');
    }
}
