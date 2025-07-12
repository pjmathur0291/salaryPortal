<?php
require 'db.php';

// Get record by ID
if (!isset($_GET['id'])) {
    header('Location: ../salary_report.php');
    exit;
}
$id = intval($_GET['id']);

// Fetch the record
$stmt = $conn->prepare("SELECT * FROM salary_records WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();
$stmt->close();

if (!$record) {
    echo "Record not found.";
    exit;
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $basic_salary = floatval($_POST['basic_salary']);
    $allowances = floatval($_POST['allowances']);
    $deductions = floatval($_POST['deductions']);
    $leaves = intval($_POST['leaves']);
    $half_days = intval($_POST['half_days']);
    $early_leaves = intval($_POST['early_leaves']);

    // Deduction logic (same as salary_entry.php)
    $free_occurrences = 1;
    $leaves_deduct = $leaves;
    $half_days_deduct = $half_days;
    $early_leaves_deduct = $early_leaves;
    if ($leaves_deduct > 0) {
        $leaves_deduct -= 1;
    } elseif ($half_days_deduct > 0) {
        $half_days_deduct -= 1;
    } elseif ($early_leaves_deduct > 0) {
        $early_leaves_deduct -= 1;
    }
    // Ensure valid month and year for cal_days_in_month
    $month = isset($record['month']) && $record['month'] >= 1 && $record['month'] <= 12 ? intval($record['month']) : 1;
    $year = isset($record['year']) && $record['year'] >= 1970 && $record['year'] <= 2100 ? intval($record['year']) : date('Y');
    $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $per_day_salary = $basic_salary / $days_in_month;
    $leave_deduction = ($leaves_deduct * 1 + $half_days_deduct * 0.5 + $early_leaves_deduct * 1) * $per_day_salary;
    $total_deductions = $deductions + $leave_deduction;
    $net_salary = $basic_salary + $allowances - $total_deductions;

    // Update the record
    $stmt = $conn->prepare("UPDATE salary_records SET basic_salary=?, allowances=?, deductions=?, leaves=?, half_days=?, early_leaves=?, net_salary=? WHERE id=?");
    $stmt->bind_param('dddiiddi', $basic_salary, $allowances, $deductions, $leaves, $half_days, $early_leaves, $net_salary, $id);
    $stmt->execute();
    $stmt->close();
    header('Location: ../salary_report.php?updated=1');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Salary Record</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Edit Salary Record</h1>
    <form method="POST">
        <div class="form-group">
            <label>Basic Salary</label>
            <input type="number" name="basic_salary" value="<?= htmlspecialchars($record['basic_salary']) ?>" min="0" required>
        </div>
        <div class="form-group">
            <label>Allowances</label>
            <input type="number" name="allowances" value="<?= htmlspecialchars($record['allowances']) ?>" min="0" required>
        </div>
        <div class="form-group">
            <label>Deductions</label>
            <input type="number" name="deductions" value="<?= htmlspecialchars($record['deductions']) ?>" min="0" required>
        </div>
        <div class="form-group">
            <label>Leaves</label>
            <input type="number" name="leaves" value="<?= htmlspecialchars($record['leaves']) ?>" min="0" required>
        </div>
        <div class="form-group">
            <label>Half Days</label>
            <input type="number" name="half_days" value="<?= htmlspecialchars($record['half_days']) ?>" min="0" required>
        </div>
        <div class="form-group">
            <label>Early Leaves</label>
            <input type="number" name="early_leaves" value="<?= htmlspecialchars($record['early_leaves']) ?>" min="0" required>
        </div>
        <button type="submit">Update</button>
    </form>
</div>
</body>
</html> 