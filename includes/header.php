<?php
/**
 * Global Header Component
 * NexByte Technologies Ltd.
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
  <title><?php echo htmlspecialchars($page_title); ?> | NexByte Technologies Ltd.</title>

  <!-- Web Icon / Favicon -->
  <link rel="icon" type="image/png" href="assets/images/favicon.png">

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
    <!-- Sleek Formatted Topbar -->
    <header class="app-topbar">
      <div class="topbar-left">
        <button type="button" id="toggleSidebar" class="btn-icon d-lg-none" aria-label="Toggle Sidebar" style="display:none;">
          <i class="fa-solid fa-bars"></i>
        </button>
        <div class="page-title">
          <div class="page-breadcrumb">
            <span class="company-badge">
              <img src="assets/images/logo.png" style="width:14px; height:14px; object-fit:contain;" alt="logo">
              NexByte Technologies
            </span>
            <span class="breadcrumb-slash">/</span>
            <span class="active-crumb"><?php echo htmlspecialchars($breadcrumb); ?></span>
          </div>
          <h1><?php echo htmlspecialchars($page_title); ?></h1>
        </div>
      </div>

      <div class="topbar-right">
        <!-- Live System Status -->
        <div class="system-status-pill">
          <span class="pulse-dot"></span>
          <span>Database: hr_db</span>
        </div>

        <!-- Date Chip -->
        <div class="date-chip">
          <i class="fa-regular fa-calendar-days text-primary"></i>
          <span><?php echo date('D, d M Y'); ?></span>
        </div>

        <!-- Add Employee Button -->
        <a href="add_employee.php" class="btn-primary-custom" style="padding:7px 15px; font-size:0.82rem;">
          <i class="fa-solid fa-plus"></i>
          <span>Add Employee</span>
        </a>
      </div>
    </header>

    <div class="content-body">
