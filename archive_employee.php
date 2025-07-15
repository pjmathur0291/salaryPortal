<?php
require 'db.php';
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("UPDATE employees SET status = 'inactive' WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    header('Location: archived_employees.php?success=1');
} else {
    header('Location: employee_list.php?error=1');
}
exit; 