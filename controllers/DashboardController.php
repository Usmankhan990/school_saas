<?php
/**
 * DashboardController – School dashboard
 */
class DashboardController extends Controller
{
    public function index(): void
    {
        $schoolId = current_school_id();
        $role = current_role();

        $data = [
            'title' => 'Dashboard',
            'role'  => $role,
        ];

        // Stats based on role
        if (has_role(['super_admin', 'admin', 'staff'])) {
            $data['totalStudents']  = Student::countForSchool($schoolId);
            $data['activeStudents'] = Student::countActiveForSchool($schoolId);
            $data['totalTeachers']  = Teacher::countForSchool($schoolId);
            $data['totalStaff']     = Staff::countForSchool($schoolId);
            $data['totalClasses']   = ClassModel::countForSchool($schoolId);
            $data['totalUsers']     = User::countForSchool($schoolId);
        }

        // Teacher-specific: show their assigned class count (placeholder for Phase 2)
        if ($role === 'teacher') {
            $data['totalStudents'] = 0; // Will be populated in Phase 2
            $data['assignedClasses'] = 0;
        }

        // Student-specific
        if ($role === 'student') {
            $data['studentInfo'] = null; // Will be populated later
        }

        $this->view('dashboard.index', $data);
    }
}
