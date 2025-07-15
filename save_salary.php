<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: salary_entry.php?error=method');
    exit;
}

$employee_id = intval($_POST['employee_id'] ?? 0);
$month = intval($_POST['month'] ?? 0);
$year = intval($_POST['year'] ?? 0);
$basic_salary = floatval($_POST['basic_salary'] ?? 0);
$allowances = floatval($_POST['allowances'] ?? 0);
$deductions = floatval($_POST['deductions'] ?? 0);
$leaves = intval($_POST['leaves'] ?? 0);
$half_days = intval($_POST['half_days'] ?? 0);
$early_leaves = intval($_POST['early_leaves'] ?? 0);
$late_leaves = intval($_POST['late_leaves'] ?? 0);

// Calculate per day salary
$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$per_day_salary = $basic_salary / $days_in_month;

// Fetch lifetime half days for this employee
$q = $conn->prepare("SELECT COUNT(*) as cnt FROM leave_applications WHERE employee_id=? AND leave_type='Half Day' AND status='approved'");
$q->bind_param('i', $employee_id);
$q->execute();
$r = $q->get_result()->fetch_assoc();
$lifetime_half_days = intval($r['cnt']);
$q->close();

// Deductible half days for this month (first half day in lifetime is free)
$deductible_half_days = max(0, $half_days - (1 - min(1, $lifetime_half_days - $half_days)));
$half_day_deduction = $deductible_half_days * ($per_day_salary / 2);

// You can add similar logic for other leave types if needed

// Total deduction
$total_deductions = $deductions + $half_day_deduction;

// Net salary
$net_salary = $basic_salary + $allowances - $total_deductions;

if ($employee_id <= 0 || $month <= 0 || $year <= 0 || $basic_salary <= 0) {
    header('Location: salary_entry.php?error=invalid');
    exit;
}

$stmt = $conn->prepare("INSERT INTO salary_records (employee_id, month, year, basic_salary, allowances, deductions, leaves, half_days, early_leaves, late_leaves, net_salary) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    header('Location: salary_entry.php?error=db');
    exit;
}
$stmt->bind_param('iiiiddiiddi', $employee_id, $month, $year, $basic_salary, $allowances, $deductions, $leaves, $half_days, $early_leaves, $late_leaves, $net_salary);

if ($stmt->execute()) {
    header('Location: salary_entry.php?success=1');
    exit;
} else {
    header('Location: salary_entry.php?error=save');
    exit;
}
$stmt->close();
$conn->close();
?>
