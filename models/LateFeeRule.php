<?php
class LateFeeRule
{
    public static function allForSchool(int $schoolId): array
    {
        return Database::fetchAll(
            "SELECT lfr.*, ft.name as fee_type_name
             FROM late_fee_rules lfr
             LEFT JOIN fee_types ft ON lfr.fee_type_id = ft.id
             WHERE lfr.school_id = ?",
            [$schoolId]
        );
    }

    public static function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return Database::insert('late_fee_rules', $data);
    }

    public static function delete(int $id, int $schoolId): int
    {
        return Database::delete('late_fee_rules', 'id = ? AND school_id = ?', [$id, $schoolId]);
    }

    public static function calculateLateFee(int $schoolId, int $feeTypeId, float $amount, string $dueDate): float
    {
        $rule = Database::fetch(
            "SELECT * FROM late_fee_rules WHERE school_id = ? AND (fee_type_id = ? OR fee_type_id IS NULL) ORDER BY fee_type_id DESC LIMIT 1",
            [$schoolId, $feeTypeId]
        );
        if (!$rule) return 0;

        $daysLate = max(0, (int)((time() - strtotime($dueDate)) / 86400) - (int)$rule['grace_days']);
        if ($daysLate <= 0) return 0;

        if ($rule['penalty_type'] === 'fixed') {
            return (float)$rule['penalty_value'];
        }
        return round($amount * (float)$rule['penalty_value'] / 100, 2);
    }
}
