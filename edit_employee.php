<?php
/**
 * Edit Employee
 * Apex HR Management System
 */
require_once 'db.php';

$page_title = 'Edit Employee';
$breadcrumb = 'Workforce / Edit';

$emp_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($emp_id <= 0) {
    header('Location: employees.php');
    exit;
}

$message = '';
$message_type = '';

// Handle Update POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_name     = mysqli_real_escape_string($conn, trim($_POST['employee_name']));
    $dept_id      = intval($_POST['department_id']);
    $designation  = mysqli_real_escape_string($conn, trim($_POST['designation']));
    $email        = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone        = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $status       = mysqli_real_escape_string($conn, $_POST['status'] ?? 'Active');
    $hire_date    = mysqli_real_escape_string($conn, $_POST['hire_date']);
    
    $basic_salary = floatval($_POST['basic_salary']);
    $bonus        = floatval($_POST['bonus']);

    // Update Employee
    $upd_emp = "UPDATE Employee SET 
                employee_name = '$emp_name',
                department_id = $dept_id,
                designation = '$designation',
                email = '$email',
                phone = '$phone',
                hire_date = '$hire_date',
                status = '$status'
                WHERE employee_id = $emp_id";

    if (mysqli_query($conn, $upd_emp)) {
        // Update or Insert Salary
        $check_sal = mysqli_query($conn, "SELECT salary_id FROM Salary WHERE employee_id = $emp_id");
        if (mysqli_num_rows($check_sal) > 0) {
            $upd_sal = "UPDATE Salary SET basic_salary = $basic_salary, bonus = $bonus WHERE employee_id = $emp_id";
            mysqli_query($conn, $upd_sal);
        } else {
            $ins_sal = "INSERT INTO Salary (employee_id, basic_salary, bonus) VALUES ($emp_id, $basic_salary, $bonus)";
            mysqli_query($conn, $ins_sal);
        }

        $message = "Employee record #$emp_id successfully updated!";
        $message_type = 'success';
    } else {
        $message = "Update Error: " . mysqli_error($conn);
        $message_type = 'danger';
    }
}

// Fetch current details
$emp_query = "SELECT 
                e.*, 
                s.basic_salary, 
                s.bonus 
              FROM Employee e 
              LEFT JOIN Salary s ON e.employee_id = s.employee_id 
              WHERE e.employee_id = $emp_id";
$res = mysqli_query($conn, $emp_query);
$employee = ($res && mysqli_num_rows($res) > 0) ? mysqli_fetch_assoc($res) : null;

if (!$employee) {
    die("Employee not found.");
}

$dept_query = mysqli_query($conn, "SELECT * FROM Department ORDER BY department_name ASC");

include_once 'includes/header.php';
?>

<div class="form-container">
  
  <?php if (!empty($message)): ?>
    <div style="padding:16px 20px; border-radius:var(--radius-md); margin-bottom:24px; font-weight:600; display:flex; align-items:center; gap:12px; <?php echo ($message_type == 'success') ? 'background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0;' : 'background:#fef2f2; color:#991b1b; border:1px solid #fecaca;'; ?>">
      <i class="fa-solid <?php echo ($message_type == 'success') ? 'fa-circle-check' : 'fa-circle-exclamation'; ?>" style="font-size:1.2rem;"></i>
      <div style="flex:1;">
        <?php echo $message; ?>
        <div style="margin-top:6px; font-weight:normal; font-size:0.85rem;">
          <a href="index.php" style="color:var(--primary); font-weight:600; text-decoration:underline;">Back to Dashboard</a>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <div class="app-card">
    <div class="card-header-custom">
      <div>
        <div class="card-title">Modify Employee: #<?php echo $employee['employee_id']; ?> - <?php echo htmlspecialchars($employee['employee_name']); ?></div>
        <div class="card-subtitle">Update department, job designation, or payroll rates</div>
      </div>
      <a href="employees.php" class="btn-secondary-custom">
        <i class="fa-solid fa-arrow-left"></i> Back to Directory
      </a>
    </div>

    <div style="padding:28px 32px;">
      <form action="edit_employee.php?id=<?php echo $emp_id; ?>" method="POST">
        
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Employee ID (Read Only)</label>
            <input type="text" value="<?php echo $employee['employee_id']; ?>" class="form-control" disabled style="background:#f1f5f9; cursor:not-allowed;">
          </div>

          <div class="form-group">
            <label class="form-label" for="employee_name">Full Name *</label>
            <input type="text" id="employee_name" name="employee_name" value="<?php echo htmlspecialchars($employee['employee_name']); ?>" class="form-control" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="department_id">Department *</label>
            <select id="department_id" name="department_id" class="form-select" required>
              <?php if ($dept_query): ?>
                <?php while ($d = mysqli_fetch_assoc($dept_query)): ?>
                  <option value="<?php echo $d['department_id']; ?>" <?php echo ($employee['department_id'] == $d['department_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($d['department_name']); ?>
                  </option>
                <?php endwhile; ?>
              <?php endif; ?>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label" for="designation">Designation *</label>
            <input type="text" id="designation" name="designation" value="<?php echo htmlspecialchars($employee['designation'] ?? 'Specialist'); ?>" class="form-control" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="email">Email *</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($employee['email']); ?>" class="form-control" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="phone">Phone Number</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($employee['phone'] ?? ''); ?>" class="form-control">
          </div>

          <div class="form-group">
            <label class="form-label" for="hire_date">Join Date</label>
            <input type="date" id="hire_date" name="hire_date" value="<?php echo htmlspecialchars($employee['hire_date'] ?? date('Y-m-d')); ?>" class="form-control">
          </div>

          <div class="form-group">
            <label class="form-label" for="status">Status</label>
            <select id="status" name="status" class="form-select">
              <option value="Active" <?php echo ($employee['status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
              <option value="On Leave" <?php echo ($employee['status'] == 'On Leave') ? 'selected' : ''; ?>>On Leave</option>
              <option value="Suspended" <?php echo ($employee['status'] == 'Suspended') ? 'selected' : ''; ?>>Suspended</option>
            </select>
          </div>
        </div>

        <div style="font-size:0.8rem; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:var(--primary); margin:24px 0 14px; border-bottom:1px solid var(--border-color); padding-bottom:6px;">
          Compensation Package
        </div>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="basic_salary">Basic Salary (৳) *</label>
            <input type="number" step="0.01" id="basic_salary" name="basic_salary" value="<?php echo $employee['basic_salary'] ?? 0; ?>" class="form-control" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="bonus">Bonus / Allowance (৳)</label>
            <input type="number" step="0.01" id="bonus" name="bonus" value="<?php echo $employee['bonus'] ?? 0; ?>" class="form-control">
          </div>
        </div>

        <div style="margin-top:28px; display:flex; gap:12px;">
          <button type="submit" class="btn-primary-custom" style="padding:12px 24px;">
            <i class="fa-solid fa-check"></i> Save Changes
          </button>
          <a href="index.php" class="btn-secondary-custom" style="padding:12px 20px;">Cancel</a>
        </div>

      </form>
    </div>
  </div>

</div>

<?php include_once 'includes/footer.php'; ?>

