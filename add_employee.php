<?php
/**
 * Add New Employee
 * Apex HR Management System
 */
require_once 'db.php';

$page_title = 'Add New Employee';
$breadcrumb = 'Workforce / Add Employee';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_id       = intval($_POST['employee_id']);
    $emp_name     = mysqli_real_escape_string($conn, trim($_POST['employee_name']));
    $dept_id      = intval($_POST['department_id']);
    $designation  = mysqli_real_escape_string($conn, trim($_POST['designation']));
    $email        = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone        = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $hire_date    = !empty($_POST['hire_date']) ? mysqli_real_escape_string($conn, $_POST['hire_date']) : date('Y-m-d');
    $status       = mysqli_real_escape_string($conn, $_POST['status'] ?? 'Active');
    
    $basic_salary = floatval($_POST['basic_salary']);
    $bonus        = floatval($_POST['bonus']);

    // Check duplicate ID
    $check_res = mysqli_query($conn, "SELECT employee_id FROM Employee WHERE employee_id = $emp_id");
    if (mysqli_num_rows($check_res) > 0) {
        $message = "Employee ID #$emp_id is already assigned to another employee!";
        $message_type = 'danger';
    } else {
        // 1. Insert Employee
        $sql_emp = "INSERT INTO Employee (employee_id, employee_name, department_id, designation, email, phone, hire_date, status)
                    VALUES ($emp_id, '$emp_name', $dept_id, '$designation', '$email', '$phone', '$hire_date', '$status')";
        
        if (mysqli_query($conn, $sql_emp)) {
            // 2. Insert Salary
            $sql_sal = "INSERT INTO Salary (employee_id, basic_salary, bonus)
                        VALUES ($emp_id, $basic_salary, $bonus)";
            mysqli_query($conn, $sql_sal);

            // 3. Insert Today's Attendance
            $today = date('Y-m-d');
            $sql_att = "INSERT INTO Attendance (employee_id, attendance_date, status, notes)
                        VALUES ($emp_id, '$today', 'Present', 'New onboarding attendance')";
            mysqli_query($conn, $sql_att);

            $message = "Employee #$emp_id ($emp_name) has been successfully added to MySQL database!";
            $message_type = 'success';
        } else {
            $message = "Database Error: " . mysqli_error($conn);
            $message_type = 'danger';
        }
    }
}

// Fetch Departments
$dept_query = mysqli_query($conn, "SELECT * FROM Department ORDER BY department_name ASC");

// Auto-suggest next employee ID
$next_id_res = mysqli_query($conn, "SELECT MAX(employee_id) as max_id FROM Employee");
$next_id = 101;
if ($next_id_res && $row = mysqli_fetch_assoc($next_id_res)) {
    $next_id = intval($row['max_id']) + 1;
}

include_once 'includes/header.php';
?>

<div class="form-container">
  
  <?php if (!empty($message)): ?>
    <div style="padding:16px 20px; border-radius:var(--radius-md); margin-bottom:24px; font-weight:600; display:flex; align-items:center; gap:12px; <?php echo ($message_type == 'success') ? 'background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0;' : 'background:#fef2f2; color:#991b1b; border:1px solid #fecaca;'; ?>">
      <i class="fa-solid <?php echo ($message_type == 'success') ? 'fa-circle-check' : 'fa-circle-exclamation'; ?>" style="font-size:1.2rem;"></i>
      <div style="flex:1;">
        <?php echo $message; ?>
        <?php if ($message_type == 'success'): ?>
          <div style="margin-top:6px; font-weight:normal; font-size:0.85rem;">
            <a href="index.php" style="color:var(--primary); font-weight:600; text-decoration:underline;">Go to Dashboard</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>

  <div class="app-card">
    <div class="card-header-custom">
      <div>
        <div class="card-title">Employee Onboarding Form</div>
        <div class="card-subtitle">Fill in employee credentials, department assignment, and payroll structure</div>
      </div>
      <a href="employees.php" class="btn-secondary-custom">
        <i class="fa-solid fa-arrow-left"></i> Back to Directory
      </a>
    </div>

    <div style="padding:28px 32px;">
      <form action="add_employee.php" method="POST">
        
        <div style="font-size:0.8rem; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:var(--primary); margin-bottom:14px; border-bottom:1px solid var(--border-color); padding-bottom:6px;">
          1. Basic & Professional Information
        </div>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="employee_id">Employee ID *</label>
            <input type="number" id="employee_id" name="employee_id" value="<?php echo $next_id; ?>" class="form-control" required>
            <small style="color:var(--slate-500); font-size:0.75rem;">Unique company numerical ID</small>
          </div>

          <div class="form-group">
            <label class="form-label" for="employee_name">Full Name *</label>
            <input type="text" id="employee_name" name="employee_name" placeholder="e.g. Rachel Adams" class="form-control" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="department_id">Department *</label>
            <select id="department_id" name="department_id" class="form-select" required>
              <option value="">-- Choose Department --</option>
              <?php if ($dept_query): ?>
                <?php while ($d = mysqli_fetch_assoc($dept_query)): ?>
                  <option value="<?php echo $d['department_id']; ?>">
                    <?php echo htmlspecialchars($d['department_name']); ?> (<?php echo htmlspecialchars($d['department_code']); ?>)
                  </option>
                <?php endwhile; ?>
              <?php endif; ?>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label" for="designation">Job Title / Designation *</label>
            <input type="text" id="designation" name="designation" placeholder="e.g. Senior Software Engineer" class="form-control" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="email">Work Email *</label>
            <input type="email" id="email" name="email" placeholder="e.g. rachel@company.com" class="form-control" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="phone">Phone Number</label>
            <input type="text" id="phone" name="phone" placeholder="+880 1700-000000" class="form-control">
          </div>

          <div class="form-group">
            <label class="form-label" for="hire_date">Date of Joining</label>
            <input type="date" id="hire_date" name="hire_date" value="<?php echo date('Y-m-d'); ?>" class="form-control">
          </div>

          <div class="form-group">
            <label class="form-label" for="status">Employment Status</label>
            <select id="status" name="status" class="form-select">
              <option value="Active" selected>Active</option>
              <option value="On Leave">On Leave</option>
              <option value="Suspended">Suspended</option>
            </select>
          </div>
        </div>

        <div style="font-size:0.8rem; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:var(--primary); margin:24px 0 14px; border-bottom:1px solid var(--border-color); padding-bottom:6px;">
          2. Compensation & Payroll Structure
        </div>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="basic_salary">Monthly Basic Salary (৳) *</label>
            <input type="number" step="0.01" id="basic_salary" name="basic_salary" placeholder="60000.00" class="form-control" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="bonus">Monthly Bonus / Allowances (৳)</label>
            <input type="number" step="0.01" id="bonus" name="bonus" placeholder="5000.00" value="0.00" class="form-control">
          </div>
        </div>

        <div style="margin-top:28px; display:flex; gap:12px;">
          <button type="submit" class="btn-primary-custom" style="padding:12px 24px; font-size:0.95rem;">
            <i class="fa-solid fa-save"></i> Save Employee Record
          </button>
          <a href="index.php" class="btn-secondary-custom" style="padding:12px 20px;">Cancel</a>
        </div>

      </form>
    </div>
  </div>

</div>

<?php include_once 'includes/footer.php'; ?>
