<?php
/**
 * Staff Model
 */
class Staff
{
    public static function allForSchool(int $schoolId): array
    {
        return Database::fetchAll(
            "SELECT st.*, u.email as user_email
             FROM staff st
             LEFT JOIN users u ON st.user_id = u.id
             WHERE st.school_id = ?
             ORDER BY st.created_at DESC",
            [$schoolId]
        );
    }

    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM staff WHERE id = ?", [$id]);
    }

    public static function findSecure(int $id, int $schoolId): ?array
    {
        return Database::fetch(
            "SELECT st.*, u.email as user_email
             FROM staff st
             LEFT JOIN users u ON st.user_id = u.id
             WHERE st.id = ? AND st.school_id = ?",
            [$id, $schoolId]
        );
    }

    public static function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return Database::insert('staff', $data);
    }

    public static function update(int $id, array $data): int
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return Database::update('staff', $data, 'id = ?', [$id]);
    }

    public static function delete(int $id, int $schoolId): int
    {
        return Database::delete('staff', 'id = ? AND school_id = ?', [$id, $schoolId]);
    }

    public static function countForSchool(int $schoolId): int
    {
        $row = Database::fetch("SELECT COUNT(*) as cnt FROM staff WHERE school_id = ?", [$schoolId]);
        return (int) $row['cnt'];
    }
}
