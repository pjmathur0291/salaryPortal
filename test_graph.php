<?php
// Test script for graph functionality
require 'db.php';

echo "<h2>Graph Functionality Test</h2>";

// Test 1: Check if we can fetch data for different years
echo "<h3>Test 1: Available Years</h3>";
$years_sql = "SELECT DISTINCT year FROM salary_records ORDER BY year DESC";
$years_result = $conn->query($years_sql);
$available_years = [];
while ($row = $years_result->fetch_assoc()) {
    $available_years[] = $row['year'];
}
echo "<p>Available years: " . implode(', ', $available_years) . "</p>";

// Test 2: Check monthly data for each year
foreach ($available_years as $year) {
    echo "<h3>Test 2: Monthly Data for Year {$year}</h3>";
    
    $sql = "SELECT month, SUM(net_salary) as total_salary, COUNT(*) as employee_count 
            FROM salary_records 
            WHERE year = ? AND month BETWEEN 1 AND 12
            GROUP BY month 
            ORDER BY month ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $year);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>Month</th><th>Total Salary</th><th>Employee Count</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['month']}</td>";
            echo "<td>₹" . number_format($row['total_salary'], 2) . "</td>";
            echo "<td>{$row['employee_count']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No data found for year {$year}</p>";
    }
    $stmt->close();
}

// Test 3: Test the API endpoint
echo "<h3>Test 3: API Endpoint Test</h3>";
$test_year = 2025;
$api_url = "get_salary_data.php?year={$test_year}";

echo "<p>Testing API endpoint: {$api_url}</p>";

// Simulate the API call
$year = $test_year;
$sql = "SELECT month, SUM(net_salary) as total_salary, COUNT(*) as employee_count 
        FROM salary_records 
        WHERE year = ? AND month BETWEEN 1 AND 12
        GROUP BY month 
        ORDER BY month ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $year);
$stmt->execute();
$result = $stmt->get_result();

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

// Fill in actual data
while ($row = $result->fetch_assoc()) {
    $month = (int)$row['month'];
    if ($month >= 1 && $month <= 12) {
        $monthly_data[$month]['total_salary'] = (float)$row['total_salary'];
        $monthly_data[$month]['employee_count'] = (int)$row['employee_count'];
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

$api_response = [
    'year' => $year,
    'monthly_data' => array_values($monthly_data),
    'total_stats' => $total_stats,
    'success' => true
];

echo "<h4>API Response for Year {$test_year}:</h4>";
echo "<pre>" . json_encode($api_response, JSON_PRETTY_PRINT) . "</pre>";

$stmt->close();
$total_stmt->close();

// Test 4: Check for any data issues
echo "<h3>Test 4: Data Quality Check</h3>";

// Check for invalid month values
$result = $conn->query("SELECT COUNT(*) as count FROM salary_records WHERE month < 1 OR month > 12");
$invalid_months = $result->fetch_assoc()['count'];
echo "<p>Records with invalid month values: {$invalid_months}</p>";

// Check for null values
$result = $conn->query("SELECT COUNT(*) as count FROM salary_records WHERE month IS NULL OR year IS NULL");
$null_values = $result->fetch_assoc()['count'];
echo "<p>Records with null month/year values: {$null_values}</p>";

// Check for negative salaries
$result = $conn->query("SELECT COUNT(*) as count FROM salary_records WHERE net_salary < 0");
$negative_salaries = $result->fetch_assoc()['count'];
echo "<p>Records with negative salaries: {$negative_salaries}</p>";

$conn->close();

echo "<h3>Test Complete!</h3>";
echo "<p><a href='dashboard.php'>Go to Dashboard</a></p>";
echo "<p><a href='fix_database.php'>Run Database Fix</a></p>";
?> 