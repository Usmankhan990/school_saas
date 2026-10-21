<?php
require_once 'config.php';
require_once 'core/Database.php';

// Auto-load models for testing
spl_autoload_register(function ($class) {
    if (file_exists('models/' . $class . '.php')) {
        require_once 'models/' . $class . '.php';
    }
});

echo "Starting tests...\n";

try {
    // 1. Create School
    $schoolId = School::create([
        'name'     => 'Test Flow School',
        'slug'     => 'testflow',
        'address'  => '123 Test St',
        'phone'    => '555-1234',
        'principal_name' => 'Mr. Principal',
        'currency' => 'PKR',
        'status'   => 'active',
    ]);
    echo "School created: $schoolId\n";

    // 2. Create User
    $userId = User::create([
        'school_id'     => $schoolId,
        'name'          => 'School Admin',
        'email'         => 'admin@testflow.com',
        'password_hash' => password_hash('password', PASSWORD_BCRYPT),
        'role'          => 'super_admin',
        'status'        => 'active',
    ]);
    echo "User created: $userId\n";

    // 3. Create Class
    $classId = ClassModel::create([
        'school_id'    => $schoolId,
        'name'         => 'Class 1',
        'numeric_name' => 1,
    ]);
    echo "Class created: $classId\n";

    // 4. Create Section
    $sectionId = SectionModel::create([
        'school_id' => $schoolId,
        'class_id'  => $classId,
        'name'      => 'A',
    ]);
    echo "Section created: $sectionId\n";

    // 5. Create Subject
    $subjectId = Subject::create([
        'school_id' => $schoolId,
        'name'      => 'Math',
        'code'      => 'MTH101',
    ]);
    echo "Subject created: $subjectId\n";

    // 6. Create Student
    $studentId = Student::create([
        'school_id'    => $schoolId,
        'admission_no' => 'ADM-001',
        'name'         => 'John Doe',
        'father_name'  => 'Jane Doe',
        'phone'        => '555-0000',
        'address'      => 'Somewhere',
        'class_id'     => $classId,
        'section_id'   => $sectionId,
        'dob'          => '2010-01-01',
        'gender'       => 'male',
    ]);
    echo "Student created: $studentId\n";

    // 7. Create FeeType
    $feeTypeId = FeeType::create([
        'school_id'   => $schoolId,
        'name'        => 'Tuition',
        'description' => 'Monthly Tuition',
    ]);
    echo "FeeType created: $feeTypeId\n";

    echo "\nAll core entity creations passed!\n";
} catch (Exception $e) {
    echo "\nTEST FAILED: " . $e->getMessage() . "\n";
}
