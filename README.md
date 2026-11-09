# HR Management System

An Enterprise HR & Payroll Management System built with **PHP** and **MySQL / MariaDB**. Designed for efficient workforce management, daily attendance tracking, department budget distribution, payroll calculation, and relational SQL reporting.

---

## 🚀 Key Features

- **📊 Executive Dashboard (`index.php`)**:
  - Real-time KPI Metric cards: Total Employees, Operating Departments, Monthly Payroll, and Today's Attendance Rate.
  - Department headcount & budget distribution overview.
  - Daily attendance status summary (Present, Absent, Leave).

- **👥 Employee Directory & Onboarding (`employees.php`, `add_employee.php`, `edit_employee.php`)**:
  - Filter by department, real-time search by employee name, ID, or email.
  - Add and modify employee details with automated multi-table insertion (`Employee`, `Salary`, `Attendance`).
  - Safe delete with cascading relational constraints.

- **📅 Daily Attendance Tracker (`attendance.php`)**:
  - Interactive status pill buttons (Present, Absent, Leave).
  - Bulk actions: "All Present" and "All Absent" in 1 click.
  - Date navigator (Previous, Next, and Jump to Today).
  - Real-time attendance percentage rate indicator.

- **💰 Payroll & Compensation Management (`payroll.php`)**:
  - Monthly payroll calculation (Basic Pay + Performance Bonus).
  - Statutory deduction & net pay estimation.
  - **Printable Payslip generator** for each employee.

- **🏢 Department Management (`departments.php`)**:
  - Department overview with headcount and monthly payroll expenses.
  - Create and manage organizational departments.

- **📑 SQL Analytics & Reports (`reports.php`)**:
  - Multi-Table SQL `LEFT JOIN` (Employee, Department, Salary, Attendance).
  - Top 5 Highest Compensations query.
  - Department-wise aggregate cost analysis (`GROUP BY`).
  - Printable reports with SQL syntax display.

---

## 🛠️ Technology Stack

- **Backend**: PHP 8.0+
- **Database**: MySQL / MariaDB (Database: `hr_db`)
- **Frontend**: HTML5, CSS3, JavaScript (ES6)
- **UI Components**: Google Font (*Plus Jakarta Sans*), FontAwesome 6.5.1
- **Server**: Apache (via XAMPP / WAMP / Standalone)

---

## 📂 Project Structure

```
hr_management/
├── assets/
│   ├── css/
│   │   └── style.css          # Master stylesheet & design system
│   └── js/
│       └── script.js          # Interactive client scripts
├── includes/
│   ├── header.php             # HTML head & top navigation bar
│   ├── sidebar.php            # Left collapsible sidebar navigation
│   └── footer.php             # Footer & script inclusions
├── add_employee.php           # Onboarding form for new staff
├── attendance.php             # Daily attendance sheet & logger
├── database.sql               # MySQL relational schema & seed data
├── db.php                     # Database connection & global helper functions
├── delete_employee.php        # Delete employee controller
├── departments.php            # Department management & metrics
├── edit_employee.php          # Update employee & salary records
├── employees.php              # Full employee directory with filters
├── index.php                  # Executive HR dashboard
├── payroll.php                # Compensation & printable payslips
├── reports.php                # Advanced SQL reports & analytics
└── README.md                  # Project documentation
```

---

## ⚙️ Installation & Setup

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/sojibahmed404/HR-Management-System.git
   ```

2. **Move to Web Root**:
   Place the project folder inside your web server's root directory (e.g., `C:/xampp/htdocs/hr_management/` or inside your virtual host).

3. **Start Apache & MySQL**:
   Open XAMPP Control Panel and start **Apache** and **MySQL**.

4. **Import Database**:
   - Open **phpMyAdmin** (`http://localhost/phpmyadmin/`) or MySQL CLI.
   - Run the provided `database.sql` script to create the `hr_db` database and seed records:
     ```bash
     mysql -u root < database.sql
     ```

5. **Configure Database Connection**:
   Update `db.php` if your MySQL username, password, or port differs:
   ```php
   define('DB_HOST', '127.0.0.1');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'hr_db');
   ```

6. **Launch Application**:
   Open your browser and navigate to:
   ```
   http://localhost/hr_management/
   ```

---

## 📄 License
This project is open source and available under the [MIT License](LICENSE).
