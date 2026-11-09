<?php
/**
 * Modern Attendance Tracker & Sheet
 * Apex HR Management System
 */
require_once 'db.php';

$page_title = 'Attendance Sheet';
$breadcrumb = 'Time & Attendance';

$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$prev_date     = date('Y-m-d', strtotime($selected_date . ' -1 day'));
$next_date     = date('Y-m-d', strtotime($selected_date . ' +1 day'));
$today_date    = date('Y-m-d');

$message = '';
$message_type = '';

// Handle Bulk Attendance Save / Update (Without Remarks / Notes)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['attendance'])) {
    $post_date = mysqli_real_escape_string($conn, $_POST['date']);
    
    foreach ($_POST['attendance'] as $emp_id => $data) {
        $emp_id = intval($emp_id);
        $status = mysqli_real_escape_string($conn, $data['status'] ?? 'Present');

        // Check if attendance already recorded for this employee on this date
        $chk = mysqli_query($conn, "SELECT attendance_id FROM Attendance WHERE employee_id = $emp_id AND attendance_date = '$post_date'");
        if (mysqli_num_rows($chk) > 0) {
            $upd = "UPDATE Attendance SET status = '$status' WHERE employee_id = $emp_id AND attendance_date = '$post_date'";
            mysqli_query($conn, $upd);
        } else {
            $ins = "INSERT INTO Attendance (employee_id, attendance_date, status) VALUES ($emp_id, '$post_date', '$status')";
            mysqli_query($conn, $ins);
        }
    }

    $message = "Attendance records for " . date('d F Y', strtotime($post_date)) . " have been saved successfully!";
    $message_type = 'success';
    $selected_date = $post_date;
}

// Fetch all employees with their attendance status for the selected date
$query = "SELECT 
            e.employee_id,
            e.employee_name,
            e.designation,
            d.department_name,
            d.department_code,
            COALESCE(a.status, 'Present') as status
          FROM Employee e
          LEFT JOIN Department d ON e.department_id = d.department_id
          LEFT JOIN Attendance a ON e.employee_id = a.employee_id AND a.attendance_date = '$selected_date'
          ORDER BY e.employee_id ASC";
$result = mysqli_query($conn, $query);

// Summary stats for the selected day
$stat_sql = "SELECT 
                SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as p_cnt,
                SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) as a_cnt,
                SUM(CASE WHEN status = 'Leave' THEN 1 ELSE 0 END) as l_cnt,
                COUNT(*) as total_logged
             FROM Attendance WHERE attendance_date = '$selected_date'";
$stat_res = mysqli_query($conn, $stat_sql);
$day_stats = ($stat_res) ? mysqli_fetch_assoc($stat_res) : ['p_cnt' => 0, 'a_cnt' => 0, 'l_cnt' => 0, 'total_logged' => 0];

$total_employees = 0;
$cnt_res = mysqli_query($conn, "SELECT COUNT(*) as c FROM Employee");
if ($cnt_res && $crow = mysqli_fetch_assoc($cnt_res)) {
    $total_employees = (int)$crow['c'];
}

$present_count = (int)$day_stats['p_cnt'];
$absent_count  = (int)$day_stats['a_cnt'];
$leave_count   = (int)$day_stats['l_cnt'];
$attendance_pct = ($total_employees > 0) ? round(($present_count / $total_employees) * 100) : 0;

include_once 'includes/header.php';
?>

<style>
/* Modern Interactive Status Segmented Controls */
.status-pill-group {
  display: inline-flex;
  background: #f1f5f9;
  padding: 4px;
  border-radius: 30px;
  gap: 4px;
  border: 1px solid #e2e8f0;
}

.status-pill-label {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 7px 16px;
  border-radius: 20px;
  font-size: 0.82rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  color: #64748b;
  user-select: none;
  margin: 0;
}

.status-pill-label input[type="radio"] {
  display: none;
}

.status-pill-label:hover {
  color: #1e293b;
  background: rgba(255, 255, 255, 0.6);
}

/* Active States */
.status-pill-label.active-present {
  background: #10b981 !important;
  color: #ffffff !important;
  box-shadow: 0 3px 10px rgba(16, 185, 129, 0.35);
}

.status-pill-label.active-absent {
  background: #ef4444 !important;
  color: #ffffff !important;
  box-shadow: 0 3px 10px rgba(239, 68, 68, 0.35);
}

.status-pill-label.active-leave {
  background: #f59e0b !important;
  color: #ffffff !important;
  box-shadow: 0 3px 10px rgba(245, 158, 11, 0.35);
}

.date-nav-btn {
  background: #ffffff;
  border: 1px solid var(--border-color);
  padding: 8px 14px;
  border-radius: 8px;
  color: var(--slate-700);
  font-size: 0.85rem;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: all 0.15s;
}

.date-nav-btn:hover {
  background: var(--slate-100);
  color: var(--slate-900);
}
</style>

<!-- Top Date Controller & Quick Navigation -->
<div class="app-card" style="margin-bottom:24px;">
  <div style="padding:18px 24px; display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:16px;">
    
    <!-- Date Switcher Controls -->
    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
      <a href="attendance.php?date=<?php echo $prev_date; ?>" class="date-nav-btn" title="Previous Day">
        <i class="fa-solid fa-chevron-left"></i> Previous
      </a>

      <form method="GET" action="attendance.php" id="datePickerForm" style="display:flex; align-items:center; gap:8px;">
        <div style="position:relative;">
          <input type="date" name="date" value="<?php echo htmlspecialchars($selected_date); ?>" class="form-control" onchange="document.getElementById('datePickerForm').submit();" style="font-size:0.95rem; font-weight:600; padding:8px 14px;">
        </div>
      </form>

      <a href="attendance.php?date=<?php echo $next_date; ?>" class="date-nav-btn" title="Next Day">
        Next <i class="fa-solid fa-chevron-right"></i>
      </a>

      <?php if ($selected_date !== $today_date): ?>
        <a href="attendance.php?date=<?php echo $today_date; ?>" class="date-nav-btn" style="background:#eef2ff; color:var(--primary); border-color:#c7d2fe;">
          <i class="fa-solid fa-calendar-day"></i> Jump to Today
        </a>
      <?php endif; ?>
    </div>

    <!-- Attendance Rate Progress Bar -->
    <div style="display:flex; align-items:center; gap:14px;">
      <div style="text-align:right;">
        <div style="font-size:0.75rem; color:var(--slate-500); text-transform:uppercase; font-weight:700;">Attendance Rate</div>
        <div style="font-size:1.15rem; font-weight:800; color:var(--slate-900);"><?php echo $attendance_pct; ?>%</div>
      </div>
      <div style="width:120px; background:#e2e8f0; height:8px; border-radius:10px; overflow:hidden;">
        <div style="width:<?php echo $attendance_pct; ?>%; background:#10b981; height:100%; border-radius:10px; transition:width 0.4s;"></div>
      </div>
    </div>

  </div>
</div>

<!-- 3 Stat Metrics for the Selected Day -->
<div class="metrics-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom:24px;">
  <!-- Present Card -->
  <div class="metric-card" style="padding:18px;">
    <div>
      <div class="metric-title" style="color:#059669;">Present Staff</div>
      <div class="metric-value" style="color:#10b981;"><?php echo $present_count; ?></div>
      <div class="metric-sub" style="color:#047857;">On-duty at office / remote</div>
    </div>
    <div class="metric-icon-box icon-green">
      <i class="fa-solid fa-user-check"></i>
    </div>
  </div>

  <!-- Absent Card -->
  <div class="metric-card" style="padding:18px;">
    <div>
      <div class="metric-title" style="color:#b91c1c;">Absent Staff</div>
      <div class="metric-value" style="color:#ef4444;"><?php echo $absent_count; ?></div>
      <div class="metric-sub" style="color:#991b1b;">Unexcused absences</div>
    </div>
    <div class="metric-icon-box" style="background:#fee2e2; color:#ef4444;">
      <i class="fa-solid fa-user-xmark"></i>
    </div>
  </div>

  <!-- On Leave Card -->
  <div class="metric-card" style="padding:18px;">
    <div>
      <div class="metric-title" style="color:#d97706;">On Leave</div>
      <div class="metric-value" style="color:#f59e0b;"><?php echo $leave_count; ?></div>
      <div class="metric-sub" style="color:#92400e;">Casual / Medical leave</div>
    </div>
    <div class="metric-icon-box icon-amber">
      <i class="fa-solid fa-clock"></i>
    </div>
  </div>
</div>

<?php if (!empty($message)): ?>
  <div style="padding:16px 20px; border-radius:var(--radius-md); margin-bottom:24px; font-weight:600; background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; display:flex; align-items:center; gap:12px;">
    <i class="fa-solid fa-circle-check" style="font-size:1.2rem;"></i>
    <div><?php echo $message; ?></div>
  </div>
<?php endif; ?>

<!-- Main Attendance Sheet Form -->
<form method="POST" action="attendance.php" id="attendanceForm">
  <input type="hidden" name="date" value="<?php echo htmlspecialchars($selected_date); ?>">

  <div class="app-card">
    <div class="card-header-custom">
      <div>
        <div class="card-title">Daily Attendance Sheet &bull; <?php echo date('l, d F Y', strtotime($selected_date)); ?></div>
        <div class="card-subtitle">Mark present, absent, or leave status for each staff member</div>
      </div>
      
      <div style="display:flex; align-items:center; gap:10px;">
        <!-- Quick Bulk Select Buttons -->
        <button type="button" onclick="markAll('Present')" class="btn-secondary-custom" style="padding:7px 12px; font-size:0.8rem;">
          <i class="fa-solid fa-check-double text-success"></i> All Present
        </button>
        <button type="button" onclick="markAll('Absent')" class="btn-secondary-custom" style="padding:7px 12px; font-size:0.8rem;">
          <i class="fa-solid fa-xmark text-danger"></i> All Absent
        </button>

        <button type="submit" class="btn-primary-custom" style="padding:8px 18px;">
          <i class="fa-solid fa-floppy-disk"></i> Save Attendance
        </button>
      </div>
    </div>

    <div class="table-responsive">
      <table class="custom-table">
        <thead>
          <tr>
            <th style="width:70px;">ID</th>
            <th>Employee Name</th>
            <th>Department</th>
            <th>Job Designation</th>
            <th style="text-align:right; padding-right:30px;">Attendance Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): 
              $current_st = $row['status'] ?? 'Present';
              $emp_id = $row['employee_id'];
              
              $parts = explode(' ', trim($row['employee_name']));
              $initials = strtoupper(substr($parts[0] ?? 'E', 0, 1) . substr($parts[1] ?? '', 0, 1));
            ?>
              <tr>
                <td><span style="font-weight:700; color:var(--slate-500);">#<?php echo $emp_id; ?></span></td>
                <td>
                  <div class="employee-cell">
                    <div class="avatar-badge"><?php echo $initials; ?></div>
                    <div>
                      <div class="emp-name"><?php echo htmlspecialchars($row['employee_name']); ?></div>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="badge-custom badge-active">
                    <i class="fa-regular fa-building me-1"></i><?php echo htmlspecialchars($row['department_name'] ?? 'General'); ?>
                  </span>
                </td>
                <td><strong><?php echo htmlspecialchars($row['designation'] ?? 'Specialist'); ?></strong></td>
                <td style="text-align:right; padding-right:24px;">
                  <!-- Sleek Segmented Radio Pill Group -->
                  <div class="status-pill-group">
                    
                    <!-- Present -->
                    <label class="status-pill-label <?php echo ($current_st == 'Present') ? 'active-present' : ''; ?>" onclick="updatePill(this, 'present')">
                      <input type="radio" name="attendance[<?php echo $emp_id; ?>][status]" value="Present" <?php echo ($current_st == 'Present') ? 'checked' : ''; ?>>
                      <i class="fa-solid fa-check"></i> Present
                    </label>

                    <!-- Absent -->
                    <label class="status-pill-label <?php echo ($current_st == 'Absent') ? 'active-absent' : ''; ?>" onclick="updatePill(this, 'absent')">
                      <input type="radio" name="attendance[<?php echo $emp_id; ?>][status]" value="Absent" <?php echo ($current_st == 'Absent') ? 'checked' : ''; ?>>
                      <i class="fa-solid fa-xmark"></i> Absent
                    </label>

                    <!-- Leave -->
                    <label class="status-pill-label <?php echo ($current_st == 'Leave') ? 'active-leave' : ''; ?>" onclick="updatePill(this, 'leave')">
                      <input type="radio" name="attendance[<?php echo $emp_id; ?>][status]" value="Leave" <?php echo ($current_st == 'Leave') ? 'checked' : ''; ?>>
                      <i class="fa-solid fa-clock"></i> Leave
                    </label>

                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" style="text-align:center; padding:40px; color:var(--slate-400);">
                No employee records found.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Bottom Save Action Bar -->
    <div style="padding:18px 24px; border-top:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center;">
      <div style="color:var(--slate-500); font-size:0.85rem;">
        <i class="fa-solid fa-circle-info me-1 text-primary"></i> Changes will be saved for <b><?php echo date('d F Y', strtotime($selected_date)); ?></b>.
      </div>
      <button type="submit" class="btn-primary-custom" style="padding:10px 24px;">
        <i class="fa-solid fa-floppy-disk"></i> Save Attendance
      </button>
    </div>

  </div>
</form>

<script>
// Handle Pill Active States
function updatePill(labelElem, type) {
  const group = labelElem.closest('.status-pill-group');
  group.querySelectorAll('.status-pill-label').forEach(l => {
    l.classList.remove('active-present', 'active-absent', 'active-leave');
  });
  labelElem.classList.add('active-' + type);
  const input = labelElem.querySelector('input[type="radio"]');
  if (input) input.checked = true;
}

// Quick Bulk Actions: Mark All Present / Absent
function markAll(status) {
  const rows = document.querySelectorAll('.status-pill-group');
  rows.forEach(group => {
    const radios = group.querySelectorAll('input[type="radio"]');
    radios.forEach(r => {
      if (r.value === status) {
        r.checked = true;
        const label = r.closest('.status-pill-label');
        updatePill(label, status.toLowerCase());
      }
    });
  });
}
</script>

<?php include_once 'includes/footer.php'; ?>
