<?php
require 'config.php';
require 'core/Database.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    ]);
    
    $cols = [
        "employee_id" => "VARCHAR(50) DEFAULT NULL AFTER email",
        "department" => "VARCHAR(100) DEFAULT NULL AFTER employee_id",
        "designation" => "VARCHAR(100) DEFAULT NULL AFTER department",
        "joining_date" => "DATE DEFAULT NULL AFTER designation",
        "salary" => "DECIMAL(10,2) DEFAULT NULL AFTER joining_date"
    ];

    foreach ($cols as $col => $def) {
        try { $pdo->exec("ALTER TABLE staff ADD COLUMN `$col` $def"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE teachers ADD COLUMN `$col` $def"); } catch (Exception $e) {}
    }
    
    echo "Added staff/teacher columns.";
} catch (Exception $e) {
    echo $e->getMessage();
}
