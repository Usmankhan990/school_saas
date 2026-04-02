<?php
/**
 * Exam Model
 */
class Exam
{
    public static function allForSchool(int $schoolId): array
    {
        return Database::fetchAll(
            "SELECT e.*, c.name as class_name
             FROM exams e
             LEFT JOIN classes c ON e.class_id = c.id
             WHERE e.school_id = ?
             ORDER BY e.start_date DESC",
            [$schoolId]
        );
    }

    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM exams WHERE id = ?", [$id]);
    }

    public static function findSecure(int $id, int $schoolId): ?array
    {
        return Database::fetch(
            "SELECT * FROM exams WHERE id = ? AND school_id = ?",
            [$id, $schoolId]
        );
    }

    public static function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return Database::insert('exams', $data);
    }

    public static function update(int $id, array $data): int
    {
        return Database::update('exams', $data, 'id = ?', [$id]);
    }

    public static function delete(int $id, int $schoolId): int
    {
        return Database::delete('exams', 'id = ? AND school_id = ?', [$id, $schoolId]);
    }

    public static function countForSchool(int $schoolId): int
    {
        $row = Database::fetch("SELECT COUNT(*) as cnt FROM exams WHERE school_id = ?", [$schoolId]);
        return (int) $row['cnt'];
    }
}
