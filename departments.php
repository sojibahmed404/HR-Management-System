<?php
/**
 * Department Management
 * Apex HR Management System
 */
require_once 'db.php';

$page_title = 'Department Management';
$breadcrumb = 'Organization / Departments';

$message = '';
$message_type = '';

// Handle Add Department
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_department'])) {
    $dept_name = mysqli_real_escape_string($conn, trim($_POST['department_name']));
    $dept_code = mysqli_real_escape_string($conn, trim($_POST['department_code']));
    $location  = mysqli_real_escape_string($conn, trim($_POST['location']));

    if (!empty($dept_name)) {
        $ins = "INSERT INTO Department (department_name, department_code, location) VALUES ('$dept_name', '$dept_code', '$location')";
        if (mysqli_query($conn, $ins)) {
            $message = "Department '$dept_name' created successfully!";
            $message_type = 'success';
        } else {
            $message = "Error: " . mysqli_error($conn);
            $message_type = 'danger';
        }
    }
}

// Fetch all departments with aggregated metrics
$sql = "SELECT 
            d.department_id,
            d.department_name,
            d.department_code,
            d.location,
            COUNT(e.employee_id) as total_staff,
            COALESCE(SUM(s.basic_salary + s.bonus), 0) as total_payroll,
            COALESCE(AVG(s.basic_salary), 0) as avg_basic
        FROM Department d
        LEFT JOIN Employee e ON d.department_id = e.department_id
        LEFT JOIN Salary s ON e.employee_id = s.employee_id
        GROUP BY d.department_id
        ORDER BY total_staff DESC, d.department_name ASC";
$departments = mysqli_query($conn, $sql);

include_once 'includes/header.php';
?>

<?php if (!empty($message)): ?>
  <div style="padding:16px 20px; border-radius:var(--radius-md); margin-bottom:24px; font-weight:600; <?php echo ($message_type == 'success') ? 'background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0;' : 'background:#fef2f2; color:#991b1b; border:1px solid #fecaca;'; ?>">
    <?php echo $message; ?>
  </div>
<?php endif; ?>

<!-- Top Split Layout: Add Dept Form & Cards Grid -->
<div style="display:grid; grid-template-columns: 1fr 2.5fr; gap: 24px;">

  <!-- Add Department Card -->
  <div class="app-card" style="height:fit-content;">
    <div class="card-header-custom">
      <div>
        <div class="card-title">New Department</div>
        <div class="card-subtitle">Expand business unit</div>
      </div>
    </div>
    <div style="padding:20px;">
      <form action="departments.php" method="POST">
        <input type="hidden" name="add_department" value="1">
        
        <div class="form-group">
          <label class="form-label" for="department_name">Department Name *</label>
          <input type="text" id="department_name" name="department_name" placeholder="e.g. Quality Assurance" class="form-control" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="department_code">Department Code</label>
          <input type="text" id="department_code" name="department_code" placeholder="e.g. QA" class="form-control">
        </div>

        <div class="form-group">
          <label class="form-label" for="location">Office Location / Floor</label>
          <input type="text" id="location" name="location" placeholder="e.g. Block C - 4th Floor" class="form-control">
        </div>

        <button type="submit" class="btn-primary-custom" style="width:100%; justify-content:center; margin-top:8px;">
          <i class="fa-solid fa-plus"></i> Create Department
        </button>
      </form>
    </div>
  </div>

  <!-- Departments Cards List -->
  <div style="display:flex; flex-direction:column; gap:20px;">
    
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:20px;">
      <?php if ($departments && mysqli_num_rows($departments) > 0): ?>
        <?php while ($dept = mysqli_fetch_assoc($departments)): ?>
          <div class="app-card" style="margin-bottom:0; display:flex; flex-direction:column;">
            <div style="padding:22px;">
              <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
                <div>
                  <span class="badge-custom badge-active" style="margin-bottom:6px;"><?php echo htmlspecialchars($dept['department_code'] ?? 'GEN'); ?></span>
                  <h3 style="font-size:1.15rem; font-weight:700; color:var(--slate-900);"><?php echo htmlspecialchars($dept['department_name']); ?></h3>
                </div>
                <div class="metric-icon-box icon-purple" style="width:42px; height:42px; font-size:1.1rem;">
                  <i class="fa-solid fa-building"></i>
                </div>
              </div>

              <div style="font-size:0.8rem; color:var(--slate-500); margin-bottom:18px;">
                <i class="fa-solid fa-location-dot me-1 text-danger"></i><?php echo htmlspecialchars($dept['location'] ?? 'Headquarters'); ?>
              </div>

              <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:12px 16px; display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <div>
                  <div style="font-size:0.75rem; color:var(--slate-500); text-transform:uppercase; font-weight:600;">Headcount</div>
                  <div style="font-size:1.2rem; font-weight:800; color:var(--slate-900);"><?php echo $dept['total_staff']; ?> Staff</div>
                </div>
                <div>
                  <div style="font-size:0.75rem; color:var(--slate-500); text-transform:uppercase; font-weight:600;">Total Budget</div>
                  <div style="font-size:0.95rem; font-weight:700; color:var(--primary);"><?php echo format_bdt($dept['total_payroll']); ?></div>
                </div>
              </div>

              <div style="margin-top:16px;">
                <a href="index.php" class="btn-secondary-custom" style="width:100%; justify-content:center; font-size:0.82rem; padding:8px 12px;">
                  View on Dashboard <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php endif; ?>
    </div>

  </div>

</div>

<?php include_once 'includes/footer.php'; ?>

