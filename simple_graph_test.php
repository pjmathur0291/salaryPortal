<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Graph Test</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .chart-container {
            position: relative;
            height: 400px;
            margin: 20px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
        }
        .debug-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            font-family: monospace;
            font-size: 12px;
        }
        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Salary Graph Test</h1>
        
        <div class="debug-info">
            <h3>Debug Information:</h3>
            <div id="debugInfo">Loading...</div>
        </div>

        <div>
            <button onclick="testChart()">Test Basic Chart</button>
            <button onclick="testDataFetch()">Test Data Fetch</button>
            <button onclick="testAPI()">Test API Call</button>
        </div>

        <div class="chart-container">
            <canvas id="testChart"></canvas>
        </div>

        <div id="results"></div>
    </div>

    <script>
        let chartInstance = null; // Only declare once, no conflict

        // Test 1: Basic Chart.js functionality
        function testChart() {
            console.log('Testing basic chart...');
            const ctx = document.getElementById('testChart').getContext('2d');
            if (chartInstance) {
                chartInstance.destroy();
            }
            chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['January', 'February', 'March', 'April', 'May', 'June'],
                    datasets: [{
                        label: 'Test Data',
                        data: [12, 19, 3, 5, 2, 3],
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            document.getElementById('debugInfo').innerHTML += '<br>✅ Basic chart created successfully';
        }

        // Test 2: Data fetch functionality
        function testDataFetch() {
            console.log('Testing data fetch...');
            fetch('get_salary_data_test.php?year=2025')
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Fetched data:', data);
                    document.getElementById('debugInfo').innerHTML += '<br>✅ Data fetch successful';
                    document.getElementById('debugInfo').innerHTML += '<br>Data: ' + JSON.stringify(data, null, 2);
                    // Test creating chart with real data
                    if (data.monthly_data && data.monthly_data.length > 0) {
                        createChartWithData(data);
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    document.getElementById('debugInfo').innerHTML += '<br>❌ Data fetch failed: ' + error.message;
                });
        }

        // Test 3: API call test
        function testAPI() {
            console.log('Testing API...');
            // Test if the API endpoint exists
            fetch('get_salary_data_test.php')
                .then(response => {
                    document.getElementById('debugInfo').innerHTML += '<br>✅ API endpoint accessible';
                    return response.text();
                })
                .then(text => {
                    document.getElementById('debugInfo').innerHTML += '<br>API Response: ' + text.substring(0, 200) + '...';
                })
                .catch(error => {
                    document.getElementById('debugInfo').innerHTML += '<br>❌ API test failed: ' + error.message;
                });
        }

        // Create chart with real data
        function createChartWithData(data) {
            const ctx = document.getElementById('testChart').getContext('2d');
            if (chartInstance) {
                chartInstance.destroy();
            }
            const labels = data.monthly_data.map(item => item.month_name);
            const values = data.monthly_data.map(item => item.total_salary);
            chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Monthly Salary (₹)',
                        data: values,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
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
            document.getElementById('debugInfo').innerHTML += '<br>✅ Chart created with real data';
        }

        // Initialize debug info
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('debugInfo').innerHTML = 'Page loaded successfully<br>';
            document.getElementById('debugInfo').innerHTML += 'Chart.js version: ' + (Chart ? 'Loaded' : 'Not loaded') + '<br>';
            document.getElementById('debugInfo').innerHTML += 'Canvas element: ' + (document.getElementById('testChart') ? 'Found' : 'Not found');
        });
    </script>
</body>
</html> 