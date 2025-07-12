<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
require 'db.php';

// Fetch employees for dropdown (only active employees)
$employees = [];
$result = $conn->query("SELECT * FROM employees WHERE status = 'active' ORDER BY name ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['employee_id'])) {
    $employee_id = intval($_POST['employee_id']);
    $month = intval($_POST['month']);
    $year = intval($_POST['year']);
    $basic_salary = floatval($_POST['basic_salary']);
    $allowances = floatval($_POST['allowances']);
    $deductions = floatval($_POST['deductions']);
    $leaves = intval($_POST['leaves']);
    $half_days = intval($_POST['half_days']);
    $early_leaves = intval($_POST['early_leaves']);

    // Deduction logic: first occurrence is free
    $free_occurrences = 1;
    $leaves_deduct = $leaves;
    $half_days_deduct = $half_days;
    $early_leaves_deduct = $early_leaves;

    // Subtract the free occurrence from the first available type
    if ($leaves_deduct > 0) {
        $leaves_deduct -= 1;
    } elseif ($half_days_deduct > 0) {
        $half_days_deduct -= 1;
    } elseif ($early_leaves_deduct > 0) {
        $early_leaves_deduct -= 1;
    }

    // Use actual days in the selected month
    $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $per_day_salary = $basic_salary / $days_in_month;
    $leave_deduction = ($leaves_deduct * 1 + $half_days_deduct * 0.5 + $early_leaves_deduct * 1) * $per_day_salary;

    $total_deductions = $deductions + $leave_deduction;
    $net_salary = $basic_salary + $allowances - $total_deductions;

    // Insert into salary_records
    $stmt = $conn->prepare("INSERT INTO salary_records (employee_id, month, year, basic_salary, allowances, deductions, leaves, half_days, early_leaves, net_salary) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('iiiiddiiid', $employee_id, $month, $year, $basic_salary, $allowances, $deductions, $leaves, $half_days, $early_leaves, $net_salary);
    $stmt->execute();
    $stmt->close();

    header("Location: salary_entry.php?success=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Salary Entry</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
<div class="container">
    <div style="text-align:right; margin-bottom:10px;">
        <form action="logout.php" method="post" style="display:inline;">
            <button type="submit" style="background:#d32f2f; color:#fff; border:none; padding:8px 18px; border-radius:5px; font-weight:600; cursor:pointer;">Logout</button>
        </form>
    </div>
    <h1>Salary Entry</h1>
    <?php if (isset($_GET['success'])): ?>
        <p style="color:green;">Salary record saved!</p>
    <?php endif; ?>
    <form method="POST" id="salaryEntryForm">
        <div class="form-group">
            <label for="employee_id">Employee</label>
            <select name="employee_id" id="employee_id" required>
                <option value="">Select Employee</option>
                <?php foreach ($employees as $emp): ?>
                    <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="month">Month</label>
            <select name="month" id="month" required>
                <?php
                for ($m = 1; $m <= 12; $m++) {
                    $monthName = date('F', mktime(0, 0, 0, $m, 10));
                    echo "<option value=\"$m\">$monthName</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="year">Year</label>
            <input type="number" name="year" id="year" value="<?= date('Y') ?>" required>
        </div>
        <div class="form-group">
            <label for="basic_salary">Basic Salary</label>
            <input type="number" name="basic_salary" id="basic_salary" min="0" required>
        </div>
        <div class="form-group">
            <label for="allowances">Allowances</label>
            <input type="number" name="allowances" id="allowances" min="0" value="0">
        </div>
        <div class="form-group">
            <label for="deductions">Other Deductions</label>
            <input type="number" name="deductions" id="deductions" min="0" value="0">
        </div>
        <div class="form-group">
            <label for="leaves">Leaves</label>
            <input type="number" name="leaves" id="leaves" min="0" value="0">
        </div>
        <div class="form-group">
            <label for="half_days">Half Days</label>
            <input type="number" name="half_days" id="half_days" min="0" value="0">
        </div>
        <div class="form-group">
            <label for="early_leaves">Early Leaves</label>
            <input type="number" name="early_leaves" id="early_leaves" min="0" value="0">
        </div>
        <button type="submit">Save Salary</button>
    </form>
</div>
</body>
</html>