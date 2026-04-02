<?php
/**
 * Subject Model
 */
class Subject
{
    public static function allForSchool(int $schoolId): array
    {
        return Database::fetchAll(
            "SELECT * FROM subjects WHERE school_id = ? ORDER BY name ASC",
            [$schoolId]
        );
    }

    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM subjects WHERE id = ?", [$id]);
    }

    public static function findSecure(int $id, int $schoolId): ?array
    {
        return Database::fetch(
            "SELECT * FROM subjects WHERE id = ? AND school_id = ?",
            [$id, $schoolId]
        );
    }

    public static function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return Database::insert('subjects', $data);
    }

    public static function update(int $id, array $data): int
    {
        return Database::update('subjects', $data, 'id = ?', [$id]);
    }

    public static function delete(int $id, int $schoolId): int
    {
        return Database::delete('subjects', 'id = ? AND school_id = ?', [$id, $schoolId]);
    }

    public static function countForSchool(int $schoolId): int
    {
        $row = Database::fetch("SELECT COUNT(*) as cnt FROM subjects WHERE school_id = ?", [$schoolId]);
        return (int) $row['cnt'];
    }
}
