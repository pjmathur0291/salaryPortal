<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Check</h1>";

// Test database connection
echo "<h2>1. Database Connection Test</h2>";
try {
    require 'db.php';
    echo "✅ Database connection successful<br>";
    echo "Server info: " . $conn->server_info . "<br>";
    echo "Client info: " . $conn->client_info . "<br>";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    exit;
}

// Check if tables exist
echo "<h2>2. Table Structure Check</h2>";
$tables = ['salary_records', 'employees'];
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result && $result->num_rows > 0) {
        echo "✅ Table '$table' exists<br>";
        
        // Show table structure
        $structure = $conn->query("DESCRIBE $table");
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $structure->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "<td>{$row['Default']}</td>";
            echo "<td>{$row['Extra']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ Table '$table' does not exist<br>";
    }
}

// Check salary_records data
echo "<h2>3. Salary Records Data Check</h2>";
$result = $conn->query("SELECT COUNT(*) as count FROM salary_records");
$count = $result->fetch_assoc()['count'];
echo "Total salary records: $count<br>";

if ($count > 0) {
    echo "<h3>Sample Data:</h3>";
    $result = $conn->query("SELECT * FROM salary_records ORDER BY id DESC LIMIT 5");
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ID</th><th>Employee ID</th><th>Month</th><th>Year</th><th>Basic Salary</th><th>Net Salary</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['employee_id']}</td>";
        echo "<td>{$row['month']}</td>";
        echo "<td>{$row['year']}</td>";
        echo "<td>{$row['basic_salary']}</td>";
        echo "<td>{$row['net_salary']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Check for data issues
echo "<h2>4. Data Quality Check</h2>";

// Check for invalid month values
$result = $conn->query("SELECT COUNT(*) as count FROM salary_records WHERE month < 1 OR month > 12");
$invalid_months = $result->fetch_assoc()['count'];
echo "Records with invalid month values: $invalid_months<br>";

if ($invalid_months > 0) {
    echo "<h3>Invalid Month Records:</h3>";
    $result = $conn->query("SELECT id, month, year FROM salary_records WHERE month < 1 OR month > 12");
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ID</th><th>Month</th><th>Year</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['month']}</td>";
        echo "<td>{$row['year']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Check for null values
$result = $conn->query("SELECT COUNT(*) as count FROM salary_records WHERE month IS NULL OR year IS NULL");
$null_values = $result->fetch_assoc()['count'];
echo "Records with null month/year values: $null_values<br>";

// Check available years
echo "<h2>5. Available Years</h2>";
$result = $conn->query("SELECT DISTINCT year FROM salary_records ORDER BY year DESC");
$years = [];
while ($row = $result->fetch_assoc()) {
    $years[] = $row['year'];
}
echo "Available years: " . implode(', ', $years) . "<br>";

// Test the dashboard query
echo "<h2>6. Dashboard Query Test</h2>";
foreach ($years as $year) {
    echo "<h3>Year: $year</h3>";
    
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
        echo "No data found for year $year<br>";
    }
    $stmt->close();
}

$conn->close();

echo "<h2>Check Complete!</h2>";
echo "<p><a href='simple_graph_test.php'>Go to Simple Graph Test</a></p>";
echo "<p><a href='fix_database.php'>Run Database Fix</a></p>";
echo "<p><a href='dashboard.php'>Go to Dashboard</a></p>";
?> 