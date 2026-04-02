<?php
/**
 * Teacher Model
 */
class Teacher
{
    public static function allForSchool(int $schoolId): array
    {
        return Database::fetchAll(
            "SELECT t.*, u.email as user_email
             FROM teachers t
             LEFT JOIN users u ON t.user_id = u.id
             WHERE t.school_id = ?
             ORDER BY t.created_at DESC",
            [$schoolId]
        );
    }

    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM teachers WHERE id = ?", [$id]);
    }

    public static function findSecure(int $id, int $schoolId): ?array
    {
        return Database::fetch(
            "SELECT t.*, u.email as user_email
             FROM teachers t
             LEFT JOIN users u ON t.user_id = u.id
             WHERE t.id = ? AND t.school_id = ?",
            [$id, $schoolId]
        );
    }

    public static function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return Database::insert('teachers', $data);
    }

    public static function update(int $id, array $data): int
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return Database::update('teachers', $data, 'id = ?', [$id]);
    }

    public static function delete(int $id, int $schoolId): int
    {
        return Database::delete('teachers', 'id = ? AND school_id = ?', [$id, $schoolId]);
    }

    public static function countForSchool(int $schoolId): int
    {
        $row = Database::fetch("SELECT COUNT(*) as cnt FROM teachers WHERE school_id = ?", [$schoolId]);
        return (int) $row['cnt'];
    }
}
