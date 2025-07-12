<?php
require 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<h2>Invalid employee ID.</h2>';
    echo '<a href="employees.php">Back to Employee List</a>';
    exit;
}
$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$emp = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$emp) {
    echo '<h2>Employee not found.</h2>';
    echo '<a href="employees.php">Back to Employee List</a>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Details - <?= htmlspecialchars($emp['name']) ?></title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .emp-detail-container {
            max-width: 700px;
            margin: 40px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 32px 24px;
        }
        .emp-detail-title {
            text-align: center;
            color: #1976d2;
            margin-bottom: 24px;
        }
        .emp-detail-table th {
            width: 200px;
            color: #444;
            font-weight: 600;
            background: #f4f6f8;
        }
        .emp-detail-table td {
            color: #222;
        }
        .back-btn {
            margin-top: 24px;
        }
    </style>
</head>
<body>
    <?php if (file_exists('header.php')) include 'header.php'; ?>
    <div class="emp-detail-container">
        <h2 class="emp-detail-title">Employee Details</h2>
        <table class="table emp-detail-table table-bordered">
            <tr><th>Employee ID</th><td><?= htmlspecialchars($emp['id']) ?></td></tr>
            <tr><th>Name</th><td><?= htmlspecialchars($emp['name']) ?></td></tr>
            <tr><th>Department</th><td><?= htmlspecialchars($emp['department']) ?></td></tr>
            <tr><th>Basic Salary</th><td>₹<?= htmlspecialchars($emp['basic_salary']) ?></td></tr>
            <tr><th>Email</th><td><?= htmlspecialchars($emp['email']) ?></td></tr>
            <tr><th>Date of Birth</th><td><?= htmlspecialchars($emp['dob']) ?></td></tr>
            <tr><th>Company Email</th><td><?= htmlspecialchars($emp['company_email']) ?></td></tr>
            <tr><th>Date of Joining</th><td><?= htmlspecialchars($emp['date_of_joining']) ?></td></tr>
            <tr><th>Documents Submitted</th><td><?= htmlspecialchars($emp['documents_submitted']) ?></td></tr>
            <tr><th>Manager</th><td><?= htmlspecialchars($emp['manager']) ?></td></tr>
            <tr><th>Assets Given</th><td><?= htmlspecialchars($emp['assets_given']) ?></td></tr>
            <tr><th>Phone</th><td><?= htmlspecialchars($emp['phone']) ?></td></tr>
            <tr><th>Emergency Phone</th><td><?= htmlspecialchars($emp['emergency_phone']) ?></td></tr>
            <tr><th>Father's Name</th><td><?= htmlspecialchars($emp['father_name']) ?></td></tr>
            <tr><th>Address</th><td><?= htmlspecialchars($emp['address']) ?></td></tr>
            <tr><th>City</th><td><?= htmlspecialchars($emp['city']) ?></td></tr>
            <tr><th>State</th><td><?= htmlspecialchars($emp['state']) ?></td></tr>
            <tr><th>Qualification</th><td><?= htmlspecialchars($emp['qualification']) ?></td></tr>
            <tr><th>Previous Employer(s)</th><td><?= htmlspecialchars($emp['previous_employers']) ?></td></tr>
            <tr><th>Bank Name</th><td><?= htmlspecialchars($emp['bank_name']) ?></td></tr>
            <tr><th>Branch Name</th><td><?= htmlspecialchars($emp['branch_name']) ?></td></tr>
            <tr><th>Account Number</th><td><?= htmlspecialchars($emp['account_number']) ?></td></tr>
            <tr><th>IFSC Code</th><td><?= htmlspecialchars($emp['ifsc_code']) ?></td></tr>
            <tr><th>Date Added</th><td><?= htmlspecialchars($emp['created_at']) ?></td></tr>
            <tr><th>Status</th><td><?= htmlspecialchars($emp['status']) ?></td></tr>
        </table>
        <a href="employees.php" class="btn btn-secondary back-btn">&larr; Back to Employee List</a>
        <a href="edit_employee.php?id=<?= $emp['id'] ?>" class="btn btn-primary back-btn" style="margin-left:10px;">Edit</a>
    </div>
</body>
</html> 