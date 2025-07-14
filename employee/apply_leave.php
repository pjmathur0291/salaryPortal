<?php
session_start();
if (!isset($_SESSION['employee_id'])) {
    header('Location: index.php');
    exit;
}
require '../db.php';
// Add error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = $_SESSION['employee_id'];
    $leave_type = isset($_POST['leave_type']) ? trim($_POST['leave_type']) : null;
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $reason = $_POST['reason'];
    $late_join_time = isset($_POST['late_join_time']) && $_POST['late_join_time'] !== '' ? $_POST['late_join_time'] : null;
    $half_day_option = isset($_POST['half_day_option']) && $_POST['half_day_option'] !== '' ? $_POST['half_day_option'] : null;
    if (strtotime($from_date) > strtotime($to_date)) {
        $error = "From date cannot be after To date.";
    } else if (empty($leave_type)) {
        $error = "Please select a leave type.";
    } else {
        // Calculate number of days
        $days = (strtotime($to_date) - strtotime($from_date)) / (60*60*24) + 1;
        if ($days > 30) {
            $error = "Leave duration cannot exceed 30 days.";
        } else if ($leave_type === 'Paid' && $days > 1) {
            $error = "Paid leave can only be for 1 day.";
        } else {
            // Restrict to only 1 paid leave ever (regardless of approval status)
            if ($leave_type === 'Paid') {
                $stmt = $conn->prepare("SELECT COUNT(*) FROM leave_applications WHERE employee_id = ? AND leave_type = 'Paid'");
                $stmt->bind_param('i', $employee_id);
                $stmt->execute();
                $stmt->bind_result($paid_count);
                $stmt->fetch();
                $stmt->close();
                if ($paid_count >= 1) {
                    $error = "You have already used your 1 paid leave. Please select Unpaid leave.";
                }
            }
            if (!$error) {
                // Always insert all columns, use NULL for optional ones
                $sql = "INSERT INTO leave_applications (employee_id, leave_type, from_date, to_date, reason, late_join_time, half_day_option)
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param(
                    "issssss",
                    $employee_id,
                    $leave_type,
                    $from_date,
                    $to_date,
                    $reason,
                    $late_join_time,
                    $half_day_option
                );
                $stmt->execute();
                $stmt->close();
                $success = "Leave application submitted!";
            }
        }
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
    <?php include 'header.php' ?>
<div class="container" style="max-width:600px;">
    <h2>Apply for Leave</h2>
    <?php if ($success): ?><div style="color:green;"> <?= $success ?> </div><?php endif; ?>
    <?php if ($error): ?><div style="color:red;"> <?= $error ?> </div><?php endif; ?>
    <form method="post">
        <div class="form-group">
            <label>Leave Type</label>
            <select name="leave_type" class="form-control" required id="leaveTypeSelect">
                <option value="">Select Leave Type</option>
                <option value="Paid">Paid Leave</option>
                <option value="Unpaid">Unpaid Leave</option>
                <option value="Early">Early Leave</option>
                <option value="Half Day">Half Day</option>
                <option value="Late Join">Late Join</option>
            </select>
        </div>
        <div class="form-group" id="halfDayOptionGroup" style="display:none;">
            <label>Half Day Option</label>
            <select name="half_day_option" class="form-control">
                <option value="">Select Option</option>
                <option value="Joining in 2nd Half">Joining in 2nd Half</option>
                <option value="Available for 1st Half">Available for 1st Half</option>
            </select>
        </div>
        <div class="form-group" id="lateJoinTimeGroup" style="display:none;">
            <label>Late Join Time</label>
            <input type="time" name="late_join_time" class="form-control">
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    var leaveType = document.getElementById('leaveTypeSelect');
    var lateJoinGroup = document.getElementById('lateJoinTimeGroup');
    var halfDayOptionGroup = document.getElementById('halfDayOptionGroup');
    leaveType.addEventListener('change', function() {
        if (leaveType.value === 'Late Join') {
            lateJoinGroup.style.display = '';
        } else {
            lateJoinGroup.style.display = 'none';
        }
        if (leaveType.value === 'Half Day') {
            halfDayOptionGroup.style.display = '';
        } else {
            halfDayOptionGroup.style.display = 'none';
        }
    });
    // Trigger on page load in case of form resubmission
    if (leaveType.value === 'Late Join') {
        lateJoinGroup.style.display = '';
    }
    if (leaveType.value === 'Half Day') {
        halfDayOptionGroup.style.display = '';
    }
});
</script>
</body>
</html>