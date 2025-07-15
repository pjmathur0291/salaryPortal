<?php
require 'db.php';
$result = $conn->query("SELECT * FROM salaries ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Salary Report</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1 class="mb-4">Salary Report</h1>
    <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Employee Name</th>
                <th>Basic Salary</th>
                <th>Allowances</th>
                <th>Deductions</th>
                <th>Net Salary</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['employee_name']) ?></td>
                    <td><?= $row['basic_salary'] ?></td>
                    <td><?= $row['allowances'] ?></td>
                    <td><?= $row['deductions'] ?></td>
                    <td><?= $row['net_salary'] ?></td>
                    <td><?= $row['created_at'] ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7" class="text-center">No records found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>
</body>
</html>