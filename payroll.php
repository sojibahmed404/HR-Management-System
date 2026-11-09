<?php
/**
 * Payroll & Salary Management
 * Apex HR Management System
 */
require_once 'db.php';

$page_title = 'Payroll & Compensation';
$breadcrumb = 'Finance / Payroll';

// Fetch payroll data
$sql = "SELECT 
            e.employee_id,
            e.employee_name,
            e.designation,
            e.email,
            d.department_name,
            s.salary_id,
            s.basic_salary,
            s.bonus,
            (s.basic_salary + s.bonus) as gross_salary
        FROM Employee e
        JOIN Salary s ON e.employee_id = s.employee_id
        LEFT JOIN Department d ON e.department_id = d.department_id
        ORDER BY gross_salary DESC";
$payroll = mysqli_query($conn, $sql);

// Summary Stats
$summary_sql = "SELECT 
                    SUM(basic_salary) as sum_basic,
                    SUM(bonus) as sum_bonus,
                    SUM(basic_salary + bonus) as total_payout,
                    AVG(basic_salary) as avg_basic,
                    MAX(basic_salary + bonus) as max_pay
                FROM Salary";
$sum_res = mysqli_query($conn, $summary_sql);
$stats = ($sum_res) ? mysqli_fetch_assoc($sum_res) : [];

include_once 'includes/header.php';
?>

<!-- Payroll KPI Cards -->
<div class="metrics-grid">
  <div class="metric-card">
    <div>
      <div class="metric-title">Total Monthly Payroll</div>
      <div class="metric-value"><?php echo format_bdt($stats['total_payout'] ?? 0); ?></div>
      <div class="metric-sub"><span style="color:var(--success); font-weight:700;">Basic + Allowances</span></div>
    </div>
    <div class="metric-icon-box icon-green">
      <i class="fa-solid fa-money-bill-wave"></i>
    </div>
  </div>

  <div class="metric-card">
    <div>
      <div class="metric-title">Average Base Salary</div>
      <div class="metric-value"><?php echo format_bdt($stats['avg_basic'] ?? 0); ?></div>
      <div class="metric-sub"><span style="color:var(--secondary); font-weight:600;">Per staff member</span></div>
    </div>
    <div class="metric-icon-box icon-blue">
      <i class="fa-solid fa-chart-line"></i>
    </div>
  </div>

  <div class="metric-card">
    <div>
      <div class="metric-title">Total Bonuses / Perks</div>
      <div class="metric-value"><?php echo format_bdt($stats['sum_bonus'] ?? 0); ?></div>
      <div class="metric-sub"><span style="color:var(--primary); font-weight:600;">Incentive pool</span></div>
    </div>
    <div class="metric-icon-box icon-purple">
      <i class="fa-solid fa-gift"></i>
    </div>
  </div>

  <div class="metric-card">
    <div>
      <div class="metric-title">Top Compensation</div>
      <div class="metric-value"><?php echo format_bdt($stats['max_pay'] ?? 0); ?></div>
      <div class="metric-sub"><span style="color:var(--warning); font-weight:600;">Executive / Lead rate</span></div>
    </div>
    <div class="metric-icon-box icon-amber">
      <i class="fa-solid fa-trophy"></i>
    </div>
  </div>
</div>

<!-- Payroll Table -->
<div class="app-card">
  <div class="card-header-custom">
    <div>
      <div class="card-title">Monthly Payroll Sheet: <?php echo date('F Y'); ?></div>
      <div class="card-subtitle">Staff compensation breakdown with payslip generation</div>
    </div>
    <button type="button" onclick="window.print()" class="btn-secondary-custom">
      <i class="fa-solid fa-print"></i> Print Payroll Sheet
    </button>
  </div>

  <div class="table-responsive">
    <table class="custom-table">
      <thead>
        <tr>
          <th>Employee</th>
          <th>Department</th>
          <th>Basic Pay</th>
          <th>Bonus / Perks</th>
          <th>Gross Pay</th>
          <th>Deduction (5%)</th>
          <th>Net Payout</th>
          <th style="text-align:right;">Payslip Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($payroll && mysqli_num_rows($payroll) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($payroll)): 
            $basic = (float)$row['basic_salary'];
            $bonus = (float)$row['bonus'];
            $gross = $basic + $bonus;
            $tax   = $gross * 0.05; // 5% standard provident/tax
            $net   = $gross - $tax;
            
            $parts = explode(' ', trim($row['employee_name']));
            $initials = strtoupper(substr($parts[0] ?? 'E', 0, 1) . substr($parts[1] ?? '', 0, 1));
          ?>
            <tr>
              <td>
                <div class="employee-cell">
                  <div class="avatar-badge"><?php echo $initials; ?></div>
                  <div>
                    <div class="emp-name"><?php echo htmlspecialchars($row['employee_name']); ?></div>
                    <div class="emp-role">#<?php echo $row['employee_id']; ?> &bull; <?php echo htmlspecialchars($row['designation'] ?? 'Staff'); ?></div>
                  </div>
                </div>
              </td>
              <td><span class="badge-custom badge-active"><?php echo htmlspecialchars($row['department_name'] ?? 'General'); ?></span></td>
              <td><?php echo format_bdt($basic); ?></td>
              <td><span style="color:var(--success); font-weight:600;">+ <?php echo format_bdt($bonus); ?></span></td>
              <td><strong><?php echo format_bdt($gross); ?></strong></td>
              <td style="color:var(--danger);">- <?php echo format_bdt($tax); ?></td>
              <td><strong style="color:var(--primary); font-size:1rem;"><?php echo format_bdt($net); ?></strong></td>
              <td style="text-align:right;">
                <button type="button" onclick="printPayslip(<?php echo $row['employee_id']; ?>)" class="btn-secondary-custom" style="padding:6px 12px; font-size:0.8rem;">
                  <i class="fa-solid fa-receipt me-1 text-primary"></i> Payslip
                </button>

                <!-- Hidden Printable Payslip Container -->
                <div id="payslip-<?php echo $row['employee_id']; ?>" style="display:none;">
                  <div style="border:2px solid #e2e8f0; border-radius:12px; padding:30px; max-width:650px; margin:0 auto; font-family:sans-serif;">
                    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:2px solid #4f46e5; padding-bottom:15px; margin-bottom:20px;">
                      <div>
                        <h2 style="color:#4f46e5; margin:0; font-size:24px;">Apex Global Enterprise</h2>
                        <p style="color:#64748b; font-size:13px; margin:4px 0 0 0;">Official Monthly Salary Slip &bull; <?php echo date('F Y'); ?></p>
                      </div>
                      <div style="text-align:right;">
                        <span style="background:#ecfdf5; color:#065f46; padding:6px 12px; border-radius:20px; font-weight:bold; font-size:12px;">PAID</span>
                      </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:25px; background:#f8fafc; padding:15px; border-radius:8px;">
                      <div>
                        <div style="font-size:12px; color:#64748b;">EMPLOYEE NAME</div>
                        <div style="font-size:15px; font-weight:bold; color:#0f172a;"><?php echo htmlspecialchars($row['employee_name']); ?></div>
                      </div>
                      <div>
                        <div style="font-size:12px; color:#64748b;">EMPLOYEE ID</div>
                        <div style="font-size:15px; font-weight:bold; color:#0f172a;">#<?php echo $row['employee_id']; ?></div>
                      </div>
                      <div>
                        <div style="font-size:12px; color:#64748b;">DEPARTMENT</div>
                        <div style="font-size:14px; font-weight:600; color:#334155;"><?php echo htmlspecialchars($row['department_name']); ?></div>
                      </div>
                      <div>
                        <div style="font-size:12px; color:#64748b;">DESIGNATION</div>
                        <div style="font-size:14px; font-weight:600; color:#334155;"><?php echo htmlspecialchars($row['designation']); ?></div>
                      </div>
                    </div>

                    <table style="width:100%; border-collapse:collapse; margin-bottom:20px;">
                      <tr style="border-bottom:1px solid #cbd5e1;">
                        <th style="text-align:left; padding:10px 0; color:#475569;">Description</th>
                        <th style="text-align:right; padding:10px 0; color:#475569;">Amount (BDT)</th>
                      </tr>
                      <tr style="border-bottom:1px solid #e2e8f0;">
                        <td style="padding:10px 0;">Base Monthly Salary</td>
                        <td style="text-align:right; padding:10px 0; font-weight:600;"><?php echo format_bdt($basic); ?></td>
                      </tr>
                      <tr style="border-bottom:1px solid #e2e8f0;">
                        <td style="padding:10px 0;">Performance Bonus & Allowances</td>
                        <td style="text-align:right; padding:10px 0; font-weight:600; color:#10b981;"><?php echo format_bdt($bonus); ?></td>
                      </tr>
                      <tr style="border-bottom:1px solid #e2e8f0;">
                        <td style="padding:10px 0;">Standard Statutory Deduction (5%)</td>
                        <td style="text-align:right; padding:10px 0; font-weight:600; color:#ef4444;">- <?php echo format_bdt($tax); ?></td>
                      </tr>
                      <tr style="border-top:2px solid #0f172a; font-size:16px;">
                        <td style="padding:14px 0; font-weight:bold;">Net Disbursed Salary</td>
                        <td style="text-align:right; padding:14px 0; font-weight:bold; color:#4f46e5;"><?php echo format_bdt($net); ?></td>
                      </tr>
                    </table>

                    <div style="display:flex; justify-content:space-between; margin-top:40px; font-size:12px; color:#64748b; border-top:1px dashed #cbd5e1; padding-top:20px;">
                      <div>Authorized Signature: __________________</div>
                      <div>Employee Signature: __________________</div>
                    </div>
                  </div>
                </div>

              </td>
            </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include_once 'includes/footer.php'; ?>

