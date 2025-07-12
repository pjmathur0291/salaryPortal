<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
require 'db.php';

// Only allow POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

// Get and sanitize inputs
$employee_name = trim($_POST['employee_name'] ?? '');
$basic_salary  = floatval($_POST['basic_salary'] ?? 0);
$allowances    = floatval($_POST['allowances'] ?? 0);
$deductions    = floatval($_POST['deductions'] ?? 0);

// ✅ Do the calculation in PHP (not sent from frontend)
$net_salary = $basic_salary + $allowances - $deductions;

// Basic validation
if ($employee_name === '' || $basic_salary <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid input.']);
    exit;
}

// Insert into DB
$stmt = $conn->prepare("INSERT INTO salaries (employee_name, basic_salary, allowances, deductions, net_salary) VALUES (?, ?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param('sdddd', $employee_name, $basic_salary, $allowances, $deductions, $net_salary);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Saved successfully']);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
