<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
require 'db.php';

// Fetch monthly salary data for the current year, only for valid months and active employees
$current_year = date('Y');
$sql = "SELECT sr.month, SUM(sr.net_salary) as total_salary, COUNT(*) as employee_count 
        FROM salary_records sr
        JOIN employees e ON sr.employee_id = e.id
        WHERE sr.year = ? AND sr.month BETWEEN 1 AND 12 AND e.status = 'active'
        GROUP BY sr.month 
        ORDER BY sr.month ASC";
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

// Get total statistics for active employees only
$total_sql = "SELECT 
    COUNT(DISTINCT e.id) as total_employees,
    SUM(sr.net_salary) as total_paid,
    AVG(sr.net_salary) as avg_salary
    FROM salary_records sr
    JOIN employees e ON sr.employee_id = e.id
    WHERE sr.year = ? AND e.status = 'active'";
$total_stmt = $conn->prepare($total_sql);
$total_stmt->bind_param('i', $current_year);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_stats = $total_result->fetch_assoc();

// Get available years for dropdown (only for active employees)
$years_sql = "SELECT DISTINCT sr.year FROM salary_records sr JOIN employees e ON sr.employee_id = e.id WHERE e.status = 'active' ORDER BY sr.year DESC";
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
        .chart-controls {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            align-items: center;
            flex-wrap: wrap;
        }
        .chart-type-btn {
            padding: 8px 16px;
            border: 2px solid #007bff;
            background: white;
            color: #007bff;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        .chart-type-btn.active {
            background: #007bff;
            color: white;
        }
        .chart-type-btn:hover {
            background: #007bff;
            color: white;
        }
        .loading {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            font-style: italic;
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
            <div class="chart-controls">
                <button class="chart-type-btn active" data-type="bar">Bar Chart</button>
                <button class="chart-type-btn" data-type="line">Line Chart</button>
                <button class="chart-type-btn" data-type="doughnut">Doughnut Chart</button>
                <button class="chart-type-btn" data-type="pie">Pie Chart</button>
            </div>
            <div id="chartLoading" class="loading" style="display: none;">
                Loading chart data...
            </div>
            <div id="chartNoData" class="no-data" style="display: none;">
                No salary data available for the selected year.
            </div>
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

    <!-- Comparison Section -->
    <div class="card mt-4 mb-4 p-3">
      <h4>Compare Two Months</h4>
      <div class="row g-2 align-items-end">
        <div class="col-md-3">
          <label>Month 1</label>
          <select id="compareMonth1" class="form-select"></select>
        </div>
        <div class="col-md-3">
          <label>Year 1</label>
          <select id="compareYear1" class="form-select"></select>
        </div>
        <div class="col-md-3">
          <label>Month 2</label>
          <select id="compareMonth2" class="form-select"></select>
        </div>
        <div class="col-md-3">
          <label>Year 2</label>
          <select id="compareYear2" class="form-select"></select>
        </div>
        <div class="col-md-12 mt-2">
          <button id="compareBtn" class="btn btn-primary">Compare</button>
        </div>
      </div>
    </div>
    <div id="compareResult"></div>

    <script>
        let chartInstance = null;
        let currentChartType = 'bar';
        const monthNames = <?php echo json_encode(array_values($month_names)); ?>;

        // Chart colors
        const chartColors = {
            bar: {
                backgroundColor: 'rgba(52, 152, 219, 0.8)',
                borderColor: 'rgba(52, 152, 219, 1)'
            },
            line: {
                backgroundColor: 'rgba(46, 204, 113, 0.2)',
                borderColor: 'rgba(46, 204, 113, 1)',
                pointBackgroundColor: 'rgba(46, 204, 113, 1)'
            },
            doughnut: [
                '#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6',
                '#1abc9c', '#34495e', '#e67e22', '#95a5a6', '#16a085',
                '#c0392b', '#8e44ad'
            ],
            pie: [
                '#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6',
                '#1abc9c', '#34495e', '#e67e22', '#95a5a6', '#16a085',
                '#c0392b', '#8e44ad'
            ]
        };

        // Create or update the chart
        function createChartWithData(data, chartType = 'bar') {
            const ctx = document.getElementById('salaryChart').getContext('2d');
            if (chartInstance) {
                chartInstance.destroy();
            }
            const labels = data.monthly_data.map(item => item.month_name);
            const values = data.monthly_data.map(item => item.total_salary);
            const chartConfig = {
                type: chartType,
                data: {
                    labels: labels,
                    datasets: []
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
                                    if (chartType === 'bar' || chartType === 'line') {
                                        return 'Total Salary: ₹' + context.parsed.y.toLocaleString('en-IN');
                                    } else {
                                        return context.label + ': ₹' + context.parsed.toLocaleString('en-IN');
                                    }
                                }
                            }
                        }
                    }
                }
            };
            if (chartType === 'bar') {
                chartConfig.data.datasets = [{
                    label: 'Total Salary (₹)',
                    data: values,
                    backgroundColor: chartColors.bar.backgroundColor,
                    borderColor: chartColors.bar.borderColor,
                    borderWidth: 2,
                    borderRadius: 5,
                    borderSkipped: false,
                }];
                chartConfig.options.scales = {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₹' + value.toLocaleString('en-IN');
                            }
                        }
                    }
                };
            } else if (chartType === 'line') {
                chartConfig.data.datasets = [{
                    label: 'Total Salary (₹)',
                    data: values,
                    backgroundColor: chartColors.line.backgroundColor,
                    borderColor: chartColors.line.borderColor,
                    borderWidth: 3,
                    pointBackgroundColor: chartColors.line.pointBackgroundColor,
                    pointBorderColor: chartColors.line.borderColor,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    fill: true,
                    tension: 0.4
                }];
                chartConfig.options.scales = {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₹' + value.toLocaleString('en-IN');
                            }
                        }
                    }
                };
            } else if (chartType === 'doughnut' || chartType === 'pie') {
                // Filter out months with zero salary for pie/doughnut charts
                const filteredData = [];
                const filteredLabels = [];
                values.forEach((value, index) => {
                    if (value > 0) {
                        filteredData.push(value);
                        filteredLabels.push(labels[index]);
                    }
                });
                chartConfig.data.labels = filteredLabels;
                chartConfig.data.datasets = [{
                    label: 'Total Salary (₹)',
                    data: filteredData,
                    backgroundColor: chartColors[chartType].slice(0, filteredData.length),
                    borderWidth: 2,
                    borderColor: '#fff'
                }];
            }
            chartInstance = new Chart(ctx, chartConfig);
        }

        // Update dashboard data
        function updateDashboard(year) {
            const loadingDiv = document.getElementById('chartLoading');
            const noDataDiv = document.getElementById('chartNoData');
            const canvas = document.getElementById('salaryChart');
            loadingDiv.style.display = 'block';
            noDataDiv.style.display = 'none';
            canvas.style.display = 'none';
            fetch(`get_salary_data.php?year=${year}`)
                .then(response => response.json())
                .then(data => {
                    loadingDiv.style.display = 'none';
                    canvas.style.display = 'block';
                    // Check if there's any data
                    const hasData = data.monthly_data.some(item => item.total_salary > 0);
                    if (!hasData) {
                        noDataDiv.style.display = 'block';
                        canvas.style.display = 'none';
                        return;
                    }
                    // Update statistics
                    document.getElementById('totalEmployees').textContent = data.total_stats.total_employees || 0;
                    document.getElementById('totalPaid').textContent = '₹' + (data.total_stats.total_paid || 0).toLocaleString('en-IN', {minimumFractionDigits: 2});
                    document.getElementById('avgSalary').textContent = '₹' + (data.total_stats.avg_salary || 0).toLocaleString('en-IN', {minimumFractionDigits: 2});
                    // Update chart
                    createChartWithData(data, currentChartType);
                    // Update table
                    updateTable(data.monthly_data);
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    loadingDiv.style.display = 'none';
                    noDataDiv.style.display = 'block';
                    canvas.style.display = 'none';
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
        createChartWithData({monthly_data: <?php echo json_encode(array_values($monthly_data)); ?>}, currentChartType);

        // Add event listener for year selection
        document.getElementById('yearSelect').addEventListener('change', function() {
            updateDashboard(this.value);
        });

        // Add event listeners for chart type buttons
        document.querySelectorAll('.chart-type-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                document.querySelectorAll('.chart-type-btn').forEach(b => b.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');
                // Update chart type
                currentChartType = this.dataset.type;
                // Get current year and update chart
                const currentYear = document.getElementById('yearSelect').value;
                updateDashboard(currentYear);
            });
        });

        // Comparison feature
        const compareMonthNames = [
          "January", "February", "March", "April", "May", "June",
          "July", "August", "September", "October", "November", "December"
        ];
        const compareAvailableYears = <?php echo json_encode($available_years); ?>;
        function fillCompareDropdowns() {
          const m1 = document.getElementById('compareMonth1');
          const m2 = document.getElementById('compareMonth2');
          const y1 = document.getElementById('compareYear1');
          const y2 = document.getElementById('compareYear2');
          m1.innerHTML = m2.innerHTML = '';
          y1.innerHTML = y2.innerHTML = '';
          compareMonthNames.forEach((m, i) => {
            m1.innerHTML += `<option value="${i+1}">${m}</option>`;
            m2.innerHTML += `<option value="${i+1}">${m}</option>`;
          });
          compareAvailableYears.forEach(y => {
            y1.innerHTML += `<option value="${y}">${y}</option>`;
            y2.innerHTML += `<option value="${y}">${y}</option>`;
          });
          // Default to current month/year and previous month/year
          const now = new Date();
          m1.value = now.getMonth() + 1;
          y1.value = now.getFullYear();
          m2.value = now.getMonth() === 0 ? 12 : now.getMonth();
          y2.value = now.getMonth() === 0 ? now.getFullYear() - 1 : now.getFullYear();
        }
        fillCompareDropdowns();
        document.getElementById('compareBtn').onclick = function() {
          const m1 = document.getElementById('compareMonth1').value;
          const y1 = document.getElementById('compareYear1').value;
          const m2 = document.getElementById('compareMonth2').value;
          const y2 = document.getElementById('compareYear2').value;
          Promise.all([
            fetch(`get_salary_data.php?year=${y1}&month=${m1}`).then(r=>r.json()),
            fetch(`get_salary_data.php?year=${y2}&month=${m2}`).then(r=>r.json())
          ]).then(([data1, data2]) => {
            const d1 = data1.single_month || data1.monthly_data[0];
            const d2 = data2.single_month || data2.monthly_data[0];
            document.getElementById('compareResult').innerHTML = `
              <div class="row mt-3 mb-4">
                <div class="col-md-6">
                  <div class="card p-3">
                    <h5>${compareMonthNames[d1.month-1]} ${y1}</h5>
                    <div>Total Salary: <b>₹${(d1.total_salary||0).toLocaleString('en-IN', {minimumFractionDigits:2})}</b></div>
                    <div>Employee Count: <b>${d1.employee_count||0}</b></div>
                    <div>Average: <b>₹${(d1.avg_salary||0).toLocaleString('en-IN', {minimumFractionDigits:2})}</b></div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="card p-3">
                    <h5>${compareMonthNames[d2.month-1]} ${y2}</h5>
                    <div>Total Salary: <b>₹${(d2.total_salary||0).toLocaleString('en-IN', {minimumFractionDigits:2})}</b></div>
                    <div>Employee Count: <b>${d2.employee_count||0}</b></div>
                    <div>Average: <b>₹${(d2.avg_salary||0).toLocaleString('en-IN', {minimumFractionDigits:2})}</b></div>
                  </div>
                </div>
              </div>
            `;
          });
        };
    </script>
    
</body>
</html> 