<?php
require 'config.php';
require 'core/Database.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // 1. academic_sessions
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS `academic_sessions` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `name` varchar(255) NOT NULL,
        `start_date` date NOT NULL,
        `end_date` date NOT NULL,
        `is_current` tinyint(1) DEFAULT 0,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    ) ENGINE=InnoDB;
    ");

    // 2. parents
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS `parents` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `name` varchar(255) NOT NULL,
        `phone` varchar(50) DEFAULT NULL,
        `email` varchar(255) DEFAULT NULL,
        `address` text DEFAULT NULL,
        `occupation` varchar(255) DEFAULT NULL,
        `cnic` varchar(50) DEFAULT NULL,
        `emergency_contact` varchar(255) DEFAULT NULL,
        `user_id` int NULL,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    ) ENGINE=InnoDB;
    ");

    // 3. teacher_assignments
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS `teacher_assignments` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `teacher_id` int NOT NULL,
        `class_id` int NOT NULL,
        `section_id` int NOT NULL,
        `subject_id` int NOT NULL,
        `academic_session_id` int NULL,
        `created_at` datetime DEFAULT NULL
    ) ENGINE=InnoDB;
    ");

    // 4. timetables
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS `timetables` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `class_id` int NOT NULL,
        `section_id` int NOT NULL,
        `subject_id` int NOT NULL,
        `teacher_id` int NOT NULL,
        `day_of_week` varchar(20) NOT NULL,
        `start_time` time NOT NULL,
        `end_time` time NOT NULL,
        `room` varchar(100) DEFAULT NULL,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    ) ENGINE=InnoDB;
    ");

    // 5. staff_attendance
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS `staff_attendance` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `staff_id` int NOT NULL,
        `date` date NOT NULL,
        `status` varchar(20) NOT NULL,
        `remarks` text DEFAULT NULL,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    ) ENGINE=InnoDB;
    ");

    // 6. homeworks
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS `homeworks` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `class_id` int NOT NULL,
        `section_id` int NOT NULL,
        `subject_id` int NOT NULL,
        `teacher_id` int NOT NULL,
        `title` varchar(255) NOT NULL,
        `description` text NOT NULL,
        `assigned_date` date NOT NULL,
        `due_date` date NOT NULL,
        `attachment_url` varchar(255) DEFAULT NULL,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    ) ENGINE=InnoDB;
    ");

    // 7. leaves
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS `leaves` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `user_type` varchar(50) NOT NULL,
        `user_id` int NOT NULL,
        `from_date` date NOT NULL,
        `to_date` date NOT NULL,
        `reason` text NOT NULL,
        `status` varchar(20) DEFAULT 'pending',
        `approved_by` int NULL,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    ) ENGINE=InnoDB;
    ");

    // 8. calendar_events
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS `calendar_events` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `title` varchar(255) NOT NULL,
        `type` varchar(50) NOT NULL,
        `start_date` date NOT NULL,
        `end_date` date NOT NULL,
        `description` text DEFAULT NULL,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    ) ENGINE=InnoDB;
    ");

    // 9. grades
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS `grades` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `name` varchar(50) NOT NULL,
        `min_percentage` decimal(5,2) NOT NULL,
        `max_percentage` decimal(5,2) NOT NULL,
        `remarks` varchar(255) DEFAULT NULL,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    ) ENGINE=InnoDB;
    ");

    // 10. school_settings
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS `school_settings` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `setting_key` varchar(100) NOT NULL,
        `setting_value` text DEFAULT NULL,
        UNIQUE KEY `unique_setting` (`school_id`, `setting_key`)
    ) ENGINE=InnoDB;
    ");

    // 11. Modify students table
    $studentColumns = [
        "roll_no" => "VARCHAR(50) DEFAULT NULL AFTER admission_no",
        "academic_session_id" => "INT NULL AFTER roll_no",
        "parent_id" => "INT NULL AFTER academic_session_id",
        "admission_status" => "VARCHAR(20) DEFAULT 'enrolled' AFTER parent_id",
        "admission_date" => "DATE NULL AFTER admission_status",
        "previous_school" => "VARCHAR(255) DEFAULT NULL AFTER admission_date",
        "previous_class" => "VARCHAR(100) DEFAULT NULL AFTER previous_school",
        "emergency_contact" => "VARCHAR(255) DEFAULT NULL AFTER previous_class",
        "photo_url" => "VARCHAR(255) DEFAULT NULL AFTER emergency_contact",
        "documents_json" => "TEXT DEFAULT NULL AFTER photo_url",
        "notes" => "TEXT DEFAULT NULL AFTER documents_json"
    ];

    foreach ($studentColumns as $colName => $def) {
        try {
            $pdo->exec("ALTER TABLE `students` ADD COLUMN `$colName` $def");
            echo "Added $colName to students\n";
        } catch (Exception $e) {
            // Might already exist
        }
    }

    echo "Database migrations applied successfully.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
