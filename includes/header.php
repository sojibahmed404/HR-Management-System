<?php
/**
 * Global Header Component
 * Apex HR Management System
 */
if (!isset($page_title)) {
    $page_title = 'HR Dashboard';
}
if (!isset($breadcrumb)) {
    $breadcrumb = 'Overview';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($page_title); ?> | Apex HR Management</title>

  <!-- Google Font: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Font Awesome 6.5.1 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Master HR Stylesheet -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="app-wrapper">
  <!-- Include Sidebar -->
  <?php include_once __DIR__ . '/sidebar.php'; ?>

  <!-- Main Content Wrapper -->
  <main class="app-main">
    <!-- Topbar -->
    <header class="app-topbar">
      <div class="topbar-left">
        <button type="button" id="toggleSidebar" class="btn-icon d-lg-none" aria-label="Toggle Sidebar" style="display:none;">
          <i class="fa-solid fa-bars"></i>
        </button>
        <div class="page-title">
          <h1><?php echo htmlspecialchars($page_title); ?></h1>
          <div class="page-breadcrumb">
            <span>Apex HRM</span>
            <span>/</span>
            <span style="color:var(--primary); font-weight:600;"><?php echo htmlspecialchars($breadcrumb); ?></span>
          </div>
        </div>
      </div>

      <div class="topbar-right">
        <div class="db-pill">
          <span class="pulse-dot"></span>
          <span>Database: hr_db</span>
        </div>
        <a href="add_employee.php" class="btn-primary-custom">
          <i class="fa-solid fa-plus"></i>
          <span>Add Employee</span>
        </a>
      </div>
    </header>

    <div class="content-body">

