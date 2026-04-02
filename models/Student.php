<?php
/**
 * Student Model
 */
class Student
{
    public static function allForSchool(int $schoolId): array
    {
        return Database::fetchAll(
            "SELECT s.*, c.name as class_name, sec.name as section_name
             FROM students s
             LEFT JOIN classes c ON s.class_id = c.id
             LEFT JOIN sections sec ON s.section_id = sec.id
             WHERE s.school_id = ?
             ORDER BY s.created_at DESC",
            [$schoolId]
        );
    }

    public static function find(int $id): ?array
    {
        return Database::fetch(
            "SELECT s.*, c.name as class_name, sec.name as section_name
             FROM students s
             LEFT JOIN classes c ON s.class_id = c.id
             LEFT JOIN sections sec ON s.section_id = sec.id
             WHERE s.id = ?",
            [$id]
        );
    }

    public static function findSecure(int $id, int $schoolId): ?array
    {
        return Database::fetch(
            "SELECT s.*, c.name as class_name, sec.name as section_name
             FROM students s
             LEFT JOIN classes c ON s.class_id = c.id
             LEFT JOIN sections sec ON s.section_id = sec.id
             WHERE s.id = ? AND s.school_id = ?",
            [$id, $schoolId]
        );
    }

    public static function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return Database::insert('students', $data);
    }

    public static function update(int $id, array $data): int
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return Database::update('students', $data, 'id = ?', [$id]);
    }

    public static function delete(int $id, int $schoolId): int
    {
        return Database::delete('students', 'id = ? AND school_id = ?', [$id, $schoolId]);
    }

    public static function generateAdmissionNo(int $schoolId): string
    {
        $row = Database::fetch(
            "SELECT COUNT(*) as cnt FROM students WHERE school_id = ?",
            [$schoolId]
        );
        $next = ($row['cnt'] ?? 0) + 1;
        return 'ADM-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }

    public static function admissionNoExists(int $schoolId, string $admissionNo, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as cnt FROM students WHERE school_id = ? AND admission_no = ?";
        $params = [$schoolId, $admissionNo];
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        $row = Database::fetch($sql, $params);
        return $row['cnt'] > 0;
    }

    public static function countForSchool(int $schoolId): int
    {
        $row = Database::fetch("SELECT COUNT(*) as cnt FROM students WHERE school_id = ?", [$schoolId]);
        return (int) $row['cnt'];
    }

    public static function countActiveForSchool(int $schoolId): int
    {
        $row = Database::fetch(
            "SELECT COUNT(*) as cnt FROM students WHERE school_id = ? AND status = 'active'",
            [$schoolId]
        );
        return (int) $row['cnt'];
    }
}
