<?php
session_start();
if (!isset($_SESSION['employee_id'])) {
    header('Location: index.php');
    exit;
}
require '../db.php';
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = $_SESSION['employee_id'];
    $leave_type = $_POST['leave_type'];
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $reason = $_POST['reason'];
    if (strtotime($from_date) > strtotime($to_date)) {
        $error = "From date cannot be after To date.";
    } else {
        $stmt = $conn->prepare("INSERT INTO leave_applications (employee_id, leave_type, from_date, to_date, reason) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('issss', $employee_id, $leave_type, $from_date, $to_date, $reason);
        $stmt->execute();
        $stmt->close();
        $success = "Leave application submitted!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Apply for Leave</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="container" style="max-width:600px;">
    <h2>Apply for Leave</h2>
    <?php if ($success): ?><div style="color:green;"><?= $success ?></div><?php endif; ?>
    <?php if ($error): ?><div style="color:red;"><?= $error ?></div><?php endif; ?>
    <form method="post">
        <div class="form-group">
            <label>Leave Type</label>
            <select name="leave_type" class="form-control" required>
                <option value="Paid">Paid</option>
                <option value="Unpaid">Unpaid</option>
            </select>
        </div>
        <div class="form-group">
            <label>From Date</label>
            <input type="date" name="from_date" class="form-control" required>
        </div>
        <div class="form-group">
            <label>To Date</label>
            <input type="date" name="to_date" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Reason</label>
            <textarea name="reason" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top:10px;">Apply</button>
        <a href="dashboard.php" class="btn btn-secondary" style="margin-top:10px;">Back</a>
    </form>
</div>
</body>
</html>