<?php
// Test version of get_salary_data.php without session authentication
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php';

$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

echo "<!-- Debug: Processing year: $year -->\n";

// Fetch monthly salary data for the specified year, only for valid months
$sql = "SELECT month, SUM(net_salary) as total_salary, COUNT(*) as employee_count 
        FROM salary_records 
        WHERE year = ? AND month BETWEEN 1 AND 12
        GROUP BY month 
        ORDER BY month ASC";

echo "<!-- Debug: SQL Query: $sql -->\n";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo "<!-- Debug: Prepare failed: " . $conn->error . " -->\n";
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param('i', $year);
$stmt->execute();
$result = $stmt->get_result();

echo "<!-- Debug: Query executed, rows found: " . ($result ? $result->num_rows : 'null') . " -->\n";

$monthly_data = [];
$month_names = [
    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
];

// Initialize all months with 0
for ($i = 1; $i <= 12; $i++) {
    $monthly_data[$i] = [
        'month_name' => $month_names[$i],
        'total_salary' => 0,
        'employee_count' => 0
    ];
}

// Fill in actual data, only for valid months
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $month = (int)$row['month'];
        echo "<!-- Debug: Processing month: $month, salary: {$row['total_salary']} -->\n";
        if ($month >= 1 && $month <= 12) {
            $monthly_data[$month]['total_salary'] = (float)$row['total_salary'];
            $monthly_data[$month]['employee_count'] = (int)$row['employee_count'];
        }
    }
}

// Get total statistics
$total_sql = "SELECT 
    COUNT(DISTINCT employee_id) as total_employees,
    SUM(net_salary) as total_paid,
    AVG(net_salary) as avg_salary
    FROM salary_records 
    WHERE year = ?";

$total_stmt = $conn->prepare($total_sql);
$total_stmt->bind_param('i', $year);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_stats = $total_result->fetch_assoc();

// Convert to proper data types
$total_stats['total_employees'] = (int)($total_stats['total_employees'] ?? 0);
$total_stats['total_paid'] = (float)($total_stats['total_paid'] ?? 0);
$total_stats['avg_salary'] = (float)($total_stats['avg_salary'] ?? 0);

// Get available years
$years_sql = "SELECT DISTINCT year FROM salary_records ORDER BY year DESC";
$years_result = $conn->query($years_sql);
$available_years = [];
while ($row = $years_result->fetch_assoc()) {
    $available_years[] = (int)$row['year'];
}

$stmt->close();
$total_stmt->close();
$conn->close();

$response = [
    'year' => $year,
    'monthly_data' => array_values($monthly_data),
    'total_stats' => $total_stats,
    'available_years' => $available_years,
    'success' => true
];

echo "<!-- Debug: Response prepared -->\n";

header('Content-Type: application/json');
echo json_encode($response);
?> 