<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    exit('Unauthorized');
}

require 'db.php';

if (isset($_GET['employee_id']) && isset($_GET['month']) && isset($_GET['year'])) {
    $employee_id = intval($_GET['employee_id']);
    $month = intval($_GET['month']);
    $year = intval($_GET['year']);
    
    // Calculate leave days for the specified month/year
    $sql = "SELECT SUM(
                CASE 
                    WHEN leave_type = 'Unpaid' THEN 
                        DATEDIFF(to_date, from_date) + 1
                    ELSE 0 
                END
            ) as unpaid_days
            FROM leave_applications 
            WHERE employee_id = ? 
            AND status = 'approved'
            AND MONTH(from_date) = ? 
            AND YEAR(from_date) = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iii', $employee_id, $month, $year);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $leave_days = $row['unpaid_days'] ?? 0;
    
    header('Content-Type: application/json');
    echo json_encode(['leave_days' => intval($leave_days)]);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameters']);
}
?> 