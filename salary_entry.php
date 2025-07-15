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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Salary Entry</title>
    <link rel="stylesheet" href="style.css">
    <!-- Optionally include Bootstrap for better UI -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include 'header.php'; ?>
<div class="container">
    <h1>Salary Entry</h1>
    <?php if (isset($_GET['success'])): ?>
        <p style="color:green; font-weight:bold;">Salary record saved successfully!</p>
    <?php endif; ?>
    <form method="POST" id="salaryEntryForm" action="save_salary.php">
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
            <input type="number" name="allowances" id="allowances" min="0" value="0" readonly>
        </div>
        <div class="form-group">
            <label for="deductions">Other Deductions</label>
            <input type="number" name="deductions" id="deductions" min="0" value="0" readonly>
        </div>
        <div class="form-group">
            <label for="leaves">Leaves</label>
            <input type="number" name="leaves" id="leaves" min="0" value="0" readonly>
        </div>
        <div class="form-group">
            <label for="half_days">Half Days</label>
            <input type="number" name="half_days" id="half_days" min="0" value="0" readonly>
        </div>
        <div class="form-group">
            <label for="early_leaves">Early Leaves</label>
            <input type="number" name="early_leaves" id="early_leaves" min="0" value="0" readonly>
        </div>
        <div class="form-group">
            <label for="late_leaves">Late Leaves</label>
            <input type="number" name="late_leaves" id="late_leaves" min="0" value="0" readonly>
        </div>
        <div id="net_salary_display" style="font-weight:bold; font-size:1.2em; margin:10px 0;"></div>
        <button type="submit">Save Salary</button>
    </form>
</div>
<script>
function fetchSalaryData() {
    var empId = $('#employee_id').val();
    var month = $('#month').val();
    var year = $('#year').val();
    if(empId && month && year) {
        $.ajax({
            url: 'get_salary_data.php',
            type: 'POST',
            data: { employee_id: empId, month: month, year: year },
            dataType: 'json',
            success: function(data) {
                $('#allowances').val(data.allowances);
                $('#deductions').val(data.deductions);
                $('#leaves').val(data.leaves);
                $('#half_days').val(data.half_days);
                $('#early_leaves').val(data.early_leaves);
                $('#late_leaves').val(data.late_leaves);
                $('#basic_salary').val(data.basic_salary);
                $('#net_salary_display').text('Net Salary: ₹' + data.net_salary);
            }
        });
    }
}
$('#employee_id, #month, #year').change(fetchSalaryData);
</script>
</body>
</html>