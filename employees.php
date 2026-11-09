<?php
/**
 * Employee Directory
 * Apex HR Management System
 */
require_once 'db.php';

$page_title = 'Employee Directory';
$breadcrumb = 'Workforce / Directory';

// Filter inputs
$dept_filter = isset($_GET['dept']) ? intval($_GET['dept']) : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$where_clauses = [];
if ($dept_filter > 0) {
    $where_clauses[] = "e.department_id = $dept_filter";
}
if (!empty($search)) {
    $safe_search = mysqli_real_escape_string($conn, $search);
    $where_clauses[] = "(e.employee_name LIKE '%$safe_search%' OR e.email LIKE '%$safe_search%' OR e.employee_id LIKE '%$safe_search%')";
}

$where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

$sql = "SELECT 
            e.employee_id,
            e.employee_name,
            e.designation,
            e.email,
            e.phone,
            e.hire_date,
            e.status,
            d.department_name,
            d.department_code,
            s.basic_salary,
            s.bonus
        FROM Employee e
        LEFT JOIN Department d ON e.department_id = d.department_id
        LEFT JOIN Salary s ON e.employee_id = s.employee_id
        $where_sql
        ORDER BY e.employee_id ASC";

$employees = mysqli_query($conn, $sql);
$all_depts = mysqli_query($conn, "SELECT * FROM Department ORDER BY department_name ASC");

include_once 'includes/header.php';
?>

<!-- Filter & Search Toolbar -->
<div class="app-card" style="margin-bottom:16px;">
  <div style="padding:12px 18px;">
    <form method="GET" action="employees.php" style="display:flex; flex-wrap:wrap; gap:10px; align-items:center; justify-content:space-between;">
      
      <div style="display:flex; flex-wrap:wrap; gap:8px; align-items:center; flex:1;">
        <!-- Search Input -->
        <div style="position:relative; min-width:220px;">
          <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search name, ID, or email..." class="form-control" style="padding-left:32px; padding-top:6px; padding-bottom:6px; font-size:0.8rem;">
          <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:10px; top:9px; color:var(--slate-400); font-size:0.75rem;"></i>
        </div>

        <!-- Department Filter -->
        <div style="min-width:170px;">
          <select name="dept" class="form-select" onchange="this.form.submit()" style="padding-top:6px; padding-bottom:6px; font-size:0.8rem;">
            <option value="0">All Departments</option>
            <?php if ($all_depts): ?>
              <?php while ($d = mysqli_fetch_assoc($all_depts)): ?>
                <option value="<?php echo $d['department_id']; ?>" <?php echo ($dept_filter == $d['department_id']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($d['department_name']); ?>
                </option>
              <?php endwhile; ?>
            <?php endif; ?>
          </select>
        </div>

        <button type="submit" class="btn-primary-custom" style="padding:6px 12px; font-size:0.8rem;">
          <i class="fa-solid fa-filter"></i> Filter
        </button>

        <?php if ($dept_filter > 0 || !empty($search)): ?>
          <a href="employees.php" class="btn-secondary-custom" style="padding:6px 10px; font-size:0.8rem;">
            <i class="fa-solid fa-rotate-left"></i> Reset
          </a>
        <?php endif; ?>
      </div>

      <div>
        <a href="add_employee.php" class="btn-primary-custom" style="padding:6px 14px; font-size:0.8rem;">
          <i class="fa-solid fa-user-plus"></i> New Employee
        </a>
      </div>

    </form>
  </div>
</div>

<!-- Employee List Table -->
<div class="app-card" style="overflow:hidden;">
  <div class="card-header-custom" style="padding:12px 18px;">
    <div>
      <div class="card-title" style="font-size:0.92rem;">All Employees</div>
      <div class="card-subtitle" style="font-size:0.72rem;">Showing registered staff records</div>
    </div>
  </div>

  <div class="table-responsive" style="overflow-x:hidden;">
    <table class="custom-table" style="table-layout:fixed; width:100%;">
      <thead>
        <tr>
          <th style="width:25%;">Employee</th>
          <th style="width:23%;">Department & Role</th>
          <th style="width:24%;">Contact Info</th>
          <th style="width:14%;">Compensation</th>
          <th style="width:7%; text-align:center;">Status</th>
          <th style="width:7%; text-align:right;">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($employees && mysqli_num_rows($employees) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($employees)): 
            $parts = explode(' ', trim($row['employee_name']));
            $initials = strtoupper(substr($parts[0] ?? 'E', 0, 1) . substr($parts[1] ?? '', 0, 1));
            
            $status = $row['status'] ?? 'Active';
            $badge = ($status == 'Active') ? 'badge-present' : (($status == 'On Leave') ? 'badge-leave' : 'badge-absent');
          ?>
            <tr>
              <td>
                <div class="employee-cell">
                  <div class="avatar-badge" style="width:28px; height:28px; font-size:0.7rem;"><?php echo $initials; ?></div>
                  <div style="min-width:0; overflow:hidden;">
                    <div class="emp-name" style="font-size:0.82rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?php echo htmlspecialchars($row['employee_name']); ?></div>
                    <div class="emp-role" style="font-size:0.68rem;">#<?php echo $row['employee_id']; ?> &bull; Joined <?php echo htmlspecialchars($row['hire_date'] ?? 'N/A'); ?></div>
                  </div>
                </div>
              </td>
              <td>
                <span class="badge-custom badge-active" style="margin-bottom:2px; font-size:0.68rem; padding:2px 6px; display:inline-block; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100%;">
                  <?php echo htmlspecialchars($row['department_name'] ?? 'Unassigned'); ?>
                </span>
                <div style="font-size:0.72rem; color:var(--slate-600); font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                  <?php echo htmlspecialchars($row['designation'] ?? 'Specialist'); ?>
                </div>
              </td>
              <td>
                <div style="font-size:0.74rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?php echo htmlspecialchars($row['email']); ?>">
                  <i class="fa-regular fa-envelope me-1 text-muted"></i><?php echo htmlspecialchars($row['email']); ?>
                </div>
                <div style="font-size:0.7rem; color:var(--slate-500); margin-top:2px; white-space:nowrap;">
                  <i class="fa-solid fa-phone me-1 text-muted"></i><?php echo htmlspecialchars($row['phone'] ?? 'N/A'); ?>
                </div>
              </td>
              <td>
                <div style="font-weight:700; color:var(--slate-900); font-size:0.8rem; white-space:nowrap;"><?php echo format_bdt($row['basic_salary'] ?? 0); ?></div>
                <div style="font-size:0.68rem; color:var(--success); font-weight:600; white-space:nowrap;">+ <?php echo format_bdt($row['bonus'] ?? 0); ?></div>
              </td>
              <td style="text-align:center;">
                <span class="badge-custom <?php echo $badge; ?>" style="font-size:0.68rem; padding:2px 6px;">
                  <?php echo htmlspecialchars($status); ?>
                </span>
              </td>
              <td style="text-align:right;">
                <div class="action-btn-group" style="justify-content:flex-end; gap:4px;">
                  <a href="edit_employee.php?id=<?php echo $row['employee_id']; ?>" class="btn-icon btn-icon-edit" style="width:24px; height:24px; font-size:0.72rem;" title="Edit Employee">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </a>
                  <button type="button" onclick="confirmDelete('delete_employee.php?id=<?php echo $row['employee_id']; ?>', '<?php echo addslashes($row['employee_name']); ?>')" class="btn-icon btn-icon-delete" style="width:24px; height:24px; font-size:0.72rem;" title="Delete Employee">
                    <i class="fa-solid fa-trash-can"></i>
                  </button>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" style="text-align:center; padding:30px; color:var(--slate-400);">
              No employees matched your filter criteria.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include_once 'includes/footer.php'; ?>
