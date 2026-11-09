<?php
/**
 * Advanced HR Reports & SQL Analytics
 * Apex HR Management System
 */
require_once 'db.php';

$page_title = 'HR Analytics & SQL Reports';
$breadcrumb = 'Reports / SQL Analytics';

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'q1';

// 1. Q1 Query: Multi-table Left Join (Exercise Question)
$sql_q1 = "SELECT 
            e.employee_name,
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
$res_q1 = mysqli_query($conn, $sql_q1);

// 2. Q2 Query: Highest Paid Staff
$sql_q2 = "SELECT 
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
$res_q2 = mysqli_query($conn, $sql_q2);

// 3. Q3 Query: Department Cost Analysis
$sql_q3 = "SELECT 
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
$res_q3 = mysqli_query($conn, $sql_q3);

include_once 'includes/header.php';
?>

<!-- Tab Navigation Bar -->
<div class="app-card" style="margin-bottom:20px;">
  <div style="padding:14px 24px; display:flex; gap:12px; align-items:center; justify-content:space-between; flex-wrap:wrap;">
    <div style="display:flex; gap:8px;">
      <a href="reports.php?tab=q1" class="<?php echo ($active_tab == 'q1') ? 'btn-primary-custom' : 'btn-secondary-custom'; ?>" style="font-size:0.85rem; padding:8px 14px;">
        <i class="fa-solid fa-code-fork me-1"></i> Q1 Multi-Table JOIN
      </a>
      <a href="reports.php?tab=q2" class="<?php echo ($active_tab == 'q2') ? 'btn-primary-custom' : 'btn-secondary-custom'; ?>" style="font-size:0.85rem; padding:8px 14px;">
        <i class="fa-solid fa-ranking-star me-1"></i> Top Compensations
      </a>
      <a href="reports.php?tab=q3" class="<?php echo ($active_tab == 'q3') ? 'btn-primary-custom' : 'btn-secondary-custom'; ?>" style="font-size:0.85rem; padding:8px 14px;">
        <i class="fa-solid fa-chart-pie me-1"></i> Dept Budget Breakdown
      </a>
    </div>

    <button type="button" onclick="window.print()" class="btn-secondary-custom" style="padding:8px 14px;">
      <i class="fa-solid fa-print"></i> Print Report
    </button>
  </div>
</div>

<?php if ($active_tab == 'q1'): ?>
  <!-- Tab 1: Exercise Q1 Multi-table Join -->
  <div class="query-box-banner">
    <div>
      <div style="font-weight:700; color:#38bdf8; margin-bottom:4px;">SQL Multi-Table JOIN Statement (Exercise Question 1):</div>
      <div class="query-code" style="white-space:pre-wrap; font-size:0.8rem; line-height:1.4;">SELECT e.employee_name, d.department_name, e.email, s.basic_salary, s.bonus, a.attendance_date, a.status 
FROM Employee e 
LEFT JOIN Department d ON e.department_id = d.department_id 
LEFT JOIN Salary s ON e.employee_id = s.employee_id 
LEFT JOIN Attendance a ON e.employee_id = a.employee_id 
ORDER BY a.attendance_date DESC, e.employee_name ASC;</div>
    </div>
  </div>

  <div class="app-card">
    <div class="card-header-custom">
      <div>
        <div class="card-title">Exercise Q1 Results: Employee, Department, Salary & Attendance</div>
        <div class="card-subtitle">Full Left Join output across 4 relational tables</div>
      </div>
    </div>
    <div class="table-responsive">
      <table class="custom-table">
        <thead>
          <tr>
            <th>Employee Name</th>
            <th>Department</th>
            <th>Email</th>
            <th>Basic Salary</th>
            <th>Bonus</th>
            <th>Attendance Date</th>
            <th>Attendance Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($res_q1 && mysqli_num_rows($res_q1) > 0): ?>
            <?php while ($r = mysqli_fetch_assoc($res_q1)): 
              $st = $r['status'] ?? 'Present';
              $badge = ($st == 'Present') ? 'badge-present' : (($st == 'Absent') ? 'badge-absent' : 'badge-leave');
            ?>
              <tr>
                <td><strong><?php echo htmlspecialchars($r['employee_name']); ?></strong></td>
                <td><span class="badge-custom badge-active"><?php echo htmlspecialchars($r['department_name'] ?? 'N/A'); ?></span></td>
                <td><?php echo htmlspecialchars($r['email']); ?></td>
                <td><?php echo format_bdt($r['basic_salary'] ?? 0); ?></td>
                <td><span style="color:var(--success); font-weight:600;">+ <?php echo format_bdt($r['bonus'] ?? 0); ?></span></td>
                <td><?php echo htmlspecialchars($r['attendance_date'] ?? 'N/A'); ?></td>
                <td><span class="badge-custom <?php echo $badge; ?>"><?php echo htmlspecialchars($st); ?></span></td>
              </tr>
            <?php endwhile; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

<?php elseif ($active_tab == 'q2'): ?>
  <!-- Tab 2: Top Compensations -->
  <div class="query-box-banner">
    <div>
      <div style="font-weight:700; color:#38bdf8; margin-bottom:4px;">SQL Top 5 Salaries Query:</div>
      <div class="query-code">SELECT e.employee_name, e.designation, d.department_name, s.basic_salary, s.bonus, (s.basic_salary + s.bonus) as total_payout FROM Employee e JOIN Salary s ON e.employee_id = s.employee_id JOIN Department d ON e.department_id = d.department_id ORDER BY total_payout DESC LIMIT 5;</div>
    </div>
  </div>

  <div class="app-card">
    <div class="card-header-custom">
      <div>
        <div class="card-title">Top 5 Highest Paid Personnel</div>
        <div class="card-subtitle">Highest gross earners in the enterprise</div>
      </div>
    </div>
    <div class="table-responsive">
      <table class="custom-table">
        <thead>
          <tr>
            <th>Rank</th>
            <th>Employee Name</th>
            <th>Designation</th>
            <th>Department</th>
            <th>Base Salary</th>
            <th>Bonus</th>
            <th>Total Gross Compensation</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($res_q2 && mysqli_num_rows($res_q2) > 0): $rank = 1; ?>
            <?php while ($r = mysqli_fetch_assoc($res_q2)): ?>
              <tr>
                <td><span class="badge-custom badge-active" style="width:28px; height:28px; justify-content:center; border-radius:50%; font-weight:700;">#<?php echo $rank++; ?></span></td>
                <td><strong><?php echo htmlspecialchars($r['employee_name']); ?></strong></td>
                <td><?php echo htmlspecialchars($r['designation']); ?></td>
                <td><?php echo htmlspecialchars($r['department_name']); ?></td>
                <td><?php echo format_bdt($r['basic_salary']); ?></td>
                <td><span style="color:var(--success); font-weight:600;">+ <?php echo format_bdt($r['bonus']); ?></span></td>
                <td><strong style="color:var(--primary); font-size:1.05rem;"><?php echo format_bdt($r['total_payout']); ?></strong></td>
              </tr>
            <?php endwhile; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

<?php elseif ($active_tab == 'q3'): ?>
  <!-- Tab 3: Department Budget Analysis -->
  <div class="query-box-banner">
    <div>
      <div style="font-weight:700; color:#38bdf8; margin-bottom:4px;">SQL Group By & Aggregation Query:</div>
      <div class="query-code">SELECT d.department_name, COUNT(e.employee_id) as staff_count, MIN(s.basic_salary), MAX(s.basic_salary), AVG(s.basic_salary), SUM(s.basic_salary + s.bonus) as total_dept_budget FROM Department d LEFT JOIN Employee e ON d.department_id = e.department_id LEFT JOIN Salary s ON e.employee_id = s.employee_id GROUP BY d.department_id ORDER BY total_dept_budget DESC;</div>
    </div>
  </div>

  <div class="app-card">
    <div class="card-header-custom">
      <div>
        <div class="card-title">Department Aggregate Financial Analysis</div>
        <div class="card-subtitle">Min, Max, Average and Total Budget per Department</div>
      </div>
    </div>
    <div class="table-responsive">
      <table class="custom-table">
        <thead>
          <tr>
            <th>Department</th>
            <th>Staff Count</th>
            <th>Min Base Pay</th>
            <th>Max Base Pay</th>
            <th>Average Base Pay</th>
            <th>Total Monthly Budget</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($res_q3 && mysqli_num_rows($res_q3) > 0): ?>
            <?php while ($r = mysqli_fetch_assoc($res_q3)): ?>
              <tr>
                <td><strong><?php echo htmlspecialchars($r['department_name']); ?></strong></td>
                <td><span class="badge-custom badge-active"><?php echo $r['staff_count']; ?> Staff</span></td>
                <td><?php echo format_bdt($r['min_salary'] ?? 0); ?></td>
                <td><?php echo format_bdt($r['max_salary'] ?? 0); ?></td>
                <td><?php echo format_bdt($r['avg_salary'] ?? 0); ?></td>
                <td><strong style="color:var(--primary); font-size:1rem;"><?php echo format_bdt($r['total_dept_budget'] ?? 0); ?></strong></td>
              </tr>
            <?php endwhile; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php endif; ?>

<?php include_once 'includes/footer.php'; ?>

