<?php
/**
 * Attendance Model
 */
class Attendance
{
    public static function markBulk(array $records): void
    {
        foreach ($records as $r) {
            // Check if already marked
            $existing = Database::fetch(
                "SELECT id FROM attendance_students WHERE school_id = ? AND date = ? AND class_id = ? AND student_id = ?",
                [$r['school_id'], $r['date'], $r['class_id'], $r['student_id']]
            );
            if ($existing) {
                Database::update('attendance_students', [
                    'status'    => $r['status'],
                    'marked_by' => $r['marked_by'],
                ], 'id = ?', [$existing['id']]);
            } else {
                Database::insert('attendance_students', $r);
            }
        }
    }

    public static function getByDateClass(int $schoolId, string $date, int $classId, ?int $sectionId = null): array
    {
        $sql = "SELECT a.*, s.name as student_name, s.admission_no
                FROM attendance_students a
                LEFT JOIN students s ON a.student_id = s.id
                WHERE a.school_id = ? AND a.date = ? AND a.class_id = ?";
        $params = [$schoolId, $date, $classId];
        if ($sectionId) {
            $sql .= " AND a.section_id = ?";
            $params[] = $sectionId;
        }
        $sql .= " ORDER BY s.name ASC";
        return Database::fetchAll($sql, $params);
    }

    public static function getDailyReport(int $schoolId, string $date): array
    {
        return Database::fetchAll(
            "SELECT c.name as class_name,
                    SUM(CASE WHEN a.status='P' THEN 1 ELSE 0 END) as present,
                    SUM(CASE WHEN a.status='A' THEN 1 ELSE 0 END) as absent,
                    SUM(CASE WHEN a.status='L' THEN 1 ELSE 0 END) as late,
                    COUNT(*) as total
             FROM attendance_students a
             LEFT JOIN classes c ON a.class_id = c.id
             WHERE a.school_id = ? AND a.date = ?
             GROUP BY a.class_id
             ORDER BY c.name ASC",
            [$schoolId, $date]
        );
    }

    public static function getMonthlyReport(int $schoolId, int $classId, int $year, int $month): array
    {
        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));

        return Database::fetchAll(
            "SELECT s.name as student_name, s.admission_no,
                    SUM(CASE WHEN a.status='P' THEN 1 ELSE 0 END) as present,
                    SUM(CASE WHEN a.status='A' THEN 1 ELSE 0 END) as absent,
                    SUM(CASE WHEN a.status='L' THEN 1 ELSE 0 END) as late,
                    COUNT(*) as total_days
             FROM students s
             LEFT JOIN attendance_students a ON s.id = a.student_id
                AND a.date BETWEEN ? AND ? AND a.school_id = ?
             WHERE s.school_id = ? AND s.class_id = ?
             GROUP BY s.id
             ORDER BY s.name ASC",
            [$startDate, $endDate, $schoolId, $schoolId, $classId]
        );
    }

    public static function autoMarkFromOnlineClass(int $schoolId, int $classId, ?int $sectionId, int $studentId, int $markedBy): void
    {
        $today = date('Y-m-d');
        $existing = Database::fetch(
            "SELECT id FROM attendance_students WHERE school_id = ? AND date = ? AND class_id = ? AND student_id = ?",
            [$schoolId, $today, $classId, $studentId]
        );
        if (!$existing) {
            Database::insert('attendance_students', [
                'school_id'  => $schoolId,
                'date'       => $today,
                'class_id'   => $classId,
                'section_id' => $sectionId,
                'student_id' => $studentId,
                'status'     => 'P',
                'marked_by'  => $markedBy,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
