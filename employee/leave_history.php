<?php
session_start();
if (!isset($_SESSION['employee_id'])) {
    header('Location: index.php');
    exit;
}
require '../db.php';
$employee_id = $_SESSION['employee_id'];
$result = $conn->query("SELECT * FROM leave_applications WHERE employee_id = $employee_id ORDER BY applied_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Leave History</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="container" style="max-width:800px;">
    <h2>My Leave History</h2>
    <a href="dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Type</th>
                <th>From</th>
                <th>To</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Applied At</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['leave_type']) ?></td>
                <td><?= htmlspecialchars($row['from_date']) ?></td>
                <td><?= htmlspecialchars($row['to_date']) ?></td>
                <td><?= htmlspecialchars($row['reason']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td><?= htmlspecialchars($row['applied_at']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>