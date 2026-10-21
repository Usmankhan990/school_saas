<?php
require_once 'config.php';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';charset=' . DB_CHARSET, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`");
    $pdo->exec("USE `" . DB_NAME . "`");

        'calendar_events',
        'leaves',
        'homeworks',
        'staff_attendance',
        'timetables',
        'teacher_assignments',
        'parents',
        'academic_sessions',
        'school_settings',
        'grades',
        'audit_logs',
        'attendance_students',
        'exam_marks',
        'payments',
        'class_participants',
        'online_classes',
        'notifications',
        'late_fee_rules',
        'student_fees',
        'fee_structures',
        'fee_types',
        'exams',
        'date_sheets',
        'subjects',
        'teachers',
        'students',
        'staff',
        'sections',
        'classes',
        'users',
        'schools'
    ];

    foreach ($tables as $t) {
        $pdo->exec("DROP TABLE IF EXISTS `$t`");
    }

    $sql = "
    CREATE TABLE `schools` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `name` varchar(255) NOT NULL,
        `slug` varchar(255) NOT NULL,
        `address` text DEFAULT NULL,
        `phone` varchar(50) DEFAULT NULL,
        `principal_name` varchar(255) DEFAULT NULL,
        `currency` varchar(10) DEFAULT 'PKR',
        `logo_url` varchar(255) DEFAULT NULL,
        `status` varchar(50) DEFAULT 'active',
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    );

    CREATE TABLE `users` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NULL,
        `name` varchar(255) NOT NULL,
        `email` varchar(255) NOT NULL UNIQUE,
        `password_hash` varchar(255) NOT NULL,
        `role` varchar(50) NOT NULL,
        `status` varchar(50) DEFAULT 'active',
        `last_login_at` datetime DEFAULT NULL,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    );

    CREATE TABLE `classes` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `name` varchar(255) NOT NULL,
        `numeric_name` int NULL,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    );

    CREATE TABLE `sections` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `class_id` int NOT NULL,
        `name` varchar(255) NOT NULL,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    );

    CREATE TABLE `staff` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `name` varchar(255) NOT NULL,
        `phone` varchar(50) DEFAULT NULL,
        `designation` varchar(255) DEFAULT NULL,
        `user_id` int NULL,
        `status` varchar(50) DEFAULT 'active',
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    );

    CREATE TABLE `students` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `admission_no` varchar(50) NOT NULL,
        `roll_no` varchar(50) DEFAULT NULL,
        `academic_session_id` int NULL,
        `parent_id` int NULL,
        `admission_status` varchar(20) DEFAULT 'enrolled',
        `admission_date` date NULL,
        `previous_school` varchar(255) DEFAULT NULL,
        `previous_class` varchar(100) DEFAULT NULL,
        `emergency_contact` varchar(255) DEFAULT NULL,
        `photo_url` varchar(255) DEFAULT NULL,
        `documents_json` text DEFAULT NULL,
        `notes` text DEFAULT NULL,
        `name` varchar(255) NOT NULL,
        `father_name` varchar(255) DEFAULT NULL,
        `phone` varchar(50) DEFAULT NULL,
        `address` text DEFAULT NULL,
        `class_id` int NULL,
        `section_id` int NULL,
        `dob` date DEFAULT NULL,
        `gender` varchar(20) DEFAULT NULL,
        `status` varchar(50) DEFAULT 'active',
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    );

    CREATE TABLE `teachers` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `name` varchar(255) NOT NULL,
        `phone` varchar(50) DEFAULT NULL,
        `designation` varchar(255) DEFAULT NULL,
        `user_id` int NULL,
        `status` varchar(50) DEFAULT 'active',
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    );

    CREATE TABLE `subjects` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `name` varchar(255) NOT NULL,
        `code` varchar(50) DEFAULT NULL,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    );

    CREATE TABLE `exams` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `name` varchar(255) NOT NULL,
        `type` varchar(50) NOT NULL,
        `class_id` int NULL,
        `start_date` date DEFAULT NULL,
        `end_date` date DEFAULT NULL,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    );

    CREATE TABLE `date_sheets` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `exam_id` int NOT NULL,
        `class_id` int NOT NULL,
        `subject_id` int NOT NULL,
        `exam_date` date NOT NULL,
        `start_time` time NOT NULL,
        `end_time` time NOT NULL,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    );

    CREATE TABLE `fee_types` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `name` varchar(255) NOT NULL,
        `description` text DEFAULT NULL,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    );

    CREATE TABLE `fee_structures` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `fee_type_id` int NOT NULL,
        `class_id` int NOT NULL,
        `amount` decimal(10,2) NOT NULL,
        `due_date` date DEFAULT NULL,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    );

    CREATE TABLE `student_fees` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `student_id` int NOT NULL,
        `fee_structure_id` int NULL,
        `amount` decimal(10,2) NOT NULL,
        `due_date` date DEFAULT NULL,
        `status` varchar(20) DEFAULT 'pending',
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    );

    CREATE TABLE `late_fee_rules` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `days_after_due` int NOT NULL,
        `amount` decimal(10,2) NOT NULL,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    );

    CREATE TABLE `notifications` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `user_id` int NOT NULL,
        `title` varchar(255) NOT NULL,
        `body` text NOT NULL,
        `type` varchar(50) DEFAULT NULL,
        `is_read` tinyint(1) DEFAULT 0,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    );

    CREATE TABLE `online_classes` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `teacher_id` int NOT NULL,
        `class_id` int NOT NULL,
        `section_id` int NOT NULL,
        `title` varchar(255) NOT NULL,
        `description` text DEFAULT NULL,
        `scheduled_at` datetime NOT NULL,
        `duration_minutes` int NOT NULL,
        `meeting_link` text NOT NULL,
        `platform` varchar(100) DEFAULT NULL,
        `status` varchar(50) DEFAULT 'scheduled',
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    );

    CREATE TABLE `class_participants` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `online_class_id` int NOT NULL,
        `student_id` int NOT NULL,
        `joined_at` datetime DEFAULT NULL
    );

    CREATE TABLE `payments` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `student_fee_id` int NOT NULL,
        `student_id` int NOT NULL,
        `amount` decimal(10,2) NOT NULL,
        `payment_method` varchar(50) NOT NULL,
        `transaction_id` varchar(100) DEFAULT NULL,
        `payment_date` date NOT NULL,
        `received_by` int NULL,
        `notes` text DEFAULT NULL,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    );

    CREATE TABLE `exam_marks` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `exam_id` int NOT NULL,
        `student_id` int NOT NULL,
        `subject_id` int NOT NULL,
        `marks_obtained` decimal(5,2) NOT NULL,
        `total_marks` decimal(5,2) NOT NULL,
        `remarks` text DEFAULT NULL,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    );

    CREATE TABLE `attendance_students` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `student_id` int NOT NULL,
        `class_id` int NOT NULL,
        `section_id` int NOT NULL,
        `date` date NOT NULL,
        `status` varchar(20) NOT NULL,
        `remarks` text DEFAULT NULL,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    );

    CREATE TABLE `audit_logs` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NULL,
        `user_id` int NULL,
        `action` varchar(255) NOT NULL,
        `entity` varchar(255) NOT NULL,
        `entity_id` int NULL,
        `meta_json` text DEFAULT NULL,
        `created_at` datetime DEFAULT NULL
    );

    CREATE TABLE `academic_sessions` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `name` varchar(255) NOT NULL,
        `start_date` date NOT NULL,
        `end_date` date NOT NULL,
        `is_current` tinyint(1) DEFAULT 0,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    );

    CREATE TABLE `parents` (
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
    );

    CREATE TABLE `teacher_assignments` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `teacher_id` int NOT NULL,
        `class_id` int NOT NULL,
        `section_id` int NOT NULL,
        `subject_id` int NOT NULL,
        `academic_session_id` int NULL,
        `created_at` datetime DEFAULT NULL
    );

    CREATE TABLE `timetables` (
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
    );

    CREATE TABLE `staff_attendance` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `staff_id` int NOT NULL,
        `date` date NOT NULL,
        `status` varchar(20) NOT NULL,
        `remarks` text DEFAULT NULL,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    );

    CREATE TABLE `homeworks` (
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
    );

    CREATE TABLE `leaves` (
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
    );

    CREATE TABLE `calendar_events` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `title` varchar(255) NOT NULL,
        `type` varchar(50) NOT NULL,
        `start_date` date NOT NULL,
        `end_date` date NOT NULL,
        `description` text DEFAULT NULL,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    );

    CREATE TABLE `grades` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `name` varchar(50) NOT NULL,
        `min_percentage` decimal(5,2) NOT NULL,
        `max_percentage` decimal(5,2) NOT NULL,
        `remarks` varchar(255) DEFAULT NULL,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    );

    CREATE TABLE `school_settings` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `school_id` int NOT NULL,
        `setting_key` varchar(100) NOT NULL,
        `setting_value` text DEFAULT NULL,
        UNIQUE KEY `unique_setting` (`school_id`, `setting_key`)
    );
    ";

    $pdo->exec($sql);
    echo "Full schema created.\n";

    $adminEmail = 'admin@platform.com';
    $adminPass = 'admin123';
    $hash = password_hash($adminPass, PASSWORD_BCRYPT);
    $pdo->exec("INSERT INTO `users` (`name`, `email`, `password_hash`, `role`, `status`, `created_at`) VALUES ('Platform Admin', '$adminEmail', '$hash', 'platform_admin', 'active', NOW())");
    echo "Admin user created.\n";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
