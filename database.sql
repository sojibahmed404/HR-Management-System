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
    `phone` VARCHAR(30) DEFAULT '+880 1700-000000',
    `hire_date` DATE DEFAULT '2024-01-15',
    `status` ENUM('Active', 'On Leave', 'Suspended') DEFAULT 'Active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`department_id`) REFERENCES `Department`(`department_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Employees
INSERT INTO `Employee` (`employee_id`, `employee_name`, `department_id`, `designation`, `email`, `phone`, `hire_date`, `status`) VALUES
(101, 'Alice Smith', 1, 'Senior HR Manager', 'alice@company.com', '+880 1711-234567', '2022-03-01', 'Active'),
(102, 'Bob Johnson', 2, 'Financial Analyst', 'bob@company.com', '+880 1722-345678', '2023-01-15', 'Active'),
(103, 'Charlie Brown', 3, 'Lead Software Engineer', 'charlie@company.com', '+880 1733-456789', '2021-06-10', 'Active'),
(104, 'Diana Prince', 4, 'Creative Marketing Director', 'diana@company.com', '+880 1744-567890', '2023-04-20', 'Active'),
(105, 'Edward Norton', 3, 'DevOps Specialist', 'edward@company.com', '+880 1755-678901', '2022-09-05', 'Active'),
(106, 'Fatima Rahman', 1, 'Talent Acquisition Officer', 'fatima.r@company.com', '+880 1766-789012', '2024-02-01', 'Active'),
(107, 'Kamal Hossain', 2, 'Senior Accountant', 'kamal.h@company.com', '+880 1777-890123', '2022-11-12', 'Active'),
(108, 'Sarah Jenkins', 3, 'Full Stack Developer', 'sarah.j@company.com', '+880 1788-901234', '2024-05-18', 'Active'),
(109, 'Tanvir Ahmed', 5, 'Logistics Manager', 'tanvir.a@company.com', '+880 1799-012345', '2023-08-25', 'Active'),
(110, 'Nusrat Jahan', 4, 'Digital Media Strategist', 'nusrat.j@company.com', '+880 1811-123456', '2024-03-10', 'On Leave');

-- 3. Salary Table
CREATE TABLE `Salary` (
    `salary_id` INT AUTO_INCREMENT PRIMARY KEY,
    `employee_id` INT NOT NULL UNIQUE,
    `basic_salary` DECIMAL(10,2) NOT NULL,
    `bonus` DECIMAL(10,2) DEFAULT 0.00,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`employee_id`) REFERENCES `Employee`(`employee_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Salary
INSERT INTO `Salary` (`salary_id`, `employee_id`, `basic_salary`, `bonus`) VALUES
(1, 101, 55000.00, 6000.00),
(2, 102, 60000.00, 5500.00),
(3, 103, 85000.00, 10000.00),
(4, 104, 58000.00, 6500.00),
(5, 105, 75000.00, 8000.00),
(6, 106, 42000.00, 4000.00),
(7, 107, 65000.00, 7000.00),
(8, 108, 70000.00, 7500.00),
(9, 109, 52000.00, 4500.00),
(10, 110, 48000.00, 3500.00);

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

-- Seed Attendance (Dates across recent days)
INSERT INTO `Attendance` (`employee_id`, `attendance_date`, `status`, `notes`) VALUES
(101, CURDATE(), 'Present', 'On time (09:02 AM)'),
(102, CURDATE(), 'Present', 'On time (08:55 AM)'),
(103, CURDATE(), 'Present', 'Remote - Approved'),
(104, CURDATE(), 'Present', 'On time (09:10 AM)'),
(105, CURDATE(), 'Present', 'On time (08:45 AM)'),
(106, CURDATE(), 'Present', 'On time (09:05 AM)'),
(107, CURDATE(), 'Absent', 'Medical reason notified'),
(108, CURDATE(), 'Present', 'On time (09:12 AM)'),
(109, CURDATE(), 'Present', 'Field duty - Hub 1'),
(110, CURDATE(), 'Leave', 'Approved Casual Leave'),

-- Yesterday Attendance
(101, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Present', 'On time'),
(102, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Absent', 'No notice'),
(103, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Present', 'On time'),
(104, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Present', 'On time'),
(105, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Leave', 'Sick leave'),
(106, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Present', 'On time'),
(107, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Present', 'On time'),
(108, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Present', 'On time'),
(109, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Present', 'On time'),
(110, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Leave', 'Approved Leave');
