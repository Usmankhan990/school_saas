<?php
/**
 * School Model
 */
class School
{
    public static function all(): array
    {
        return Database::fetchAll("SELECT * FROM schools ORDER BY created_at DESC");
    }

    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM schools WHERE id = ?", [$id]);
    }

    public static function findBySlug(string $slug): ?array
    {
        return Database::fetch("SELECT * FROM schools WHERE slug = ? LIMIT 1", [$slug]);
    }

    public static function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return Database::insert('schools', $data);
    }

    public static function update(int $id, array $data): int
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return Database::update('schools', $data, 'id = ?', [$id]);
    }

    public static function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as cnt FROM schools WHERE slug = ?";
        $params = [$slug];
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        $row = Database::fetch($sql, $params);
        return $row['cnt'] > 0;
    }

    public static function count(): int
    {
        $row = Database::fetch("SELECT COUNT(*) as cnt FROM schools");
        return (int) $row['cnt'];
    }

    public static function countActive(): int
    {
        $row = Database::fetch("SELECT COUNT(*) as cnt FROM schools WHERE status = 'active'");
        return (int) $row['cnt'];
    }
}
