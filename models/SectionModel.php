<?php
/**
 * Section Model
 */
class SectionModel
{
    public static function allForSchool(int $schoolId): array
    {
        return Database::fetchAll(
            "SELECT s.*, c.name as class_name
             FROM sections s
             JOIN classes c ON s.class_id = c.id
             WHERE s.school_id = ?
             ORDER BY c.name ASC, s.name ASC",
            [$schoolId]
        );
    }

    public static function allForClass(int $classId, int $schoolId): array
    {
        return Database::fetchAll(
            "SELECT * FROM sections WHERE class_id = ? AND school_id = ? ORDER BY name ASC",
            [$classId, $schoolId]
        );
    }

    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM sections WHERE id = ?", [$id]);
    }

    public static function findSecure(int $id, int $schoolId): ?array
    {
        return Database::fetch("SELECT * FROM sections WHERE id = ? AND school_id = ?", [$id, $schoolId]);
    }

    public static function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return Database::insert('sections', $data);
    }

    public static function update(int $id, array $data): int
    {
        return Database::update('sections', $data, 'id = ?', [$id]);
    }

    public static function delete(int $id, int $schoolId): int
    {
        return Database::delete('sections', 'id = ? AND school_id = ?', [$id, $schoolId]);
    }
}
