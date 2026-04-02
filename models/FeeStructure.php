<?php
class FeeStructure
{
    public static function allForSchool(int $schoolId): array
    {
        return Database::fetchAll(
            "SELECT fs.*, ft.name as fee_type_name, c.name as class_name
             FROM fee_structures fs
             LEFT JOIN fee_types ft ON fs.fee_type_id = ft.id
             LEFT JOIN classes c ON fs.class_id = c.id
             WHERE fs.school_id = ?
             ORDER BY fs.created_at DESC",
            [$schoolId]
        );
    }

    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM fee_structures WHERE id = ?", [$id]);
    }

    public static function findSecure(int $id, int $schoolId): ?array
    {
        return Database::fetch("SELECT * FROM fee_structures WHERE id = ? AND school_id = ?", [$id, $schoolId]);
    }

    public static function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return Database::insert('fee_structures', $data);
    }

    public static function update(int $id, array $data): int
    {
        return Database::update('fee_structures', $data, 'id = ?', [$id]);
    }

    public static function delete(int $id, int $schoolId): int
    {
        return Database::delete('fee_structures', 'id = ? AND school_id = ?', [$id, $schoolId]);
    }

    /**
     * Assign a fee structure to all students in a class.
     */
    public static function assignToClass(int $structureId, int $schoolId): int
    {
        $fs = self::find($structureId);
        if (!$fs || !$fs['class_id']) return 0;

        $students = Database::fetchAll(
            "SELECT id FROM students WHERE school_id = ? AND class_id = ? AND status = 'active'",
            [$schoolId, $fs['class_id']]
        );

        $count = 0;
        foreach ($students as $s) {
            $existing = Database::fetch(
                "SELECT id FROM student_fees WHERE student_id = ? AND fee_structure_id = ?",
                [$s['id'], $structureId]
            );
            if (!$existing) {
                Database::insert('student_fees', [
                    'school_id'        => $schoolId,
                    'student_id'       => $s['id'],
                    'fee_structure_id' => $structureId,
                    'fee_type_id'      => $fs['fee_type_id'],
                    'amount'           => $fs['amount'],
                    'due_date'         => $fs['due_date'],
                    'paid_amount'      => 0,
                    'status'           => 'pending',
                    'created_at'       => date('Y-m-d H:i:s'),
                ]);
                $count++;
            }
        }
        return $count;
    }

    public static function getByClass(int $classId, int $schoolId): array
    {
        return Database::fetchAll(
            "SELECT fs.*, ft.name as fee_type_name
             FROM fee_structures fs
             LEFT JOIN fee_types ft ON fs.fee_type_id = ft.id
             WHERE fs.class_id = ? AND fs.school_id = ? AND fs.status = 'active'",
            [$classId, $schoolId]
        );
    }
}
