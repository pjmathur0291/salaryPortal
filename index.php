<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Calculator</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h1>Salary Calculator</h1>
        <form id="salaryForm" method="post" action="save_salary.php">
            <div class="form-group">
                <label for="employee_name">Employee Name</label>
                <input type="text" id="employee_name" name="employee_name" required>
            </div>
            <div class="form-group">
                <label for="basic_salary">Basic Salary</label>
                <input type="number" id="basic_salary" name="basic_salary" min="0" required>
            </div>
            <div class="form-group">
                <label for="allowances">Allowances</label>
                <input type="number" id="allowances" name="allowances" min="0" value="0">
            </div>
            <div class="form-group">
                <label for="deductions">Deductions</label>
                <input type="number" id="deductions" name="deductions" min="0" value="0">
            </div>
            <div class="form-group">
                <label for="net_salary">Net Salary</label>
                <input type="number" id="net_salary" name="net_salary" readonly>
            </div>
            <button type="submit">Save Salary</button>
        </form>

        <h2>Salary Records</h2>
        <table id="salaryTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Employee Name</th>
                    <th>Basic Salary</th>
                    <th>Allowances</th>
                    <th>Deductions</th>
                    <th>Net Salary</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
<?php
require 'db.php';
$sql = "SELECT * FROM salaries ORDER BY created_at DESC, id DESC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['employee_name']}</td>
            <td>{$row['basic_salary']}</td>
            <td>{$row['allowances']}</td>
            <td>{$row['deductions']}</td>
            <td>{$row['net_salary']}</td>
            <td>{$row['created_at']}</td>
        </tr>";
    }
} else {
    echo '<tr><td colspan="7">No records found.</td></tr>';
}
$conn->close();
?>
</tbody>
        </table>
    </div>
    <script src="script.js"></script>
</body>
</html>
