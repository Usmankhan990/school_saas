<?php
class SchoolSetting {
    public static function get($school_id, $key, $default = null) {
        $pdo = Database::getPdo();
        $stmt = $pdo->prepare("SELECT setting_value FROM school_settings WHERE school_id = ? AND setting_key = ?");
        $stmt->execute([$school_id, $key]);
        $row = $stmt->fetch();
        return $row ? $row['setting_value'] : $default;
    }

    public static function set($school_id, $key, $value) {
        $pdo = Database::getPdo();
        $stmt = $pdo->prepare("
            INSERT INTO school_settings (school_id, setting_key, setting_value) 
            VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE setting_value = ?
        ");
        return $stmt->execute([$school_id, $key, $value, $value]);
    }

    public static function getAll($school_id) {
        $pdo = Database::getPdo();
        $stmt = $pdo->prepare("SELECT setting_key, setting_value FROM school_settings WHERE school_id = ?");
        $stmt->execute([$school_id]);
        $settings = [];
        foreach ($stmt->fetchAll() as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }
}
