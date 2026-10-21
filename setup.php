<?php
require_once 'config.php';

try {
    // Connect to MySQL server (without specifying database)
    $pdo = new PDO('mysql:host=' . DB_HOST . ';charset=' . DB_CHARSET, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`");
    echo "Database created/verified.\n";

    // Switch to database
    $pdo->exec("USE `" . DB_NAME . "`");

    // Create schools table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `schools` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `name` varchar(255) NOT NULL,
        `slug` varchar(255) NOT NULL,
        `phone` varchar(50) DEFAULT NULL,
        `logo_url` varchar(255) DEFAULT NULL,
        `status` varchar(50) DEFAULT 'active',
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL
    )");
    echo "Table 'schools' created/verified.\n";

    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
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
    )");
    echo "Table 'users' created/verified.\n";

    // Insert platform admin
    $adminEmail = 'admin@platform.com';
    $adminPass = 'admin123';
    $hash = password_hash($adminPass, PASSWORD_BCRYPT);
    
    $stmt = $pdo->prepare("SELECT id FROM `users` WHERE `email` = ?");
    $stmt->execute([$adminEmail]);
    if (!$stmt->fetch()) {
        $insert = $pdo->prepare("INSERT INTO `users` (`name`, `email`, `password_hash`, `role`, `status`, `created_at`) VALUES (?, ?, ?, ?, ?, ?)");
        $insert->execute(['Platform Admin', $adminEmail, $hash, 'platform_admin', 'active', date('Y-m-d H:i:s')]);
        echo "Admin user created: {$adminEmail} / {$adminPass}\n";
    } else {
        echo "Admin user already exists.\n";
    }

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
