<?php
session_start();
if (!isset($_SESSION['employee_id'])) {
    header('Location: index.php');
    exit;
}
require '../db.php';
$id = $_SESSION['employee_id'];
$stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$emp = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="container" style="max-width:700px;">
    <h2>Welcome, <?= htmlspecialchars($emp['name']) ?></h2>
    <p><b>Department:</b> <?= htmlspecialchars($emp['department']) ?></p>
    <p><b>Email:</b> <?= htmlspecialchars($emp['email']) ?></p>
    <a href="apply_leave.php" class="btn btn-primary">Apply for Leave</a>
    <a href="leave_history.php" class="btn btn-secondary">My Leave History</a>
    <a href="logout.php" class="btn btn-danger" style="float:right;">Logout</a>
</div>
</body>
</html>