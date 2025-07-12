<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
require 'db.php';

$sql = "SELECT * FROM salaries ORDER BY created_at DESC, id DESC";
$result = $conn->query($sql);
$rows = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
}
echo json_encode($rows);
$conn->close(); 