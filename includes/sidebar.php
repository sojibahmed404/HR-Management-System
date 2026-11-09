<?php
/**
 * Corporate HR Sidebar Navigation
 * NexByte Technologies Ltd.
 */
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="app-sidebar" id="appSidebar">
  <!-- Brand / Header -->
  <div class="sidebar-header">
    <div class="brand-logo-container">
      <img src="assets/images/logo.png" alt="NexByte Logo" class="brand-logo-img">
    </div>
    <div class="brand-text">
      <h2>NexByte</h2>
      <span>Technologies Ltd.</span>
    </div>
  </div>

  <!-- Menu Links (Compact & No-Scroll) -->
  <div class="sidebar-menu">
    <div class="menu-category">Main Dashboard</div>
    <a href="index.php" class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
      <i class="fa-solid fa-chart-pie"></i>
      <span>HR Dashboard</span>
    </a>

    <div class="menu-category">Workforce Management</div>
    <a href="employees.php" class="nav-link <?php echo ($current_page == 'employees.php' || $current_page == 'edit_employee.php') ? 'active' : ''; ?>">
      <i class="fa-solid fa-id-card-clip"></i>
      <span>Employee Directory</span>
    </a>
    <a href="add_employee.php" class="nav-link <?php echo ($current_page == 'add_employee.php') ? 'active' : ''; ?>">
      <i class="fa-solid fa-user-plus"></i>
      <span>Add New Employee</span>
    </a>
    <a href="departments.php" class="nav-link <?php echo ($current_page == 'departments.php') ? 'active' : ''; ?>">
      <i class="fa-solid fa-building-user"></i>
      <span>Departments</span>
    </a>

    <div class="menu-category">Payroll & Time</div>
    <a href="attendance.php" class="nav-link <?php echo ($current_page == 'attendance.php') ? 'active' : ''; ?>">
      <i class="fa-solid fa-calendar-check"></i>
      <span>Attendance Sheet</span>
    </a>
    <a href="payroll.php" class="nav-link <?php echo ($current_page == 'payroll.php') ? 'active' : ''; ?>">
      <i class="fa-solid fa-money-check-dollar"></i>
      <span>Payroll & Salary</span>
    </a>

    <div class="menu-category">Analytics & Reports</div>
    <a href="reports.php" class="nav-link <?php echo ($current_page == 'reports.php') ? 'active' : ''; ?>">
      <i class="fa-solid fa-file-invoice"></i>
      <span>HR Reports & Analytics</span>
    </a>
  </div>

  <!-- Footer Admin Profile -->
  <div class="sidebar-footer">
    <div class="user-card">
      <img src="assets/pic.jpeg" alt="Admin Avatar">
      <div class="user-info">
        <div class="name">Md Sojib Ahmed</div>
        <div class="role">Admin &bull; NexByte</div>
      </div>
    </div>
  </div>
</aside>
