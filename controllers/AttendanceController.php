<?php
/**
 * AttendanceController – Mark & view attendance
 */
class AttendanceController extends Controller
{
    public function index(): void
    {
        $schoolId = current_school_id();
        $classes = ClassModel::allForSchool($schoolId);
        $this->view('attendance.index', [
            'title'   => 'Attendance',
            'classes' => $classes,
        ]);
    }

    public function mark(): void
    {
        $schoolId  = current_school_id();
        $classId   = (int) ($_GET['class_id'] ?? 0);
        $sectionId = (int) ($_GET['section_id'] ?? 0) ?: null;
        $date      = $_GET['date'] ?? date('Y-m-d');

        if (!$classId) {
            flash('error', 'Please select a class.');
            redirect(tenant_slug() . '/attendance');
        }

        // Get students for this class
        $sql = "SELECT * FROM students WHERE school_id = ? AND class_id = ?";
        $params = [$schoolId, $classId];
        if ($sectionId) {
            $sql .= " AND section_id = ?";
            $params[] = $sectionId;
        }
        $sql .= " ORDER BY name ASC";
        $students = Database::fetchAll($sql, $params);

        // Get existing attendance
        $existing = Attendance::getByDateClass($schoolId, $date, $classId, $sectionId);
        $attendanceMap = [];
        foreach ($existing as $a) {
            $attendanceMap[$a['student_id']] = $a['status'];
        }

        $class = ClassModel::find($classId);
        $sections = SectionModel::allForClass($classId, $schoolId);

        $this->view('attendance.mark', [
            'title'         => 'Mark Attendance',
            'students'      => $students,
            'attendanceMap' => $attendanceMap,
            'classId'       => $classId,
            'sectionId'     => $sectionId,
            'date'          => $date,
            'class'         => $class,
            'sections'      => $sections,
        ]);
    }

    public function store(): void
    {
        $schoolId  = current_school_id();
        $classId   = (int) $this->input('class_id');
        $sectionId = (int) $this->input('section_id') ?: null;
        $date      = $this->input('date');
        $statuses  = $_POST['status'] ?? [];

        $records = [];
        foreach ($statuses as $studentId => $status) {
            $records[] = [
                'school_id'  => $schoolId,
                'date'       => $date,
                'class_id'   => $classId,
                'section_id' => $sectionId,
                'student_id' => (int) $studentId,
                'status'     => $status,
                'marked_by'  => current_user_id(),
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }

        Attendance::markBulk($records);
        $this->audit('mark', 'attendance');
        flash('success', 'Attendance marked successfully for ' . $date);
        redirect(tenant_slug() . '/attendance');
    }

    public function dailyReport(): void
    {
        $schoolId = current_school_id();
        $date = $_GET['date'] ?? date('Y-m-d');
        $report = Attendance::getDailyReport($schoolId, $date);

        $this->view('attendance.daily-report', [
            'title'  => 'Daily Attendance Report',
            'report' => $report,
            'date'   => $date,
        ]);
    }

    public function monthlyReport(): void
    {
        $schoolId = current_school_id();
        $classId  = (int) ($_GET['class_id'] ?? 0);
        $month    = (int) ($_GET['month'] ?? date('m'));
        $year     = (int) ($_GET['year'] ?? date('Y'));

        $classes = ClassModel::allForSchool($schoolId);
        $report = [];
        if ($classId) {
            $report = Attendance::getMonthlyReport($schoolId, $classId, $year, $month);
        }

        $this->view('attendance.monthly-report', [
            'title'   => 'Monthly Attendance Report',
            'report'  => $report,
            'classes' => $classes,
            'classId' => $classId,
            'month'   => $month,
            'year'    => $year,
        ]);
    }
}
