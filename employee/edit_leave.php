<?php
session_start();
if (!isset($_SESSION['employee_id'])) {
    header('Location: index.php');
    exit;
}
require '../db.php';

$employee_id = $_SESSION['employee_id'];
$leave_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch leave application
$stmt = $conn->prepare("SELECT * FROM leave_applications WHERE id = ? AND employee_id = ?");
$stmt->bind_param('ii', $leave_id, $employee_id);
$stmt->execute();
$result = $stmt->get_result();
$leave = $result->fetch_assoc();
$stmt->close();

if (!$leave || $leave['status'] !== 'pending') {
    echo "<div class='alert alert-danger'>Invalid or non-editable leave application.</div>";
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cancel_leave'])) {
        // Cancel the leave
        $stmt = $conn->prepare("UPDATE leave_applications SET status='cancelled' WHERE id=? AND employee_id=? AND status='pending'");
        $stmt->bind_param('ii', $leave_id, $employee_id);
        $stmt->execute();
        $stmt->close();
        header("Location: leave_history.php?cancelled=1");
        exit;
    }
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
        $days = (strtotime($to_date) - strtotime($from_date)) / (60*60*24) + 1;
        if ($days > 30) {
            $error = "Leave duration cannot exceed 30 days.";
        } else if ($leave_type === 'Paid' && $days > 1) {
            $error = "Paid leave can only be for 1 day.";
        } else {
            // Restrict to only 1 paid leave ever (excluding this leave)
            if ($leave_type === 'Paid') {
                $stmt = $conn->prepare("SELECT COUNT(*) FROM leave_applications WHERE employee_id = ? AND leave_type = 'Paid' AND id != ?");
                $stmt->bind_param('ii', $employee_id, $leave_id);
                $stmt->execute();
                $stmt->bind_result($paid_count);
                $stmt->fetch();
                $stmt->close();
                if ($paid_count >= 1) {
                    $error = "You have already used your 1 paid leave. Please select Unpaid leave.";
                }
            }
            if (!$error) {
                $sql = "UPDATE leave_applications SET leave_type=?, from_date=?, to_date=?, reason=?, late_join_time=?, half_day_option=? WHERE id=? AND employee_id=? AND status='pending'";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param(
                    "ssssssii",
                    $leave_type,
                    $from_date,
                    $to_date,
                    $reason,
                    $late_join_time,
                    $half_day_option,
                    $leave_id,
                    $employee_id
                );
                $stmt->execute();
                $stmt->close();
                $success = "Leave application updated!";
                header("Location: leave_history.php?updated=1");
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Leave Application</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'header.php'; ?>
<div class="main-content">
    <div class="container" style="max-width:600px;">
        <h2>Edit Leave Application</h2>
        <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        <form method="post">
            <div class="form-group mb-2">
                <label>Leave Type</label>
                <select name="leave_type" class="form-control" required id="leaveTypeSelect">
                    <option value="">Select Leave Type</option>
                    <option value="Paid" <?= $leave['leave_type'] === 'Paid' ? 'selected' : '' ?>>Paid Leave</option>
                    <option value="Unpaid" <?= $leave['leave_type'] === 'Unpaid' ? 'selected' : '' ?>>Unpaid Leave</option>
                    <option value="Early" <?= $leave['leave_type'] === 'Early' ? 'selected' : '' ?>>Early Leave</option>
                    <option value="Half Day" <?= $leave['leave_type'] === 'Half Day' ? 'selected' : '' ?>>Half Day</option>
                    <option value="Late Join" <?= $leave['leave_type'] === 'Late Join' ? 'selected' : '' ?>>Late Join</option>
                </select>
            </div>
            <div class="form-group mb-2" id="halfDayOptionGroup" style="display:none;">
                <label>Half Day Option</label>
                <select name="half_day_option" class="form-control">
                    <option value="">Select Option</option>
                    <option value="Joining in 2nd Half" <?= $leave['half_day_option'] === 'Joining in 2nd Half' ? 'selected' : '' ?>>Joining in 2nd Half</option>
                    <option value="Available for 1st Half" <?= $leave['half_day_option'] === 'Available for 1st Half' ? 'selected' : '' ?>>Available for 1st Half</option>
                </select>
            </div>
            <div class="form-group mb-2" id="lateJoinTimeGroup" style="display:none;">
                <label>Late Join Time</label>
                <input type="time" name="late_join_time" class="form-control" value="<?= htmlspecialchars($leave['late_join_time']) ?>">
            </div>
            <div class="form-group mb-2">
                <label>From Date</label>
                <input type="date" name="from_date" class="form-control" required value="<?= htmlspecialchars($leave['from_date']) ?>">
            </div>
            <div class="form-group mb-2">
                <label>To Date</label>
                <input type="date" name="to_date" class="form-control" required value="<?= htmlspecialchars($leave['to_date']) ?>">
            </div>
            <div class="form-group mb-2">
                <label>Reason</label>
                <textarea name="reason" class="form-control" required><?= htmlspecialchars($leave['reason']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top:10px;">Update</button>
            <button type="submit" name="cancel_leave" class="btn btn-danger" style="margin-top:10px;" onclick="return confirm('Are you sure you want to cancel this leave?');">Cancel Leave</button>
            <a href="leave_history.php" class="btn btn-secondary" style="margin-top:10px;">Back</a>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var leaveType = document.getElementById('leaveTypeSelect');
    var lateJoinGroup = document.getElementById('lateJoinTimeGroup');
    var halfDayOptionGroup = document.getElementById('halfDayOptionGroup');
    function updateFields() {
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
    }
    leaveType.addEventListener('change', updateFields);
    updateFields();
});
</script>
</body>
</html> 