<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require 'db.php';

$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$month = isset($_GET['month']) ? intval($_GET['month']) : null;

// Only include active employees
$month_names = [
    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
];

// Fetch monthly salary data for the specified year (and month if provided), only for active employees
if ($month) {
    // Single month summary for comparison
    $sql = "SELECT sr.month, SUM(sr.net_salary) as total_salary, COUNT(*) as employee_count, AVG(sr.net_salary) as avg_salary
            FROM salary_records sr
            JOIN employees e ON sr.employee_id = e.id
            WHERE sr.year = ? AND sr.month = ? AND e.status = 'active'
            GROUP BY sr.month";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $year, $month);
    $stmt->execute();
    $result = $stmt->get_result();
    $single_month = [
        'month' => $month,
        'month_name' => $month_names[$month] ?? '',
        'total_salary' => 0,
        'employee_count' => 0,
        'avg_salary' => 0
    ];
    if ($row = $result->fetch_assoc()) {
        $single_month['total_salary'] = (float)$row['total_salary'];
        $single_month['employee_count'] = (int)$row['employee_count'];
        $single_month['avg_salary'] = (float)$row['avg_salary'];
    }
    $stmt->close();
    $monthly_data = [$single_month];
} else {
    // All months for the year
    $sql = "SELECT sr.month, SUM(sr.net_salary) as total_salary, COUNT(*) as employee_count 
            FROM salary_records sr
            JOIN employees e ON sr.employee_id = e.id
            WHERE sr.year = ? AND sr.month BETWEEN 1 AND 12 AND e.status = 'active'
            GROUP BY sr.month 
            ORDER BY sr.month ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $year);
    $stmt->execute();
    $result = $stmt->get_result();
    $monthly_data = [];
    // Initialize all months with 0
    for ($i = 1; $i <= 12; $i++) {
        $monthly_data[$i] = [
            'month_name' => $month_names[$i],
            'total_salary' => 0,
            'employee_count' => 0
        ];
    }
    while ($row = $result->fetch_assoc()) {
        $m = (int)$row['month'];
        if ($m >= 1 && $m <= 12) {
            $monthly_data[$m]['total_salary'] = (float)$row['total_salary'];
            $monthly_data[$m]['employee_count'] = (int)$row['employee_count'];
        }
    }
    $monthly_data = array_values($monthly_data);
    $stmt->close();
}

// Get total statistics for active employees only
$total_sql = "SELECT 
    COUNT(DISTINCT e.id) as total_employees,
    SUM(sr.net_salary) as total_paid,
    AVG(sr.net_salary) as avg_salary
    FROM salary_records sr
    JOIN employees e ON sr.employee_id = e.id
    WHERE sr.year = ? AND e.status = 'active'";
$total_stmt = $conn->prepare($total_sql);
$total_stmt->bind_param('i', $year);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_stats = $total_result->fetch_assoc();
$total_stats['total_employees'] = (int)($total_stats['total_employees'] ?? 0);
$total_stats['total_paid'] = (float)($total_stats['total_paid'] ?? 0);
$total_stats['avg_salary'] = (float)($total_stats['avg_salary'] ?? 0);
$total_stmt->close();

// Get available years for dropdown (only for active employees)
$years_sql = "SELECT DISTINCT sr.year FROM salary_records sr JOIN employees e ON sr.employee_id = e.id WHERE e.status = 'active' ORDER BY sr.year DESC";
$years_result = $conn->query($years_sql);
$available_years = [];
while ($row = $years_result->fetch_assoc()) {
    $available_years[] = $row['year'];
}

$conn->close();

header('Content-Type: application/json');
echo json_encode([
    'year' => $year,
    'month' => $month,
    'monthly_data' => $monthly_data,
    'single_month' => isset($single_month) ? $single_month : null,
    'total_stats' => $total_stats,
    'available_years' => $available_years,
    'success' => true
]);
?> 