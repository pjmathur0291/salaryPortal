// Real-time net salary calculation
const basicInput = document.getElementById('basic_salary');
const allowancesInput = document.getElementById('allowances');
const deductionsInput = document.getElementById('deductions');
const netSalaryInput = document.getElementById('net_salary');

function calculateNetSalary() {
    const basic = parseFloat(basicInput.value) || 0;
    const allowances = parseFloat(allowancesInput.value) || 0;
    const deductions = parseFloat(deductionsInput.value) || 0;
    const net = basic + allowances - deductions;
    netSalaryInput.value = net >= 0 ? net.toFixed(2) : 0;
}

[basicInput, allowancesInput, deductionsInput].forEach(input => {
    input.addEventListener('input', calculateNetSalary);
});

document.addEventListener('DOMContentLoaded', () => {
    calculateNetSalary();
    loadSalaryRecords();
});

// Handle form submission
const salaryForm = document.getElementById('salaryForm');
salaryForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(salaryForm);
    fetch('save_salary.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        console.log('Save response:', data);
        if (data.success) {
            salaryForm.reset();
            calculateNetSalary();
            loadSalaryRecords();
            alert('Salary saved successfully!');
        } else {
            alert('Error: ' + (data.error || 'Could not save salary.'));
        }
    })
    .catch((err) => {
        console.error('AJAX error:', err);
        alert('Error: Could not connect to server.');
    });
});

// Load salary records
function loadSalaryRecords() {
    fetch('fetch_salaries.php')
        .then(res => res.json())
        .then(data => {
            console.log('Fetched data:', data);
            const tbody = document.querySelector('#salaryTable tbody');
            tbody.innerHTML = '';
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${row.id}</td>
                        <td>${row.employee_name}</td>
                        <td>${row.basic_salary}</td>
                        <td>${row.allowances}</td>
                        <td>${row.deductions}</td>
                        <td>${row.net_salary}</td>
                        <td>${row.created_at}</td>
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                const tr = document.createElement('tr');
                tr.innerHTML = '<td colspan="7">No records found.</td>';
                tbody.appendChild(tr);
            }
        })
        .catch((err) => {
            console.error('Fetch error:', err);
            const tbody = document.querySelector('#salaryTable tbody');
            tbody.innerHTML = '<tr><td colspan="7">Error loading records.</td></tr>';
        });
} 