<?php
/**
 * HR Management - Clean Executive Dashboard
 */
require_once 'db.php';

$page_title = 'HR Dashboard';
$breadcrumb = 'Overview';

// 1. Fetch System KPIs
$metrics = get_hr_metrics($conn);

// 2. Fetch Department-wise Headcount & Budget Breakdown
$dept_query = "SELECT 
                d.department_name, 
                d.department_code,
                COUNT(e.employee_id) as emp_count,
                COALESCE(SUM(s.basic_salary + s.bonus), 0) as total_budget
               FROM Department d
               LEFT JOIN Employee e ON d.department_id = e.department_id
               LEFT JOIN Salary s ON e.employee_id = s.employee_id
               GROUP BY d.department_id
               ORDER BY emp_count DESC";
$dept_stats = mysqli_query($conn, $dept_query);

include_once 'includes/header.php';
?>

<!-- Welcome Banner -->
<div style="background:#ffffff; border:1px solid var(--border-color); border-radius:var(--radius-lg); padding:24px 28px; margin-bottom:24px; display:flex; justify-content:space-between; align-items:center;">
  <div>
    <h2 style="font-size:1.4rem; font-weight:800; color:var(--slate-900); margin-bottom:4px;">
      Welcome to HR Management Dashboard 👋
    </h2>
    <p style="color:var(--slate-500); font-size:0.9rem; margin:0;">
      Here is the real-time summary of your workforce, departments, and payroll.
    </p>
  </div>
  <div style="font-size:0.85rem; color:var(--slate-500); background:#f8fafc; padding:8px 16px; border-radius:30px; border:1px solid #e2e8f0;">
    <i class="fa-regular fa-calendar me-1 text-primary"></i> <?php echo date('l, d F Y'); ?>
  </div>
</div>

<!-- 4 Clean KPI Cards -->
<div class="metrics-grid">
  <!-- Card 1: Total Employees -->
  <div class="metric-card">
    <div>
      <div class="metric-title">Total Employees</div>
      <div class="metric-value"><?php echo $metrics['total_employees']; ?></div>
      <div class="metric-sub">
        <span style="color:var(--success); font-weight:700;"><i class="fa-solid fa-circle-check"></i> Active</span> staff records
      </div>
    </div>
    <div class="metric-icon-box icon-purple">
      <i class="fa-solid fa-users"></i>
    </div>
  </div>

  <!-- Card 2: Departments -->
  <div class="metric-card">
    <div>
      <div class="metric-title">Departments</div>
      <div class="metric-value"><?php echo $metrics['total_departments']; ?></div>
      <div class="metric-sub">
        <span style="color:var(--secondary); font-weight:600;"><i class="fa-solid fa-building"></i> Operating</span> units
      </div>
    </div>
    <div class="metric-icon-box icon-blue">
      <i class="fa-solid fa-sitemap"></i>
    </div>
  </div>

  <!-- Card 3: Monthly Payroll -->
  <div class="metric-card">
    <div>
      <div class="metric-title">Monthly Payroll</div>
      <div class="metric-value"><?php echo format_bdt($metrics['total_payroll']); ?></div>
      <div class="metric-sub">
        <span style="color:var(--primary); font-weight:600;"><i class="fa-solid fa-wallet"></i> Total budget</span>
      </div>
    </div>
    <div class="metric-icon-box icon-green">
      <i class="fa-solid fa-money-check-dollar"></i>
    </div>
  </div>

  <!-- Card 4: Attendance Rate -->
  <div class="metric-card">
    <div>
      <div class="metric-title">Today's Attendance</div>
      <div class="metric-value"><?php echo $metrics['attendance_rate']; ?>%</div>
      <div class="metric-sub">
        <span style="color:var(--success); font-weight:600;"><?php echo $metrics['present_today']; ?> Present</span>
        &bull; 
        <span style="color:var(--danger);"><?php echo $metrics['absent_today']; ?> Absent</span>
      </div>
    </div>
    <div class="metric-icon-box icon-amber">
      <i class="fa-solid fa-calendar-check"></i>
    </div>
  </div>
</div>

<!-- Department Distribution & Today's Attendance -->
<div style="display:grid; grid-template-columns: 2fr 1fr; gap: 24px;">
  
  <!-- Department Distribution Table -->
  <div class="app-card" style="margin-bottom:0;">
    <div class="card-header-custom">
      <div>
        <div class="card-title">Department Overview</div>
        <div class="card-subtitle">Employee headcount and monthly payroll by department</div>
      </div>
      <a href="departments.php" class="btn-secondary-custom" style="padding:6px 12px; font-size:0.8rem;">
        Manage <i class="fa-solid fa-arrow-right ms-1"></i>
      </a>
    </div>
    <div class="table-responsive">
      <table class="custom-table">
        <thead>
          <tr>
            <th>Department</th>
            <th>Headcount</th>
            <th>Monthly Payroll</th>
            <th>Staff Share</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($dept_stats && mysqli_num_rows($dept_stats) > 0): ?>
            <?php while ($d = mysqli_fetch_assoc($dept_stats)): 
              $pct = ($metrics['total_employees'] > 0) ? round(($d['emp_count'] / $metrics['total_employees']) * 100) : 0;
            ?>
              <tr>
                <td>
                  <strong><?php echo htmlspecialchars($d['department_name']); ?></strong>
                  <span style="font-size:0.75rem; color:var(--slate-400); margin-left:6px;">(<?php echo htmlspecialchars($d['department_code']); ?>)</span>
                </td>
                <td>
                  <span class="badge-custom badge-active"><?php echo $d['emp_count']; ?> Employees</span>
                </td>
                <td><strong><?php echo format_bdt($d['total_budget']); ?></strong></td>
                <td>
                  <div style="display:flex; align-items:center; gap:8px;">
                    <div style="flex:1; background:#e2e8f0; height:6px; border-radius:10px; overflow:hidden;">
                      <div style="width:<?php echo $pct; ?>%; background:var(--primary); height:100%;"></div>
                    </div>
                    <span style="font-size:0.75rem; color:var(--slate-500); font-weight:600;"><?php echo $pct; ?>%</span>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Today's Attendance Summary Card -->
  <div class="app-card" style="margin-bottom:0; display:flex; flex-direction:column;">
    <div class="card-header-custom">
      <div>
        <div class="card-title">Today's Attendance</div>
        <div class="card-subtitle">Daily status overview</div>
      </div>
      <a href="attendance.php" class="btn-secondary-custom" style="padding:6px 12px; font-size:0.8rem;">
        Open Sheet <i class="fa-solid fa-arrow-right ms-1"></i>
      </a>
    </div>
    <div style="padding:24px; flex:1; display:flex; flex-direction:column; justify-content:center; gap:16px;">
      
      <!-- Present Metric -->
      <div style="background:#ecfdf5; border:1px solid #a7f3d0; padding:14px; border-radius:12px; display:flex; align-items:center; justify-content:space-between;">
        <div style="display:flex; align-items:center; gap:12px;">
          <div style="width:36px; height:36px; border-radius:8px; background:#10b981; color:#fff; display:flex; align-items:center; justify-content:center;">
            <i class="fa-solid fa-user-check"></i>
          </div>
          <div>
            <div style="font-weight:700; color:#065f46; font-size:1.1rem;"><?php echo $metrics['present_today']; ?></div>
            <div style="font-size:0.75rem; color:#047857;">Present Today</div>
          </div>
        </div>
        <span class="badge-custom badge-present">Present</span>
      </div>

      <!-- Absent Metric -->
      <div style="background:#fef2f2; border:1px solid #fecaca; padding:14px; border-radius:12px; display:flex; align-items:center; justify-content:space-between;">
        <div style="display:flex; align-items:center; gap:12px;">
          <div style="width:36px; height:36px; border-radius:8px; background:#ef4444; color:#fff; display:flex; align-items:center; justify-content:center;">
            <i class="fa-solid fa-user-xmark"></i>
          </div>
          <div>
            <div style="font-weight:700; color:#991b1b; font-size:1.1rem;"><?php echo $metrics['absent_today']; ?></div>
            <div style="font-size:0.75rem; color:#b91c1c;">Absent</div>
          </div>
        </div>
        <span class="badge-custom badge-absent">Absent</span>
      </div>

      <!-- Leave Metric -->
      <div style="background:#fffbeb; border:1px solid #fde68a; padding:14px; border-radius:12px; display:flex; align-items:center; justify-content:space-between;">
        <div style="display:flex; align-items:center; gap:12px;">
          <div style="width:36px; height:36px; border-radius:8px; background:#f59e0b; color:#fff; display:flex; align-items:center; justify-content:center;">
            <i class="fa-solid fa-user-clock"></i>
          </div>
          <div>
            <div style="font-weight:700; color:#92400e; font-size:1.1rem;"><?php echo $metrics['leave_today']; ?></div>
            <div style="font-size:0.75rem; color:#b45309;">On Leave</div>
          </div>
        </div>
        <span class="badge-custom badge-leave">Leave</span>
      </div>

    </div>
  </div>

</div>

<?php include_once 'includes/footer.php'; ?>
