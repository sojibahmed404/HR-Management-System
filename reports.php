<?php
/**
 * HR Reports & Analytics
 * NexByte Technologies Ltd.
 */
require_once 'db.php';

$page_title = 'HR Reports & Analytics';
$breadcrumb = 'Reports / Overview';

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'master';

// 1. Master Workforce Query
$sql_master = "SELECT 
            e.employee_name,
            e.designation,
            d.department_name,
            e.email,
            s.basic_salary,
            s.bonus,
            a.attendance_date,
            a.status
        FROM Employee e
        LEFT JOIN Department d ON e.department_id = d.department_id
        LEFT JOIN Salary s ON e.employee_id = s.employee_id
        LEFT JOIN Attendance a ON e.employee_id = a.employee_id
        ORDER BY a.attendance_date DESC, e.employee_name ASC";
$res_master = mysqli_query($conn, $sql_master);

// 2. Top 5 Salaries Query
$sql_top = "SELECT 
            e.employee_name,
            e.designation,
            d.department_name,
            s.basic_salary,
            s.bonus,
            (s.basic_salary + s.bonus) as total_payout
        FROM Employee e
        JOIN Salary s ON e.employee_id = s.employee_id
        JOIN Department d ON e.department_id = d.department_id
        ORDER BY total_payout DESC
        LIMIT 5";
$res_top = mysqli_query($conn, $sql_top);

// 3. Department Cost Analysis Query
$sql_dept = "SELECT 
            d.department_name,
            COUNT(e.employee_id) as staff_count,
            MIN(s.basic_salary) as min_salary,
            MAX(s.basic_salary) as max_salary,
            AVG(s.basic_salary) as avg_salary,
            SUM(s.basic_salary + s.bonus) as total_dept_budget
        FROM Department d
        LEFT JOIN Employee e ON d.department_id = e.department_id
        LEFT JOIN Salary s ON e.employee_id = s.employee_id
        GROUP BY d.department_id
        ORDER BY total_dept_budget DESC";
$res_dept = mysqli_query($conn, $sql_dept);

include_once 'includes/header.php';
?>

<!-- Tab Navigation Bar -->
<div class="app-card" style="margin-bottom:18px;">
  <div style="padding:12px 20px; display:flex; gap:10px; align-items:center; justify-content:space-between; flex-wrap:wrap;">
    <div style="display:flex; gap:8px; flex-wrap:wrap;">
      <a href="reports.php?tab=master" class="<?php echo ($active_tab == 'master' || $active_tab == 'q1') ? 'btn-primary-custom' : 'btn-secondary-custom'; ?>" style="font-size:0.82rem; padding:7px 14px;">
        <i class="fa-solid fa-table-list me-1"></i> Master Staff Report
      </a>
      <a href="reports.php?tab=top" class="<?php echo ($active_tab == 'top' || $active_tab == 'q2') ? 'btn-primary-custom' : 'btn-secondary-custom'; ?>" style="font-size:0.82rem; padding:7px 14px;">
        <i class="fa-solid fa-ranking-star me-1"></i> Top Compensations
      </a>
      <a href="reports.php?tab=dept" class="<?php echo ($active_tab == 'dept' || $active_tab == 'q3') ? 'btn-primary-custom' : 'btn-secondary-custom'; ?>" style="font-size:0.82rem; padding:7px 14px;">
        <i class="fa-solid fa-chart-pie me-1"></i> Department Financials
      </a>
    </div>

    <button type="button" onclick="window.print()" class="btn-secondary-custom" style="padding:7px 14px; font-size:0.82rem;">
      <i class="fa-solid fa-print"></i> Print Report
    </button>
  </div>
</div>

<?php if ($active_tab == 'master' || $active_tab == 'q1'): ?>
  <!-- Tab 1: Master Staff Report -->
  <div class="app-card">
    <div class="card-header-custom" style="padding:14px 20px;">
      <div>
        <div class="card-title" style="font-size:0.95rem;">Comprehensive Workforce & Payroll Report</div>
        <div class="card-subtitle" style="font-size:0.75rem;">Consolidated staff records, compensation package, and attendance status</div>
      </div>
    </div>
    <div class="table-responsive" style="overflow-x:hidden;">
      <table class="custom-table" style="table-layout:fixed; width:100%;">
        <thead>
          <tr>
            <th style="width:22%;">Employee Name</th>
            <th style="width:20%;">Department</th>
            <th style="width:24%;">Email Address</th>
            <th style="width:14%;">Basic Salary</th>
            <th style="width:10%;">Bonus</th>
            <th style="width:10%; text-align:center;">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($res_master && mysqli_num_rows($res_master) > 0): ?>
            <?php while ($r = mysqli_fetch_assoc($res_master)): 
              $st = $r['status'] ?? 'Present';
              $badge = ($st == 'Present') ? 'badge-present' : (($st == 'Absent') ? 'badge-absent' : 'badge-leave');
            ?>
              <tr>
                <td>
                  <strong style="font-size:0.82rem; color:var(--slate-900);"><?php echo htmlspecialchars($r['employee_name']); ?></strong>
                  <div style="font-size:0.7rem; color:var(--slate-500);"><?php echo htmlspecialchars($r['designation'] ?? 'Specialist'); ?></div>
                </td>
                <td>
                  <span class="badge-custom badge-active" style="font-size:0.7rem; padding:2px 7px;">
                    <?php echo htmlspecialchars($r['department_name'] ?? 'N/A'); ?>
                  </span>
                </td>
                <td style="font-size:0.76rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                  <i class="fa-regular fa-envelope me-1 text-muted"></i><?php echo htmlspecialchars($r['email']); ?>
                </td>
                <td style="font-size:0.8rem; font-weight:700;">
                  <?php echo format_bdt($r['basic_salary'] ?? 0); ?>
                </td>
                <td style="font-size:0.76rem; color:var(--success); font-weight:600;">
                  + <?php echo format_bdt($r['bonus'] ?? 0); ?>
                </td>
                <td style="text-align:center;">
                  <span class="badge-custom <?php echo $badge; ?>" style="font-size:0.7rem; padding:2px 6px;">
                    <?php echo htmlspecialchars($st); ?>
                  </span>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

<?php elseif ($active_tab == 'top' || $active_tab == 'q2'): ?>
  <!-- Tab 2: Top Compensations -->
  <div class="app-card">
    <div class="card-header-custom" style="padding:14px 20px;">
      <div>
        <div class="card-title" style="font-size:0.95rem;">Top 5 Highest Paid Personnel</div>
        <div class="card-subtitle" style="font-size:0.75rem;">Highest gross earners across all corporate departments</div>
      </div>
    </div>
    <div class="table-responsive" style="overflow-x:hidden;">
      <table class="custom-table" style="table-layout:fixed; width:100%;">
        <thead>
          <tr>
            <th style="width:8%;">Rank</th>
            <th style="width:26%;">Employee Details</th>
            <th style="width:22%;">Department</th>
            <th style="width:16%;">Base Pay</th>
            <th style="width:12%;">Bonus</th>
            <th style="width:16%; text-align:right;">Total Gross Pay</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($res_top && mysqli_num_rows($res_top) > 0): $rank = 1; ?>
            <?php while ($r = mysqli_fetch_assoc($res_top)): ?>
              <tr>
                <td>
                  <span class="badge-custom badge-active" style="width:24px; height:24px; justify-content:center; border-radius:50%; font-weight:700; font-size:0.72rem;">#<?php echo $rank++; ?></span>
                </td>
                <td>
                  <strong style="font-size:0.82rem; color:var(--slate-900);"><?php echo htmlspecialchars($r['employee_name']); ?></strong>
                  <div style="font-size:0.7rem; color:var(--slate-500);"><?php echo htmlspecialchars($r['designation']); ?></div>
                </td>
                <td>
                  <span class="badge-custom badge-active" style="font-size:0.7rem; padding:2px 7px;">
                    <?php echo htmlspecialchars($r['department_name']); ?>
                  </span>
                </td>
                <td style="font-size:0.8rem; font-weight:600;"><?php echo format_bdt($r['basic_salary']); ?></td>
                <td style="font-size:0.76rem; color:var(--success); font-weight:600;">+ <?php echo format_bdt($r['bonus']); ?></td>
                <td style="text-align:right;">
                  <strong style="color:var(--primary); font-size:0.88rem;"><?php echo format_bdt($r['total_payout']); ?></strong>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

<?php elseif ($active_tab == 'dept' || $active_tab == 'q3'): ?>
  <!-- Tab 3: Department Financial Analysis -->
  <div class="app-card">
    <div class="card-header-custom" style="padding:14px 20px;">
      <div>
        <div class="card-title" style="font-size:0.95rem;">Department Aggregate Financial Breakdown</div>
        <div class="card-subtitle" style="font-size:0.75rem;">Minimum, maximum, average pay scale and total monthly payroll allocation</div>
      </div>
    </div>
    <div class="table-responsive" style="overflow-x:hidden;">
      <table class="custom-table" style="table-layout:fixed; width:100%;">
        <thead>
          <tr>
            <th style="width:24%;">Department</th>
            <th style="width:14%;">Headcount</th>
            <th style="width:16%;">Min Base Pay</th>
            <th style="width:16%;">Max Base Pay</th>
            <th style="width:15%;">Average Base</th>
            <th style="width:15%; text-align:right;">Total Budget</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($res_dept && mysqli_num_rows($res_dept) > 0): ?>
            <?php while ($r = mysqli_fetch_assoc($res_dept)): ?>
              <tr>
                <td><strong style="font-size:0.82rem; color:var(--slate-900);"><?php echo htmlspecialchars($r['department_name']); ?></strong></td>
                <td><span class="badge-custom badge-active" style="font-size:0.7rem;"><?php echo $r['staff_count']; ?> Staff</span></td>
                <td style="font-size:0.8rem;"><?php echo format_bdt($r['min_salary'] ?? 0); ?></td>
                <td style="font-size:0.8rem;"><?php echo format_bdt($r['max_salary'] ?? 0); ?></td>
                <td style="font-size:0.8rem;"><?php echo format_bdt($r['avg_salary'] ?? 0); ?></td>
                <td style="text-align:right;"><strong style="color:var(--primary); font-size:0.88rem;"><?php echo format_bdt($r['total_dept_budget'] ?? 0); ?></strong></td>
              </tr>
            <?php endwhile; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php endif; ?>

<?php include_once 'includes/footer.php'; ?>
