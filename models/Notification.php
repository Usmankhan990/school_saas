<?php
/**
 * Notification Model
 */
class Notification
{
    public static function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return Database::insert('notifications', $data);
    }

    public static function allForUser(int $userId, int $schoolId): array
    {
        return Database::fetchAll(
            "SELECT * FROM notifications WHERE user_id = ? AND school_id = ? ORDER BY created_at DESC LIMIT 50",
            [$userId, $schoolId]
        );
    }

    public static function unreadCount(int $userId, int $schoolId): int
    {
        $row = Database::fetch(
            "SELECT COUNT(*) as cnt FROM notifications WHERE user_id = ? AND school_id = ? AND is_read = 0",
            [$userId, $schoolId]
        );
        return (int) $row['cnt'];
    }

    public static function markRead(int $id, int $userId): int
    {
        return Database::update('notifications', ['is_read' => 1], 'id = ? AND user_id = ?', [$id, $userId]);
    }

    public static function markAllRead(int $userId, int $schoolId): int
    {
        return Database::update('notifications', ['is_read' => 1], 'user_id = ? AND school_id = ?', [$userId, $schoolId]);
    }

    /**
     * Notify all students in a class
     */
    public static function notifyClass(int $schoolId, int $classId, string $title, string $body, string $type = 'general'): void
    {
        $students = Database::fetchAll(
            "SELECT s.user_id FROM students s WHERE s.school_id = ? AND s.class_id = ? AND s.user_id IS NOT NULL",
            [$schoolId, $classId]
        );
        foreach ($students as $s) {
            if ($s['user_id']) {
                self::create([
                    'school_id' => $schoolId,
                    'user_id'   => $s['user_id'],
                    'title'     => $title,
                    'body'      => $body,
                    'type'      => $type,
                ]);
            }
        }
    }

    /**
     * Notify all users in a school
     */
    public static function notifySchool(int $schoolId, string $title, string $body, string $type = 'general'): void
    {
        $users = Database::fetchAll(
            "SELECT id FROM users WHERE school_id = ? AND status = 'active'",
            [$schoolId]
        );
        foreach ($users as $u) {
            self::create([
                'school_id' => $schoolId,
                'user_id'   => $u['id'],
                'title'     => $title,
                'body'      => $body,
                'type'      => $type,
            ]);
        }
    }
}
