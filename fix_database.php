<?php
// Database fix script for salary portal
// This script fixes the month field data type issue

require 'db.php';

echo "<h2>Database Fix Script</h2>";

// Check current table structure
echo "<h3>Current Table Structure:</h3>";
$result = $conn->query("DESCRIBE salary_records");
if ($result) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
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
}

// Check current data
echo "<h3>Current Data:</h3>";
$result = $conn->query("SELECT id, employee_id, month, year, net_salary FROM salary_records ORDER BY id");
if ($result && $result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ID</th><th>Employee ID</th><th>Month</th><th>Year</th><th>Net Salary</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['employee_id']}</td>";
        echo "<td>{$row['month']}</td>";
        echo "<td>{$row['year']}</td>";
        echo "<td>{$row['net_salary']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No salary records found.</p>";
}

// Fix the month field if needed
echo "<h3>Fixing Month Field:</h3>";

// First, let's check if the month field is the wrong type
$result = $conn->query("SHOW COLUMNS FROM salary_records LIKE 'month'");
$column_info = $result->fetch_assoc();

if ($column_info && strpos($column_info['Type'], 'year') !== false) {
    echo "<p>Month field is incorrectly defined as YEAR type. Fixing...</p>";
    
    // Create a temporary table with correct structure
    $conn->query("CREATE TABLE salary_records_temp LIKE salary_records");
    
    // Modify the month column in temp table
    $conn->query("ALTER TABLE salary_records_temp MODIFY month INT(11) NOT NULL");
    
    // Copy data to temp table
    $conn->query("INSERT INTO salary_records_temp SELECT * FROM salary_records");
    
    // Drop original table
    $conn->query("DROP TABLE salary_records");
    
    // Rename temp table
    $conn->query("RENAME TABLE salary_records_temp TO salary_records");
    
    echo "<p style='color: green;'>Month field fixed successfully!</p>";
} else {
    echo "<p style='color: blue;'>Month field is already correct.</p>";
}

// Update any invalid month values (convert year values to proper month numbers)
echo "<h3>Updating Invalid Month Values:</h3>";
$result = $conn->query("SELECT id, month FROM salary_records WHERE month > 12");
if ($result && $result->num_rows > 0) {
    echo "<p>Found {$result->num_rows} records with invalid month values. Updating...</p>";
    
    while ($row = $result->fetch_assoc()) {
        $old_month = $row['month'];
        $new_month = $old_month % 12;
        if ($new_month == 0) $new_month = 12;
        
        $stmt = $conn->prepare("UPDATE salary_records SET month = ? WHERE id = ?");
        $stmt->bind_param('ii', $new_month, $row['id']);
        $stmt->execute();
        $stmt->close();
        
        echo "<p>Record ID {$row['id']}: Month {$old_month} → {$new_month}</p>";
    }
    echo "<p style='color: green;'>Month values updated successfully!</p>";
} else {
    echo "<p style='color: blue;'>All month values are valid.</p>";
}

// Verify the fix
echo "<h3>Verification:</h3>";
$result = $conn->query("SELECT id, employee_id, month, year, net_salary FROM salary_records ORDER BY id");
if ($result && $result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ID</th><th>Employee ID</th><th>Month</th><th>Year</th><th>Net Salary</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['employee_id']}</td>";
        echo "<td>{$row['month']}</td>";
        echo "<td>{$row['year']}</td>";
        echo "<td>{$row['net_salary']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Test the dashboard query
echo "<h3>Testing Dashboard Query:</h3>";
$test_year = 2025;
$sql = "SELECT month, SUM(net_salary) as total_salary, COUNT(*) as employee_count 
        FROM salary_records 
        WHERE year = ? AND month BETWEEN 1 AND 12
        GROUP BY month 
        ORDER BY month ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $test_year);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    echo "<p>Dashboard query for year {$test_year}:</p>";
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Month</th><th>Total Salary</th><th>Employee Count</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['month']}</td>";
        echo "<td>{$row['total_salary']}</td>";
        echo "<td>{$row['employee_count']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No data found for year {$test_year}</p>";
}

$stmt->close();
$conn->close();

echo "<h3>Database Fix Complete!</h3>";
echo "<p><a href='dashboard.php'>Go to Dashboard</a></p>";
?> 