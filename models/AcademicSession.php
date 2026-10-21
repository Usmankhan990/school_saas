<?php
class AcademicSession {
    public static function getAllForSchool($school_id) {
        $pdo = Database::getPdo();
        $stmt = $pdo->prepare("SELECT * FROM academic_sessions WHERE school_id = ? ORDER BY start_date DESC");
        $stmt->execute([$school_id]);
        return $stmt->fetchAll();
    }

    public static function getById($id, $school_id) {
        $pdo = Database::getPdo();
        $stmt = $pdo->prepare("SELECT * FROM academic_sessions WHERE id = ? AND school_id = ?");
        $stmt->execute([$id, $school_id]);
        return $stmt->fetch();
    }

    public static function getCurrentForSchool($school_id) {
        $pdo = Database::getPdo();
        $stmt = $pdo->prepare("SELECT * FROM academic_sessions WHERE school_id = ? AND is_current = 1");
        $stmt->execute([$school_id]);
        return $stmt->fetch();
    }

    public static function create($data) {
        $pdo = Database::getPdo();
        
        // If this is set as current, unset others first
        if (!empty($data['is_current'])) {
            $stmt = $pdo->prepare("UPDATE academic_sessions SET is_current = 0 WHERE school_id = ?");
            $stmt->execute([$data['school_id']]);
        }
        
        $sql = "INSERT INTO academic_sessions (school_id, name, start_date, end_date, is_current, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['school_id'],
            $data['name'],
            $data['start_date'],
            $data['end_date'],
            $data['is_current'] ?? 0
        ]);
        return $pdo->lastInsertId();
    }

    public static function update($id, $school_id, $data) {
        $pdo = Database::getPdo();
        
        // If this is set as current, unset others first
        if (!empty($data['is_current'])) {
            $stmt = $pdo->prepare("UPDATE academic_sessions SET is_current = 0 WHERE school_id = ? AND id != ?");
            $stmt->execute([$school_id, $id]);
        }

        $sql = "UPDATE academic_sessions 
                SET name = ?, start_date = ?, end_date = ?, is_current = ?, updated_at = NOW() 
                WHERE id = ? AND school_id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $data['name'],
            $data['start_date'],
            $data['end_date'],
            $data['is_current'] ?? 0,
            $id,
            $school_id
        ]);
    }
}
