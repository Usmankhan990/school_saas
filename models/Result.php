<?php
/**
 * Result Model – Wraps exam_marks table
 */
class Result
{
    public static function getMarksByExamAndStudent(int $examId, int $studentId): array
    {
        return Database::fetchAll(
            "SELECT em.*, sub.name as subject_name
             FROM exam_marks em
             LEFT JOIN subjects sub ON em.subject_id = sub.id
             WHERE em.exam_id = ? AND em.student_id = ?
             ORDER BY sub.name ASC",
            [$examId, $studentId]
        );
    }

    public static function getMarksByExamAndClass(int $examId, int $classId, int $schoolId): array
    {
        return Database::fetchAll(
            "SELECT em.*, s.name as student_name, s.admission_no, sub.name as subject_name
             FROM exam_marks em
             LEFT JOIN students s ON em.student_id = s.id
             LEFT JOIN subjects sub ON em.subject_id = sub.id
             WHERE em.exam_id = ? AND em.school_id = ? AND s.class_id = ?
             ORDER BY s.name ASC, sub.name ASC",
            [$examId, $schoolId, $classId]
        );
    }

    public static function saveMarks(array $data): int
    {
        // Check if marks already exist
        $existing = Database::fetch(
            "SELECT id FROM exam_marks WHERE exam_id = ? AND student_id = ? AND subject_id = ?",
            [$data['exam_id'], $data['student_id'], $data['subject_id']]
        );

        $data['grade'] = self::calculateGrade($data['marks'], $data['max_marks']);

        if ($existing) {
            Database::update('exam_marks', [
                'marks'    => $data['marks'],
                'max_marks'=> $data['max_marks'],
                'grade'    => $data['grade'],
            ], 'id = ?', [$existing['id']]);
            return $existing['id'];
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        return Database::insert('exam_marks', $data);
    }

    public static function calculateGrade(float $marks, float $maxMarks): string
    {
        if ($maxMarks <= 0) return 'F';
        $pct = ($marks / $maxMarks) * 100;
        if ($pct >= 90) return 'A+';
        if ($pct >= 80) return 'A';
        if ($pct >= 70) return 'B';
        if ($pct >= 60) return 'C';
        if ($pct >= 50) return 'D';
        return 'F';
    }

    public static function calculatePercentage(float $totalMarks, float $totalMaxMarks): float
    {
        if ($totalMaxMarks <= 0) return 0;
        return round(($totalMarks / $totalMaxMarks) * 100, 2);
    }

    public static function getStudentResultSummary(int $examId, int $studentId): array
    {
        $marks = self::getMarksByExamAndStudent($examId, $studentId);
        $totalMarks = 0;
        $totalMax = 0;
        foreach ($marks as $m) {
            $totalMarks += (float)$m['marks'];
            $totalMax += (float)$m['max_marks'];
        }
        return [
            'marks'      => $marks,
            'total'      => $totalMarks,
            'max_total'  => $totalMax,
            'percentage' => self::calculatePercentage($totalMarks, $totalMax),
            'grade'      => self::calculateGrade($totalMarks, $totalMax),
        ];
    }
}
