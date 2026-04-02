<?php
/**
 * FinanceController – Dashboard for Super Admin (global) and School Admin
 */
class FinanceController extends Controller
{
    /**
     * Super Admin: Global finance dashboard (all schools).
     */
    public function globalDashboard(): void
    {
        if (!has_role(['super_admin'])) {
            flash('error', 'Access denied.');
            redirect('schools');
            return;
        }

        $data = [
            'title'             => 'Global Finance Dashboard',
            'totalRevenue'      => Payment::globalTotalRevenue(),
            'todayCollection'   => Payment::globalTodayCollection(),
            'monthlyRevenue'    => Payment::globalMonthlyRevenue(),
            'pendingAmount'     => Payment::globalPendingAmount(),
            'paidAmount'        => Payment::globalPaidAmount(),
            'schoolWise'        => Payment::schoolWiseRevenue(),
            'monthlyChart'      => Payment::globalMonthlyChart(),
        ];

        $this->viewOnly('finance.global-dashboard', $data);
    }

    /**
     * School Admin: School-level finance dashboard.
     */
    public function schoolDashboard(): void
    {
        $schoolId = current_school_id();
        $studentCount = Database::fetch("SELECT COUNT(*) as cnt FROM students WHERE school_id = ? AND status = 'active'", [$schoolId]);

        $data = [
            'title'           => 'Finance Dashboard',
            'totalStudents'   => (int)$studentCount['cnt'],
            'totalAssigned'   => StudentFee::totalAssigned($schoolId),
            'totalCollected'  => StudentFee::totalCollected($schoolId),
            'totalPending'    => StudentFee::totalPending($schoolId),
            'todayCollection' => Payment::todayCollection($schoolId),
            'monthlyRevenue'  => Payment::monthlyRevenue($schoolId),
            'totalRevenue'    => Payment::totalRevenue($schoolId),
            'statusBreakdown' => StudentFee::countByStatus($schoolId),
            'classWise'       => StudentFee::getClassWiseSummary($schoolId),
            'monthlyChart'    => Payment::monthlyRevenueChart($schoolId),
            'methodBreakdown' => Payment::methodBreakdown($schoolId),
            'recentPayments'  => Payment::allForSchool($schoolId, 10),
        ];

        $this->view('finance.school-dashboard', $data);
    }
}
