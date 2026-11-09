<?php
/**
 * Database Connection & Global Helper Functions
 * Apex HR & Payroll Management System
 */

// Database Credentials
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'hr_db');

$conn = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("<div style='max-width:600px;margin:50px auto;font-family:Segoe UI,sans-serif;padding:24px;background:#fef2f2;border:1px solid #f87171;border-radius:12px;box-shadow:0 10px 25px rgba(0,0,0,0.05);'>
        <h3 style='color:#991b1b;margin-bottom:10px;'>❌ Database Connection Failed</h3>
        <p style='color:#7f1d1d;font-size:14px;line-height:1.5;'><b>Error:</b> " . htmlspecialchars(mysqli_connect_error()) . "</p>
        <p style='color:#7f1d1d;font-size:13px;margin-top:12px;'>Please ensure XAMPP MySQL is active and <b>hr_db</b> database exists.</p>
    </div>");
}

mysqli_set_charset($conn, "utf8mb4");

/**
 * Format Currency (BDT / Tk)
 */
function format_bdt($amount) {
    return '৳ ' . number_format((float)$amount, 2);
}

/**
 * Get Global HR Dashboard Metrics
 */
function get_hr_metrics($conn) {
    $metrics = [
        'total_employees'   => 0,
        'total_departments' => 0,
        'total_payroll'     => 0,
        'present_today'     => 0,
        'absent_today'      => 0,
        'leave_today'       => 0,
        'attendance_rate'   => 0
    ];

    // Total Employees
    $res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM Employee");
    if ($res && $row = mysqli_fetch_assoc($res)) {
        $metrics['total_employees'] = (int)$row['cnt'];
    }

    // Total Departments
    $res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM Department");
    if ($res && $row = mysqli_fetch_assoc($res)) {
        $metrics['total_departments'] = (int)$row['cnt'];
    }

    // Total Payroll (Basic + Bonus)
    $res = mysqli_query($conn, "SELECT SUM(basic_salary + bonus) as total_pay FROM Salary");
    if ($res && $row = mysqli_fetch_assoc($res)) {
        $metrics['total_payroll'] = (float)($row['total_pay'] ?? 0);
    }

    // Today's Attendance
    $today = date('Y-m-d');
    $res = mysqli_query($conn, "SELECT 
        SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present_cnt,
        SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) as absent_cnt,
        SUM(CASE WHEN status = 'Leave' THEN 1 ELSE 0 END) as leave_cnt
        FROM Attendance WHERE attendance_date = '$today'");
    if ($res && $row = mysqli_fetch_assoc($res)) {
        $metrics['present_today'] = (int)$row['present_cnt'];
        $metrics['absent_today']  = (int)$row['absent_cnt'];
        $metrics['leave_today']   = (int)$row['leave_cnt'];
    }

    if ($metrics['total_employees'] > 0) {
        $metrics['attendance_rate'] = round(($metrics['present_today'] / $metrics['total_employees']) * 100);
    }

    return $metrics;
}
?>