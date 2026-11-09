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
<div class="app-card" style="margin-bottom:20px;">
  <div style="padding:18px 24px;">
    <form method="GET" action="employees.php" style="display:flex; flex-wrap:wrap; gap:16px; align-items:center; justify-content:space-between;">
      
      <div style="display:flex; flex-wrap:wrap; gap:12px; align-items:center; flex:1;">
        <!-- Search Input -->
        <div style="position:relative; min-width:260px;">
          <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by name, ID, or email..." class="form-control" style="padding-left:36px;">
          <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:12px; top:12px; color:var(--slate-400);"></i>
        </div>

        <!-- Department Filter -->
        <div style="min-width:200px;">
          <select name="dept" class="form-select" onchange="this.form.submit()">
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

        <button type="submit" class="btn-primary-custom" style="padding:9px 16px;">
          <i class="fa-solid fa-filter"></i> Apply
        </button>

        <?php if ($dept_filter > 0 || !empty($search)): ?>
          <a href="employees.php" class="btn-secondary-custom" style="padding:9px 14px;">
            <i class="fa-solid fa-rotate-left"></i> Reset
          </a>
        <?php endif; ?>
      </div>

      <div>
        <a href="add_employee.php" class="btn-primary-custom">
          <i class="fa-solid fa-user-plus"></i> New Employee
        </a>
      </div>

    </form>
  </div>
</div>

<!-- Employee List Table -->
<div class="app-card">
  <div class="card-header-custom">
    <div>
      <div class="card-title">All Employees</div>
      <div class="card-subtitle">Showing registered staff records</div>
    </div>
  </div>

  <div class="table-responsive">
    <table class="custom-table">
      <thead>
        <tr>
          <th>Employee Details</th>
          <th>Department</th>
          <th>Designation</th>
          <th>Contact</th>
          <th>Join Date</th>
          <th>Monthly Salary</th>
          <th>Status</th>
          <th style="text-align:right;">Actions</th>
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
                  <div class="avatar-badge"><?php echo $initials; ?></div>
                  <div>
                    <div class="emp-name"><?php echo htmlspecialchars($row['employee_name']); ?></div>
                    <div class="emp-role">ID: #<?php echo $row['employee_id']; ?></div>
                  </div>
                </div>
              </td>
              <td>
                <span class="badge-custom badge-active">
                  <?php echo htmlspecialchars($row['department_name'] ?? 'Unassigned'); ?>
                </span>
              </td>
              <td><strong><?php echo htmlspecialchars($row['designation'] ?? 'Specialist'); ?></strong></td>
              <td>
                <div><i class="fa-regular fa-envelope me-1 text-muted"></i><?php echo htmlspecialchars($row['email']); ?></div>
                <div style="font-size:0.75rem; color:var(--slate-500); margin-top:2px;">
                  <i class="fa-solid fa-phone me-1 text-muted"></i><?php echo htmlspecialchars($row['phone'] ?? 'N/A'); ?>
                </div>
              </td>
              <td><?php echo htmlspecialchars($row['hire_date'] ?? 'N/A'); ?></td>
              <td>
                <strong><?php echo format_bdt($row['basic_salary'] ?? 0); ?></strong>
                <div style="font-size:0.72rem; color:var(--success);">+ <?php echo format_bdt($row['bonus'] ?? 0); ?> bonus</div>
              </td>
              <td>
                <span class="badge-custom <?php echo $badge; ?>">
                  <?php echo htmlspecialchars($status); ?>
                </span>
              </td>
              <td style="text-align:right;">
                <div class="action-btn-group" style="justify-content:flex-end;">
                  <a href="edit_employee.php?id=<?php echo $row['employee_id']; ?>" class="btn-icon btn-icon-edit" title="Edit Employee">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </a>
                  <button type="button" onclick="confirmDelete('delete_employee.php?id=<?php echo $row['employee_id']; ?>', '<?php echo addslashes($row['employee_name']); ?>')" class="btn-icon btn-icon-delete" title="Delete Employee">
                    <i class="fa-solid fa-trash-can"></i>
                  </button>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="8" style="text-align:center; padding:40px; color:var(--slate-400);">
              No employees matched your filter criteria.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include_once 'includes/footer.php'; ?>

