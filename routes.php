<?php
/**
 * Routes Definition
 * $router is available from index.php
 */

// ═══════════════════════════════════════════════
//  PUBLIC / GUEST ROUTES
// ═══════════════════════════════════════════════

// Home – redirect to login
$router->get('', 'AuthController@showLogin', ['guest']);

// Platform admin login
$router->get('login', 'AuthController@showLogin', ['guest']);
$router->post('login', 'AuthController@login', ['csrf']);

// School registration
$router->get('register', 'RegisterController@showForm', ['guest']);
$router->post('register', 'RegisterController@register', ['csrf']);

// ═══════════════════════════════════════════════
//  PLATFORM ADMIN ROUTES
// ═══════════════════════════════════════════════

$router->get('schools', 'SchoolController@index', ['platform_admin']);
$router->get('schools/create', 'SchoolController@create', ['platform_admin']);
$router->post('schools/store', 'SchoolController@store', ['platform_admin', 'csrf']);
$router->get('schools/{id}/edit', 'SchoolController@edit', ['platform_admin']);
$router->post('schools/{id}/update', 'SchoolController@update', ['platform_admin', 'csrf']);
$router->post('schools/{id}/toggle', 'SchoolController@toggle', ['platform_admin', 'csrf']);
$router->get('platform/logout', 'AuthController@logout');
$router->get('finance/global', 'FinanceController@globalDashboard', ['platform_admin']);

// ═══════════════════════════════════════════════
//  TENANT ROUTES (/{slug}/...)
// ═══════════════════════════════════════════════

// School login/logout
$router->get('{slug}/login', 'AuthController@showSchoolLogin', ['tenant']);
$router->post('{slug}/login', 'AuthController@schoolLogin', ['tenant', 'csrf']);
$router->get('{slug}/logout', 'AuthController@logout');

// Dashboard
$router->get('{slug}/dashboard', 'DashboardController@index', ['tenant', 'auth']);

// School settings
$router->get('{slug}/settings', 'SettingsController@index', ['tenant', 'auth']);
$router->post('{slug}/settings', 'SettingsController@update', ['tenant', 'auth', 'csrf']);

// ── Users ──
$router->get('{slug}/users', 'UserController@index', ['tenant', 'auth']);
$router->get('{slug}/users/create', 'UserController@create', ['tenant', 'auth']);
$router->post('{slug}/users/store', 'UserController@store', ['tenant', 'auth', 'csrf']);
$router->get('{slug}/users/{id}/edit', 'UserController@edit', ['tenant', 'auth']);
$router->post('{slug}/users/{id}/update', 'UserController@update', ['tenant', 'auth', 'csrf']);
$router->post('{slug}/users/{id}/toggle', 'UserController@toggle', ['tenant', 'auth', 'csrf']);
$router->post('{slug}/users/{id}/reset-password', 'UserController@resetPassword', ['tenant', 'auth', 'csrf']);

// ── Students ──
$router->get('{slug}/students', 'StudentController@index', ['tenant', 'auth']);
$router->get('{slug}/students/create', 'StudentController@create', ['tenant', 'auth']);
$router->post('{slug}/students/store', 'StudentController@store', ['tenant', 'auth', 'csrf']);
$router->get('{slug}/students/{id}/edit', 'StudentController@edit', ['tenant', 'auth']);
$router->post('{slug}/students/{id}/update', 'StudentController@update', ['tenant', 'auth', 'csrf']);
$router->post('{slug}/students/{id}/delete', 'StudentController@delete', ['tenant', 'auth', 'csrf']);

// ── Teachers ──
$router->get('{slug}/teachers', 'TeacherController@index', ['tenant', 'auth']);
$router->get('{slug}/teachers/create', 'TeacherController@create', ['tenant', 'auth']);
$router->post('{slug}/teachers/store', 'TeacherController@store', ['tenant', 'auth', 'csrf']);
$router->get('{slug}/teachers/{id}/edit', 'TeacherController@edit', ['tenant', 'auth']);
$router->post('{slug}/teachers/{id}/update', 'TeacherController@update', ['tenant', 'auth', 'csrf']);
$router->post('{slug}/teachers/{id}/delete', 'TeacherController@delete', ['tenant', 'auth', 'csrf']);

// ── Staff ──
$router->get('{slug}/staff', 'StaffController@index', ['tenant', 'auth']);
$router->get('{slug}/staff/create', 'StaffController@create', ['tenant', 'auth']);
$router->post('{slug}/staff/store', 'StaffController@store', ['tenant', 'auth', 'csrf']);
$router->get('{slug}/staff/{id}/edit', 'StaffController@edit', ['tenant', 'auth']);
$router->post('{slug}/staff/{id}/update', 'StaffController@update', ['tenant', 'auth', 'csrf']);
$router->post('{slug}/staff/{id}/delete', 'StaffController@delete', ['tenant', 'auth', 'csrf']);

// ── Classes ──
$router->get('{slug}/classes', 'ClassController@index', ['tenant', 'auth']);
$router->get('{slug}/classes/create', 'ClassController@create', ['tenant', 'auth']);
$router->post('{slug}/classes/store', 'ClassController@store', ['tenant', 'auth', 'csrf']);
$router->get('{slug}/classes/{id}/edit', 'ClassController@edit', ['tenant', 'auth']);
$router->post('{slug}/classes/{id}/update', 'ClassController@update', ['tenant', 'auth', 'csrf']);
$router->post('{slug}/classes/{id}/delete', 'ClassController@delete', ['tenant', 'auth', 'csrf']);

// ── Sections ──
$router->get('{slug}/sections', 'SectionController@index', ['tenant', 'auth']);
$router->post('{slug}/sections/store', 'SectionController@store', ['tenant', 'auth', 'csrf']);
$router->post('{slug}/sections/{id}/update', 'SectionController@update', ['tenant', 'auth', 'csrf']);
$router->post('{slug}/sections/{id}/delete', 'SectionController@delete', ['tenant', 'auth', 'csrf']);

// ── Subjects ──
$router->get('{slug}/subjects', 'SubjectController@index', ['tenant', 'auth']);
$router->get('{slug}/subjects/create', 'SubjectController@create', ['tenant', 'auth']);
$router->post('{slug}/subjects/store', 'SubjectController@store', ['tenant', 'auth', 'csrf']);
$router->get('{slug}/subjects/{id}/edit', 'SubjectController@edit', ['tenant', 'auth']);
$router->post('{slug}/subjects/{id}/update', 'SubjectController@update', ['tenant', 'auth', 'csrf']);
$router->post('{slug}/subjects/{id}/delete', 'SubjectController@delete', ['tenant', 'auth', 'csrf']);

// ── Exams ──
$router->get('{slug}/exams', 'ExamController@index', ['tenant', 'auth']);
$router->get('{slug}/exams/create', 'ExamController@create', ['tenant', 'auth']);
$router->post('{slug}/exams/store', 'ExamController@store', ['tenant', 'auth', 'csrf']);
$router->get('{slug}/exams/{id}/edit', 'ExamController@edit', ['tenant', 'auth']);
$router->post('{slug}/exams/{id}/update', 'ExamController@update', ['tenant', 'auth', 'csrf']);
$router->post('{slug}/exams/{id}/delete', 'ExamController@delete', ['tenant', 'auth', 'csrf']);

// ── Online Classes ──
$router->get('{slug}/online-classes', 'OnlineClassController@index', ['tenant', 'auth']);
$router->get('{slug}/online-classes/create', 'OnlineClassController@create', ['tenant', 'auth']);
$router->post('{slug}/online-classes/store', 'OnlineClassController@store', ['tenant', 'auth', 'csrf']);
$router->get('{slug}/online-classes/{id}/join', 'OnlineClassController@join', ['tenant', 'auth']);
$router->post('{slug}/online-classes/{id}/status', 'OnlineClassController@updateStatus', ['tenant', 'auth', 'csrf']);
$router->get('{slug}/online-classes/{id}/participants', 'OnlineClassController@participants', ['tenant', 'auth']);

// ── Attendance ──
$router->get('{slug}/attendance', 'AttendanceController@index', ['tenant', 'auth']);
$router->get('{slug}/attendance/mark', 'AttendanceController@mark', ['tenant', 'auth']);
$router->post('{slug}/attendance/store', 'AttendanceController@store', ['tenant', 'auth', 'csrf']);
$router->get('{slug}/attendance/daily-report', 'AttendanceController@dailyReport', ['tenant', 'auth']);
$router->get('{slug}/attendance/monthly-report', 'AttendanceController@monthlyReport', ['tenant', 'auth']);

// ── Date Sheets ──
$router->get('{slug}/datesheets', 'DateSheetController@index', ['tenant', 'auth']);
$router->get('{slug}/datesheets/create', 'DateSheetController@create', ['tenant', 'auth']);
$router->post('{slug}/datesheets/store', 'DateSheetController@store', ['tenant', 'auth', 'csrf']);
$router->get('{slug}/datesheets/{id}/view', 'DateSheetController@view_sheet', ['tenant', 'auth']);
$router->get('{slug}/datesheets/{id}/pdf', 'DateSheetController@downloadPdf', ['tenant', 'auth']);
$router->post('{slug}/datesheets/{id}/delete', 'DateSheetController@delete', ['tenant', 'auth', 'csrf']);

// ── Results ──
$router->get('{slug}/results', 'ResultController@index', ['tenant', 'auth']);
$router->get('{slug}/results/{id}/enter-marks', 'ResultController@enterMarks', ['tenant', 'auth']);
$router->post('{slug}/results/store-marks', 'ResultController@storeMarks', ['tenant', 'auth', 'csrf']);
$router->get('{slug}/results/{id}/student/{studentId}', 'ResultController@viewResult', ['tenant', 'auth']);
$router->get('{slug}/results/{id}/student/{studentId}/pdf', 'ResultController@downloadPdf', ['tenant', 'auth']);

// ── Notifications ──
$router->get('{slug}/notifications', 'NotificationController@index', ['tenant', 'auth']);
$router->post('{slug}/notifications/{id}/read', 'NotificationController@markRead', ['tenant', 'auth', 'csrf']);
$router->post('{slug}/notifications/mark-all-read', 'NotificationController@markAllRead', ['tenant', 'auth', 'csrf']);

// ══════════════════════════════════════════════════
//  FEES & FINANCE MODULE
// ══════════════════════════════════════════════════

// ── Fee Types ──
$router->get('{slug}/fees/types', 'FeeController@types', ['tenant', 'auth']);
$router->get('{slug}/fees/types/create', 'FeeController@createType', ['tenant', 'auth']);
$router->post('{slug}/fees/types/store', 'FeeController@storeType', ['tenant', 'auth', 'csrf']);
$router->get('{slug}/fees/types/{id}/edit', 'FeeController@editType', ['tenant', 'auth']);
$router->post('{slug}/fees/types/{id}/update', 'FeeController@updateType', ['tenant', 'auth', 'csrf']);
$router->post('{slug}/fees/types/{id}/delete', 'FeeController@deleteType', ['tenant', 'auth', 'csrf']);

// ── Fee Structures ──
$router->get('{slug}/fees/structures', 'FeeController@structures', ['tenant', 'auth']);
$router->get('{slug}/fees/structures/create', 'FeeController@createStructure', ['tenant', 'auth']);
$router->post('{slug}/fees/structures/store', 'FeeController@storeStructure', ['tenant', 'auth', 'csrf']);
$router->post('{slug}/fees/structures/{id}/assign', 'FeeController@assignStructure', ['tenant', 'auth', 'csrf']);
$router->post('{slug}/fees/structures/{id}/delete', 'FeeController@deleteStructure', ['tenant', 'auth', 'csrf']);

// ── Student Fees ──
$router->get('{slug}/fees/student-fees', 'FeeController@studentFees', ['tenant', 'auth']);
$router->get('{slug}/fees/defaulters', 'FeeController@defaulters', ['tenant', 'auth']);

// ── Payments ──
$router->get('{slug}/payments', 'PaymentController@index', ['tenant', 'auth']);
$router->get('{slug}/payments/{id}/pay', 'PaymentController@create', ['tenant', 'auth']);
$router->post('{slug}/payments/store', 'PaymentController@store', ['tenant', 'auth', 'csrf']);
$router->get('{slug}/payments/{id}/receipt', 'PaymentController@receipt', ['tenant', 'auth']);
$router->get('{slug}/payments/{id}/receipt-pdf', 'PaymentController@receiptPdf', ['tenant', 'auth']);
$router->get('{slug}/payments/history', 'PaymentController@history', ['tenant', 'auth']);

// ── Finance Dashboard ──
$router->get('{slug}/finance', 'FinanceController@schoolDashboard', ['tenant', 'auth']);

// ── Reports ──
$router->get('{slug}/reports/fees', 'ReportController@feeReport', ['tenant', 'auth']);
$router->get('{slug}/reports/class/{id}', 'ReportController@classReport', ['tenant', 'auth']);
$router->get('{slug}/reports/defaulters', 'ReportController@defaulters', ['tenant', 'auth']);
$router->get('{slug}/reports/fee-csv', 'ReportController@exportCsv', ['tenant', 'auth']);
$router->get('{slug}/reports/defaulters/csv', 'ReportController@exportDefaultersCsv', ['tenant', 'auth']);
$router->get('{slug}/reports/defaulters/pdf', 'ReportController@exportDefaultersPdf', ['tenant', 'auth']);
