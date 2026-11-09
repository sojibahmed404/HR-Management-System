<?php
/**
 * Delete Employee Controller
 * Apex HR Management System
 */
require_once 'db.php';

$emp_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($emp_id > 0) {
    // Delete employee (child records in Salary and Attendance cascade automatically)
    $sql = "DELETE FROM Employee WHERE employee_id = $emp_id";
    mysqli_query($conn, $sql);
}

// Redirect back to employees list
header('Location: employees.php?deleted=1');
exit;
?>
