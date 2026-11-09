-- ========================================================
-- Apex Global HR & Payroll Management Database
-- Database: hr_db
-- ========================================================

CREATE DATABASE IF NOT EXISTS `hr_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `hr_db`;

-- Drop existing tables in correct FK order
DROP TABLE IF EXISTS `Attendance`;
DROP TABLE IF EXISTS `Salary`;
DROP TABLE IF EXISTS `Employee`;
DROP TABLE IF EXISTS `Department`;

-- 1. Department Table
CREATE TABLE `Department` (
    `department_id` INT AUTO_INCREMENT PRIMARY KEY,
    `department_name` VARCHAR(100) NOT NULL,
    `department_code` VARCHAR(20) DEFAULT 'GEN',
    `location` VARCHAR(100) DEFAULT 'Headquarters',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Departments
INSERT INTO `Department` (`department_id`, `department_name`, `department_code`, `location`) VALUES
(1, 'Human Resources', 'HR', 'Block A - 4th Floor'),
(2, 'Finance & Accounts', 'FIN', 'Block B - 3rd Floor'),
(3, 'IT & Engineering', 'IT', 'Tech Tower - 6th Floor'),
(4, 'Marketing & Sales', 'MKT', 'Block A - 2nd Floor'),
(5, 'Operations & Logistics', 'OPS', 'Warehouse Hub - 1st Floor');

-- 2. Employee Table
CREATE TABLE `Employee` (
    `employee_id` INT PRIMARY KEY,
    `employee_name` VARCHAR(100) NOT NULL,
    `department_id` INT NULL,
    `designation` VARCHAR(100) DEFAULT 'Specialist',
    `email` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(30) DEFAULT '01700000000',
    `hire_date` DATE DEFAULT '2024-01-15',
    `status` ENUM('Active', 'On Leave', 'Suspended') DEFAULT 'Active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`department_id`) REFERENCES `Department`(`department_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed 18 Real Employees
INSERT INTO `Employee` (`employee_id`, `employee_name`, `department_id`, `designation`, `email`, `phone`, `hire_date`, `status`) VALUES
(101, 'Md. Rakibul Islam', 3, 'Senior Software Engineer', 'rakibul19980101@gmail.com', '01643623400', '2023-01-10', 'Active'),
(102, 'Siam Hossain', 3, 'Full Stack Developer', 'siamhossain.cse18@gmail.com', '01717412612', '2023-03-15', 'Active'),
(103, 'Md. Radoun Hossin Mukta', 3, 'Database Administrator', 'redwanhossain957@gmail.com', '01797560474', '2022-11-01', 'Active'),
(104, 'Md. Shahariar Ahmed Kanon', 2, 'Financial Analyst', 'shahariarkanon816@gmail.com', '01845981362', '2023-05-20', 'Active'),
(105, 'Mst. Anamika Jahan', 1, 'HR Operations Lead', 'anamikajahann12@gmail.com', '01738128521', '2022-08-15', 'Active'),
(106, 'Md. Sojib Ahmed', 3, 'Lead Systems Architect', 'mdsojibahmed544@gmail.com', '01754301132', '2021-06-01', 'Active'),
(107, 'Mst. Ananna Khandaker', 4, 'Brand & Marketing Strategist', 'anuananna177@gmail.com', '01403598685', '2023-07-12', 'Active'),
(108, 'Md. Rakib Hasan Riyad', 2, 'Senior Accounts Officer', 'hasanrk2005@gmail.com', '01554839357', '2022-10-18', 'Active'),
(109, 'Most. Khadija Khatun', 1, 'Talent Acquisition Specialist', 'tanzinmaria0101@gmail.com', '01861111297', '2024-01-15', 'Active'),
(110, 'Md. Rezwan Ahmed Ratul', 3, 'Frontend Engineer', 'rezwanahmedratul007@gmail.com', '01603521519', '2023-09-01', 'Active'),
(111, 'Maruf Ibna Abdullah Rifat', 4, 'Digital Content Executive', 'marufibneabdullah51@gmail.com', '01870632063', '2024-02-10', 'Active'),
(112, 'Md. Rabbi', 5, 'Supply Chain Coordinator', 'rabbimollah2269@gmail.com', '01719712269', '2023-04-05', 'Active'),
(113, 'Mst. Mushkat Jahan Shila', 4, 'Public Relations Officer', 'mashkatshila@gmail.com', '01444444555', '2023-11-20', 'Active'),
(114, 'Ijaj Ahmed Rafi', 3, 'Network & Security Engineer', 'rafi01752@gmail.com', '01752909577', '2022-12-05', 'Active'),
(115, 'Md. Sabbir Hossain Rahat', 5, 'Logistics Operations Manager', 'rahatbabu122@gmail.com', '01798819616', '2023-02-25', 'Active'),
(116, 'Abid Hasan Hujaifa', 2, 'Tax & Compliance Executive', 'abidhasanhujaifa1215@gmail.com', '01302451935', '2024-03-01', 'Active'),
(117, 'Sabiha Rumman Medha', 1, 'Employee Relations Specialist', 'sabiha.rumman.10@gmail.com', '01814932244', '2023-08-10', 'Active'),
(118, 'Md. Intaj Hassan Nibir', 3, 'Quality Assurance Engineer', 'intajnibir07@gmail.com', '01721799926', '2024-01-05', 'Active');

-- 3. Salary Table
CREATE TABLE `Salary` (
    `salary_id` INT AUTO_INCREMENT PRIMARY KEY,
    `employee_id` INT NOT NULL UNIQUE,
    `basic_salary` DECIMAL(10,2) NOT NULL,
    `bonus` DECIMAL(10,2) DEFAULT 0.00,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`employee_id`) REFERENCES `Employee`(`employee_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Salary for 18 Employees
INSERT INTO `Salary` (`salary_id`, `employee_id`, `basic_salary`, `bonus`) VALUES
(1, 101, 75000.00, 8000.00),
(2, 102, 65000.00, 6000.00),
(3, 103, 70000.00, 7000.00),
(4, 104, 58000.00, 5000.00),
(5, 105, 62000.00, 6500.00),
(6, 106, 85000.00, 10000.00),
(7, 107, 55000.00, 5000.00),
(8, 108, 60000.00, 5500.00),
(9, 109, 52000.00, 4500.00),
(10, 110, 58000.00, 5000.00),
(11, 111, 48000.00, 4000.00),
(12, 112, 50000.00, 4000.00),
(13, 113, 52000.00, 4500.00),
(14, 114, 68000.00, 6000.00),
(15, 115, 56000.00, 5000.00),
(16, 116, 54000.00, 4500.00),
(17, 117, 53000.00, 4500.00),
(18, 118, 56000.00, 5000.00);

-- 4. Attendance Table
CREATE TABLE `Attendance` (
    `attendance_id` INT AUTO_INCREMENT PRIMARY KEY,
    `employee_id` INT NOT NULL,
    `attendance_date` DATE NOT NULL,
    `status` VARCHAR(50) NOT NULL DEFAULT 'Present',
    `notes` VARCHAR(255) DEFAULT '',
    `recorded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`employee_id`) REFERENCES `Employee`(`employee_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Attendance for All 18 Employees (Today)
INSERT INTO `Attendance` (`employee_id`, `attendance_date`, `status`) VALUES
(101, CURDATE(), 'Present'),
(102, CURDATE(), 'Present'),
(103, CURDATE(), 'Present'),
(104, CURDATE(), 'Present'),
(105, CURDATE(), 'Present'),
(106, CURDATE(), 'Present'),
(107, CURDATE(), 'Present'),
(108, CURDATE(), 'Present'),
(109, CURDATE(), 'Present'),
(110, CURDATE(), 'Present'),
(111, CURDATE(), 'Present'),
(112, CURDATE(), 'Present'),
(113, CURDATE(), 'Present'),
(114, CURDATE(), 'Present'),
(115, CURDATE(), 'Present'),
(116, CURDATE(), 'Present'),
(117, CURDATE(), 'Present'),
(118, CURDATE(), 'Present');
