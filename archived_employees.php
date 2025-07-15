<?php
require 'db.php';

// Fetch all inactive employees
$employees = [];
$result = $conn->query("SELECT * FROM employees WHERE status = 'inactive' ORDER BY id DESC");
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
    <title>Archived Employees</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
<div class="container">
    <h1>Archived Employees</h1>
    <?php if (isset($_GET['restored'])): ?>
        <div style="color:green; margin-bottom:10px;">Employee restored successfully.</div>
    <?php endif; ?>
    <?php if (isset($_GET['success'])): ?>
        <div style="color:green; margin-bottom:10px;">Employee archived successfully.</div>
    <?php endif; ?>
    <table border="1" class="table table-striped table-bordered align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Department</th>
                <th>Joined</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($employees) > 0): ?>
            <?php foreach ($employees as $emp): ?>
                <tr>
                    <td><?= htmlspecialchars($emp['id']) ?></td>
                    <td><?= htmlspecialchars($emp['name']) ?></td>
                    <td><?= htmlspecialchars($emp['department']) ?></td>
                    <td><?= htmlspecialchars($emp['created_at']) ?></td>
                    <td>
    <form method="POST" action="restore.php" style="display:inline;">
        <input type="hidden" name="id" value="<?= $emp['id'] ?>">
        <button type="submit" style="background:#388e3c; color:#fff; border:none; padding:6px 14px; border-radius:4px; font-weight:600; cursor:pointer;">Restore</button>
    </form>
    <form method="POST" action="delete_employee_permanent.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to permanently delete this employee? This cannot be undone!');">
        <input type="hidden" name="id" value="<?= $emp['id'] ?>">
        <button type="submit" style="background:#d32f2f; color:#fff; border:none; padding:6px 14px; border-radius:4px; font-weight:600; cursor:pointer;">Delete</button>
    </form>
</td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5">No archived employees found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>