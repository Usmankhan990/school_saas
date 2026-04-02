<?php
/**
 * OnlineClass Model
 */
class OnlineClass
{
    public static function allForSchool(int $schoolId): array
    {
        return Database::fetchAll(
            "SELECT oc.*, c.name as class_name, sec.name as section_name, t.name as teacher_name
             FROM online_classes oc
             LEFT JOIN classes c ON oc.class_id = c.id
             LEFT JOIN sections sec ON oc.section_id = sec.id
             LEFT JOIN teachers t ON oc.teacher_id = t.id
             WHERE oc.school_id = ?
             ORDER BY oc.scheduled_at DESC",
            [$schoolId]
        );
    }

    public static function allForTeacher(int $teacherId, int $schoolId): array
    {
        return Database::fetchAll(
            "SELECT oc.*, c.name as class_name, sec.name as section_name
             FROM online_classes oc
             LEFT JOIN classes c ON oc.class_id = c.id
             LEFT JOIN sections sec ON oc.section_id = sec.id
             WHERE oc.teacher_id = ? AND oc.school_id = ?
             ORDER BY oc.scheduled_at DESC",
            [$teacherId, $schoolId]
        );
    }

    public static function upcomingForStudent(int $classId, ?int $sectionId, int $schoolId): array
    {
        $sql = "SELECT oc.*, t.name as teacher_name
                FROM online_classes oc
                LEFT JOIN teachers t ON oc.teacher_id = t.id
                WHERE oc.school_id = ? AND oc.class_id = ?
                  AND oc.status IN ('scheduled','live')
                  AND oc.scheduled_at >= NOW()";
        $params = [$schoolId, $classId];
        if ($sectionId) {
            $sql .= " AND (oc.section_id = ? OR oc.section_id IS NULL)";
            $params[] = $sectionId;
        }
        $sql .= " ORDER BY oc.scheduled_at ASC";
        return Database::fetchAll($sql, $params);
    }

    public static function find(int $id): ?array
    {
        return Database::fetch(
            "SELECT oc.*, c.name as class_name, sec.name as section_name, t.name as teacher_name
             FROM online_classes oc
             LEFT JOIN classes c ON oc.class_id = c.id
             LEFT JOIN sections sec ON oc.section_id = sec.id
             LEFT JOIN teachers t ON oc.teacher_id = t.id
             WHERE oc.id = ?",
            [$id]
        );
    }

    public static function findSecure(int $id, int $schoolId): ?array
    {
        return Database::fetch(
            "SELECT oc.*, c.name as class_name, sec.name as section_name, t.name as teacher_name
             FROM online_classes oc
             LEFT JOIN classes c ON oc.class_id = c.id
             LEFT JOIN sections sec ON oc.section_id = sec.id
             LEFT JOIN teachers t ON oc.teacher_id = t.id
             WHERE oc.id = ? AND oc.school_id = ?",
            [$id, $schoolId]
        );
    }

    public static function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return Database::insert('online_classes', $data);
    }

    public static function update(int $id, array $data): int
    {
        return Database::update('online_classes', $data, 'id = ?', [$id]);
    }

    public static function delete(int $id, int $schoolId): int
    {
        return Database::delete('online_classes', 'id = ? AND school_id = ?', [$id, $schoolId]);
    }

    public static function addParticipant(int $onlineClassId, int $studentId): int
    {
        // Check if already joined
        $existing = Database::fetch(
            "SELECT id FROM class_participants WHERE online_class_id = ? AND student_id = ?",
            [$onlineClassId, $studentId]
        );
        if ($existing) return $existing['id'];

        return Database::insert('class_participants', [
            'online_class_id' => $onlineClassId,
            'student_id'      => $studentId,
            'joined_at'       => date('Y-m-d H:i:s'),
        ]);
    }

    public static function getParticipants(int $onlineClassId): array
    {
        return Database::fetchAll(
            "SELECT cp.*, s.name as student_name, s.admission_no
             FROM class_participants cp
             LEFT JOIN students s ON cp.student_id = s.id
             WHERE cp.online_class_id = ?
             ORDER BY cp.joined_at ASC",
            [$onlineClassId]
        );
    }
}
