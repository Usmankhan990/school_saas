<?php
class FeeType
{
    public static function allForSchool(int $schoolId): array
    {
        return Database::fetchAll("SELECT * FROM fee_types WHERE school_id = ? ORDER BY name ASC", [$schoolId]);
    }

    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM fee_types WHERE id = ?", [$id]);
    }

    public static function findSecure(int $id, int $schoolId): ?array
    {
        return Database::fetch("SELECT * FROM fee_types WHERE id = ? AND school_id = ?", [$id, $schoolId]);
    }

    public static function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return Database::insert('fee_types', $data);
    }

    public static function update(int $id, array $data): int
    {
        return Database::update('fee_types', $data, 'id = ?', [$id]);
    }

    public static function delete(int $id, int $schoolId): int
    {
        return Database::delete('fee_types', 'id = ? AND school_id = ?', [$id, $schoolId]);
    }
}
