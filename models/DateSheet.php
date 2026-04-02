<?php
/**
 * DateSheet Model
 */
class DateSheet
{
    public static function allForExam(int $examId, int $schoolId): array
    {
        return Database::fetchAll(
            "SELECT ds.*, sub.name as subject_name, c.name as class_name
             FROM date_sheets ds
             LEFT JOIN subjects sub ON ds.subject_id = sub.id
             LEFT JOIN classes c ON ds.class_id = c.id
             WHERE ds.exam_id = ? AND ds.school_id = ?
             ORDER BY ds.exam_date ASC, ds.start_time ASC",
            [$examId, $schoolId]
        );
    }

    public static function allForSchool(int $schoolId): array
    {
        return Database::fetchAll(
            "SELECT ds.*, sub.name as subject_name, c.name as class_name, e.name as exam_name
             FROM date_sheets ds
             LEFT JOIN subjects sub ON ds.subject_id = sub.id
             LEFT JOIN classes c ON ds.class_id = c.id
             LEFT JOIN exams e ON ds.exam_id = e.id
             WHERE ds.school_id = ?
             ORDER BY ds.exam_date ASC",
            [$schoolId]
        );
    }

    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM date_sheets WHERE id = ?", [$id]);
    }

    public static function findSecure(int $id, int $schoolId): ?array
    {
        return Database::fetch(
            "SELECT * FROM date_sheets WHERE id = ? AND school_id = ?",
            [$id, $schoolId]
        );
    }

    public static function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return Database::insert('date_sheets', $data);
    }

    public static function delete(int $id, int $schoolId): int
    {
        return Database::delete('date_sheets', 'id = ? AND school_id = ?', [$id, $schoolId]);
    }

    public static function getByExamAndClass(int $examId, int $classId, int $schoolId): array
    {
        return Database::fetchAll(
            "SELECT ds.*, sub.name as subject_name
             FROM date_sheets ds
             LEFT JOIN subjects sub ON ds.subject_id = sub.id
             WHERE ds.exam_id = ? AND ds.class_id = ? AND ds.school_id = ?
             ORDER BY ds.exam_date ASC, ds.start_time ASC",
            [$examId, $classId, $schoolId]
        );
    }
}
