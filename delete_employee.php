<?php
require 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    // Check for salary records
    $stmt = $conn->prepare("SELECT COUNT(*) FROM salary_records WHERE employee_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        // Redirect with error
        header('Location: employees.php?error=has_salary');
        exit;
    }

    // Safe to delete
    $stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}
header('Location: employees.php?deleted=1');
exit; 