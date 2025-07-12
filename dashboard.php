<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
require 'db.php';

// Fetch monthly salary data for the current year, only for valid months
$current_year = date('Y');
$sql = "SELECT month, SUM(net_salary) as total_salary, COUNT(*) as employee_count 
        FROM salary_records 
        WHERE year = ? AND month BETWEEN 1 AND 12
        GROUP BY month 
        ORDER BY month ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $current_year);
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

// Fill in actual data, only for valid months
while ($row = $result->fetch_assoc()) {
    $month = (int)$row['month'];
    if ($month >= 1 && $month <= 12) {
        $monthly_data[$month]['total_salary'] = $row['total_salary'];
        $monthly_data[$month]['employee_count'] = $row['employee_count'];
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
$total_stmt->bind_param('i', $current_year);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_stats = $total_result->fetch_assoc();

// Get available years for dropdown BEFORE closing connection
$years_sql = "SELECT DISTINCT year FROM salary_records ORDER BY year DESC";
$years_result = $conn->query($years_sql);
$available_years = [];
while ($row = $years_result->fetch_assoc()) {
    $available_years[] = $row['year'];
}

$stmt->close();
$total_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            text-align: center;
            border: 1px solid #dee2e6;
            transition: transform 0.2s ease-in-out;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }
        .stat-card h3 {
            margin: 0 0 10px 0;
            color: #6c757d;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        .stat-card .value {
            font-size: 2em;
            font-weight: bold;
            color: #2c3e50;
        }
        .chart-container {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            border: 1px solid #dee2e6;
        }
        .chart-container canvas {
            height: 400px !important;
        }
        .chart-container h2 {
            margin-top: 0;
            color: #2c3e50;
            font-weight: 600;
        }
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container-fluid px-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 mb-0">Salary Dashboard</h1>
            <div class="d-flex align-items-center gap-3">
                <label for="yearSelect" class="form-label fw-bold mb-0">Year:</label>
                <select id="yearSelect" class="form-select" style="width: auto;">
                    <?php
                    foreach ($available_years as $year) {
                        $selected = ($year == $current_year) ? 'selected' : '';
                        echo "<option value='{$year}' {$selected}>{$year}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Employees</h3>
                <div class="value" id="totalEmployees"><?php echo number_format($total_stats['total_employees']); ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Paid This Year</h3>
                <div class="value" id="totalPaid">₹<?php echo number_format($total_stats['total_paid'], 2); ?></div>
            </div>
            <div class="stat-card">
                <h3>Average Salary</h3>
                <div class="value" id="avgSalary">₹<?php echo number_format($total_stats['avg_salary'], 2); ?></div>
            </div>
        </div>

        <!-- Salary Chart -->
        <div class="chart-container">
            <h2>Monthly Salary Distribution</h2>
            <canvas id="salaryChart" width="400" height="200"></canvas>
        </div>

        <!-- Monthly Breakdown Table -->
        <div class="chart-container">
            <h2>Monthly Breakdown</h2>
            <div class="table-responsive">
                <table id="monthlyTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th class="text-end">Total Salary</th>
                            <th class="text-center">Employees</th>
                            <th class="text-end">Average</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($monthly_data as $month => $data): ?>
                        <tr>
                            <td><?php echo $data['month_name']; ?></td>
                            <td class="text-end">
                                ₹<?php echo number_format($data['total_salary'], 2); ?>
                            </td>
                            <td class="text-center">
                                <?php echo $data['employee_count']; ?>
                            </td>
                            <td class="text-end">
                                <?php 
                                $avg = $data['employee_count'] > 0 ? $data['total_salary'] / $data['employee_count'] : 0;
                                echo '₹' . number_format($avg, 2);
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        let salaryChart;
        const monthNames = <?php echo json_encode(array_values($month_names)); ?>;

        // Initialize chart
        function initChart(data) {
            const ctx = document.getElementById('salaryChart').getContext('2d');
            salaryChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: monthNames,
                    datasets: [{
                        label: 'Total Salary (₹)',
                        data: data,
                        backgroundColor: 'rgba(52, 152, 219, 0.8)',
                        borderColor: 'rgba(52, 152, 219, 1)',
                        borderWidth: 2,
                        borderRadius: 5,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Total Salary: ₹' + context.parsed.y.toLocaleString('en-IN');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₹' + value.toLocaleString('en-IN');
                                }
                            }
                        }
                    }
                }
            });
        }

        // Update dashboard data
        function updateDashboard(year) {
            fetch(`get_salary_data.php?year=${year}`)
                .then(response => response.json())
                .then(data => {
                    // Update statistics
                    document.getElementById('totalEmployees').textContent = data.total_stats.total_employees || 0;
                    document.getElementById('totalPaid').textContent = '₹' + (data.total_stats.total_paid || 0).toLocaleString('en-IN', {minimumFractionDigits: 2});
                    document.getElementById('avgSalary').textContent = '₹' + (data.total_stats.avg_salary || 0).toLocaleString('en-IN', {minimumFractionDigits: 2});

                    // Update chart
                    const salaryData = data.monthly_data.map(item => item.total_salary);
                    if (salaryChart) {
                        salaryChart.destroy();
                    }
                    initChart(salaryData);

                    // Update table
                    updateTable(data.monthly_data);
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                });
        }

        // Update table data
        function updateTable(monthlyData) {
            const tbody = document.querySelector('#monthlyTable tbody');
            tbody.innerHTML = '';
            
            monthlyData.forEach(data => {
                const avg = data.employee_count > 0 ? data.total_salary / data.employee_count : 0;
                const row = `
                    <tr>
                        <td>${data.month_name}</td>
                        <td class="text-end">
                            ₹${data.total_salary.toLocaleString('en-IN', {minimumFractionDigits: 2})}
                        </td>
                        <td class="text-center">
                            ${data.employee_count}
                        </td>
                        <td class="text-end">
                            ₹${avg.toLocaleString('en-IN', {minimumFractionDigits: 2})}
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }

        // Initialize with current year data
        initChart(<?php echo json_encode(array_column($monthly_data, 'total_salary')); ?>);

        // Add event listener for year selection
        document.getElementById('yearSelect').addEventListener('change', function() {
            updateDashboard(this.value);
        });
    </script>
    
</body>
</html> 