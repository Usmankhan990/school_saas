<?php
/**
 * ClassModel – Named differently to avoid PHP reserved word conflict
 */
class ClassModel
{
    public static function allForSchool(int $schoolId): array
    {
        return Database::fetchAll(
            "SELECT c.*, (SELECT COUNT(*) FROM sections WHERE class_id = c.id) as section_count,
                    (SELECT COUNT(*) FROM students WHERE class_id = c.id AND status = 'active') as student_count
             FROM classes c
             WHERE c.school_id = ?
             ORDER BY c.numeric_name ASC, c.name ASC",
            [$schoolId]
        );
    }

    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM classes WHERE id = ?", [$id]);
    }

    public static function findSecure(int $id, int $schoolId): ?array
    {
        return Database::fetch("SELECT * FROM classes WHERE id = ? AND school_id = ?", [$id, $schoolId]);
    }

    public static function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return Database::insert('classes', $data);
    }

    public static function update(int $id, array $data): int
    {
        return Database::update('classes', $data, 'id = ?', [$id]);
    }

    public static function delete(int $id, int $schoolId): int
    {
        return Database::delete('classes', 'id = ? AND school_id = ?', [$id, $schoolId]);
    }

    public static function countForSchool(int $schoolId): int
    {
        $row = Database::fetch("SELECT COUNT(*) as cnt FROM classes WHERE school_id = ?", [$schoolId]);
        return (int) $row['cnt'];
    }
}
