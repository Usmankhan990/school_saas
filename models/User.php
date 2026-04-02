<?php
/**
 * User Model
 */
class User
{
    /**
     * Find all users for a school (or all if platform_admin).
     */
    public static function allForSchool(?int $schoolId = null): array
    {
        if ($schoolId) {
            return Database::fetchAll(
                "SELECT * FROM users WHERE school_id = ? ORDER BY created_at DESC",
                [$schoolId]
            );
        }
        return Database::fetchAll("SELECT * FROM users ORDER BY created_at DESC");
    }

    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM users WHERE id = ?", [$id]);
    }

    public static function findByEmail(string $email): ?array
    {
        return Database::fetch("SELECT * FROM users WHERE email = ? LIMIT 1", [$email]);
    }

    public static function findByEmailAndSchool(string $email, int $schoolId): ?array
    {
        return Database::fetch(
            "SELECT * FROM users WHERE email = ? AND school_id = ? LIMIT 1",
            [$email, $schoolId]
        );
    }

    public static function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return Database::insert('users', $data);
    }

    public static function update(int $id, array $data): int
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return Database::update('users', $data, 'id = ?', [$id]);
    }

    public static function emailExists(string $email, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as cnt FROM users WHERE email = ?";
        $params = [$email];
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        $row = Database::fetch($sql, $params);
        return $row['cnt'] > 0;
    }

    public static function countForSchool(int $schoolId): int
    {
        $row = Database::fetch("SELECT COUNT(*) as cnt FROM users WHERE school_id = ?", [$schoolId]);
        return (int) $row['cnt'];
    }

    public static function countByRole(int $schoolId, string $role): int
    {
        $row = Database::fetch(
            "SELECT COUNT(*) as cnt FROM users WHERE school_id = ? AND role = ?",
            [$schoolId, $role]
        );
        return (int) $row['cnt'];
    }
}
