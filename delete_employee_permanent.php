<?php
require 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    // Optionally, delete related salary records first if you want to allow deletion
    $stmt = $conn->prepare("DELETE FROM salary_records WHERE employee_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    // Now delete the employee
    $stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}
header('Location: archived_employees.php?deleted=1');
exit;
?>