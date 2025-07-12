<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database credentials
$host = 'localhost';
$user = 'u111016890_salaryUser';
$pass = 'fZ^3UH||';
$dbname = 'u111016890_salary';

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Set character set
$conn->set_charset('utf8mb4');

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Database connection failed.']));
}
?>