<?php
class StudentFee
{
    public static function allForStudent(int $studentId, int $schoolId): array
    {
        return Database::fetchAll(
            "SELECT sf.*, ft.name as fee_type_name
             FROM student_fees sf
             LEFT JOIN fee_types ft ON sf.fee_type_id = ft.id
             WHERE sf.student_id = ? AND sf.school_id = ?
             ORDER BY sf.due_date ASC",
            [$studentId, $schoolId]
        );
    }

    public static function allForSchool(int $schoolId, string $status = ''): array
    {
        $sql = "SELECT sf.*, ft.name as fee_type_name, s.name as student_name, s.admission_no, c.name as class_name
                FROM student_fees sf
                LEFT JOIN fee_types ft ON sf.fee_type_id = ft.id
                LEFT JOIN students s ON sf.student_id = s.id
                LEFT JOIN classes c ON s.class_id = c.id
                WHERE sf.school_id = ?";
        $params = [$schoolId];
        if ($status) {
            $sql .= " AND sf.status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY sf.due_date ASC";
        return Database::fetchAll($sql, $params);
    }

    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM student_fees WHERE id = ?", [$id]);
    }

    public static function findSecure(int $id, int $schoolId): ?array
    {
        return Database::fetch("SELECT * FROM student_fees WHERE id = ? AND school_id = ?", [$id, $schoolId]);
    }

    public static function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return Database::insert('student_fees', $data);
    }

    public static function updatePayment(int $id, float $paidAmount): void
    {
        $sf = self::find($id);
        if (!$sf) return;
        $newPaid = (float)$sf['paid_amount'] + $paidAmount;
        $status = 'partial';
        if ($newPaid >= (float)$sf['amount']) {
            $status = 'paid';
            $newPaid = (float)$sf['amount'];
        }
        Database::update('student_fees', [
            'paid_amount' => $newPaid,
            'status'      => $status,
        ], 'id = ?', [$id]);
    }

    public static function getDefaulters(int $schoolId, int $classId = 0): array
    {
        $sql = "SELECT sf.*, ft.name as fee_type_name, s.name as student_name, s.admission_no, s.phone, c.name as class_name
                FROM student_fees sf
                LEFT JOIN fee_types ft ON sf.fee_type_id = ft.id
                LEFT JOIN students s ON sf.student_id = s.id
                LEFT JOIN classes c ON s.class_id = c.id
                WHERE sf.school_id = ? AND sf.status != 'paid'";
        $params = [$schoolId];
        if ($classId) {
            $sql .= " AND s.class_id = ?";
            $params[] = $classId;
        }
        $sql .= " ORDER BY sf.due_date ASC";
        return Database::fetchAll($sql, $params);
    }

    // ── Aggregate queries for dashboards ──

    public static function totalAssigned(int $schoolId): float
    {
        $r = Database::fetch("SELECT COALESCE(SUM(amount),0) as total FROM student_fees WHERE school_id = ?", [$schoolId]);
        return (float)$r['total'];
    }

    public static function totalCollected(int $schoolId): float
    {
        $r = Database::fetch("SELECT COALESCE(SUM(paid_amount),0) as total FROM student_fees WHERE school_id = ?", [$schoolId]);
        return (float)$r['total'];
    }

    public static function totalPending(int $schoolId): float
    {
        $r = Database::fetch("SELECT COALESCE(SUM(amount - paid_amount),0) as total FROM student_fees WHERE school_id = ? AND status != 'paid'", [$schoolId]);
        return (float)$r['total'];
    }

    public static function countByStatus(int $schoolId): array
    {
        return Database::fetchAll(
            "SELECT status, COUNT(*) as cnt, SUM(amount) as total_amount, SUM(paid_amount) as total_paid
             FROM student_fees WHERE school_id = ? GROUP BY status",
            [$schoolId]
        );
    }

    public static function getClassWiseSummary(int $schoolId): array
    {
        return Database::fetchAll(
            "SELECT c.name as class_name, c.id as class_id,
                    COUNT(sf.id) as fee_count,
                    COALESCE(SUM(sf.amount),0) as total_amount,
                    COALESCE(SUM(sf.paid_amount),0) as total_paid,
                    COALESCE(SUM(sf.amount - sf.paid_amount),0) as total_pending
             FROM student_fees sf
             LEFT JOIN students s ON sf.student_id = s.id
             LEFT JOIN classes c ON s.class_id = c.id
             WHERE sf.school_id = ?
             GROUP BY c.id, c.name
             ORDER BY c.name ASC",
            [$schoolId]
        );
    }
}
