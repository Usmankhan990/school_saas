<?php
class Payment
{
    public static function allForSchool(int $schoolId, int $limit = 50): array
    {
        return Database::fetchAll(
            "SELECT p.*, s.name as student_name, s.admission_no, ft.name as fee_type_name, c.name as class_name
             FROM payments p
             LEFT JOIN students s ON p.student_id = s.id
             LEFT JOIN student_fees sf ON p.student_fee_id = sf.id
             LEFT JOIN fee_types ft ON sf.fee_type_id = ft.id
             LEFT JOIN classes c ON s.class_id = c.id
             WHERE p.school_id = ?
             ORDER BY p.created_at DESC
             LIMIT ?",
            [$schoolId, $limit]
        );
    }

    public static function allForStudent(int $studentId, int $schoolId): array
    {
        return Database::fetchAll(
            "SELECT p.*, ft.name as fee_type_name
             FROM payments p
             LEFT JOIN student_fees sf ON p.student_fee_id = sf.id
             LEFT JOIN fee_types ft ON sf.fee_type_id = ft.id
             WHERE p.student_id = ? AND p.school_id = ?
             ORDER BY p.payment_date DESC",
            [$studentId, $schoolId]
        );
    }

    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM payments WHERE id = ?", [$id]);
    }

    public static function findSecure(int $id, int $schoolId): ?array
    {
        return Database::fetch(
            "SELECT p.*, s.name as student_name, s.admission_no, s.father_name, s.phone,
                    ft.name as fee_type_name, sf.amount as fee_amount, sf.paid_amount,
                    c.name as class_name, sec.name as section_name
             FROM payments p
             LEFT JOIN students s ON p.student_id = s.id
             LEFT JOIN student_fees sf ON p.student_fee_id = sf.id
             LEFT JOIN fee_types ft ON sf.fee_type_id = ft.id
             LEFT JOIN classes c ON s.class_id = c.id
             LEFT JOIN sections sec ON s.section_id = sec.id
             WHERE p.id = ? AND p.school_id = ?",
            [$id, $schoolId]
        );
    }

    public static function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return Database::insert('payments', $data);
    }

    // ── Revenue aggregations ──

    public static function todayCollection(int $schoolId): float
    {
        $r = Database::fetch(
            "SELECT COALESCE(SUM(amount),0) as total FROM payments WHERE school_id = ? AND payment_date = CURDATE()",
            [$schoolId]
        );
        return (float)$r['total'];
    }

    public static function monthlyRevenue(int $schoolId, int $year = 0, int $month = 0): float
    {
        $year = $year ?: (int)date('Y');
        $month = $month ?: (int)date('m');
        $r = Database::fetch(
            "SELECT COALESCE(SUM(amount),0) as total FROM payments WHERE school_id = ? AND YEAR(payment_date) = ? AND MONTH(payment_date) = ?",
            [$schoolId, $year, $month]
        );
        return (float)$r['total'];
    }

    public static function totalRevenue(int $schoolId): float
    {
        $r = Database::fetch("SELECT COALESCE(SUM(amount),0) as total FROM payments WHERE school_id = ?", [$schoolId]);
        return (float)$r['total'];
    }

    public static function dailyRevenueChart(int $schoolId, int $days = 30): array
    {
        return Database::fetchAll(
            "SELECT DATE(payment_date) as day, SUM(amount) as total
             FROM payments WHERE school_id = ? AND payment_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
             GROUP BY DATE(payment_date) ORDER BY day ASC",
            [$schoolId, $days]
        );
    }

    public static function monthlyRevenueChart(int $schoolId, int $year = 0): array
    {
        $year = $year ?: (int)date('Y');
        return Database::fetchAll(
            "SELECT MONTH(payment_date) as month, SUM(amount) as total
             FROM payments WHERE school_id = ? AND YEAR(payment_date) = ?
             GROUP BY MONTH(payment_date) ORDER BY month ASC",
            [$schoolId, $year]
        );
    }

    // ── Super Admin: All schools ──

    public static function globalTotalRevenue(): float
    {
        $r = Database::fetch("SELECT COALESCE(SUM(amount),0) as total FROM payments");
        return (float)$r['total'];
    }

    public static function globalTodayCollection(): float
    {
        $r = Database::fetch("SELECT COALESCE(SUM(amount),0) as total FROM payments WHERE payment_date = CURDATE()");
        return (float)$r['total'];
    }

    public static function globalMonthlyRevenue(): float
    {
        $r = Database::fetch(
            "SELECT COALESCE(SUM(amount),0) as total FROM payments WHERE YEAR(payment_date) = YEAR(CURDATE()) AND MONTH(payment_date) = MONTH(CURDATE())"
        );
        return (float)$r['total'];
    }

    public static function globalPendingAmount(): float
    {
        $r = Database::fetch("SELECT COALESCE(SUM(amount - paid_amount),0) as total FROM student_fees WHERE status != 'paid'");
        return (float)$r['total'];
    }

    public static function globalPaidAmount(): float
    {
        $r = Database::fetch("SELECT COALESCE(SUM(paid_amount),0) as total FROM student_fees");
        return (float)$r['total'];
    }

    public static function schoolWiseRevenue(): array
    {
        return Database::fetchAll(
            "SELECT sc.id, sc.name as school_name,
                    COALESCE(SUM(p.amount),0) as total_revenue,
                    (SELECT COALESCE(SUM(amount),0) FROM payments WHERE school_id = sc.id AND payment_date = CURDATE()) as today_collection,
                    (SELECT COALESCE(SUM(amount - paid_amount),0) FROM student_fees WHERE school_id = sc.id AND status != 'paid') as pending_amount
             FROM schools sc
             LEFT JOIN payments p ON p.school_id = sc.id
             WHERE sc.status = 'active'
             GROUP BY sc.id, sc.name
             ORDER BY total_revenue DESC"
        );
    }

    public static function globalMonthlyChart(int $year = 0): array
    {
        $year = $year ?: (int)date('Y');
        return Database::fetchAll(
            "SELECT MONTH(payment_date) as month, SUM(amount) as total
             FROM payments WHERE YEAR(payment_date) = ?
             GROUP BY MONTH(payment_date) ORDER BY month ASC",
            [$year]
        );
    }

    public static function methodBreakdown(int $schoolId): array
    {
        return Database::fetchAll(
            "SELECT payment_method, COUNT(*) as cnt, SUM(amount) as total
             FROM payments WHERE school_id = ? GROUP BY payment_method ORDER BY total DESC",
            [$schoolId]
        );
    }
}
