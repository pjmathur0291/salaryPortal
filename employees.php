<?php
require 'db.php';

// Handle new employee form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = trim($_POST['name']);
    $department = trim($_POST['department']);
    $email = trim($_POST['email']);
    $dob = $_POST['dob'] ?? null;
    $company_email = trim($_POST['company_email']);
    $date_of_joining = $_POST['date_of_joining'] ?? null;
    $documents_submitted = trim($_POST['documents_submitted']);
    $manager = trim($_POST['manager']);
    $assets_given = trim($_POST['assets_given']);
    $basic_salary = floatval($_POST['basic_salary']);
    $phone = trim($_POST['phone']);
    $emergency_phone = trim($_POST['emergency_phone']);
    $father_name = trim($_POST['father_name']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $qualification = trim($_POST['qualification']);
    $previous_employers = trim($_POST['previous_employers']);
    $bank_name = trim($_POST['bank_name']);
    $branch_name = trim($_POST['branch_name']);
    $account_number = trim($_POST['account_number']);
    $ifsc_code = trim($_POST['ifsc_code']);

    if ($name !== '') {
        $stmt = $conn->prepare("INSERT INTO employees (name, department, email, dob, company_email, date_of_joining, documents_submitted, manager, assets_given, basic_salary, phone, emergency_phone, father_name, address, city, state, qualification, previous_employers, bank_name, branch_name, account_number, ifsc_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            'sssssssssdssssssssssss',
            $name,
            $department,
            $email,
            $dob,
            $company_email,
            $date_of_joining,
            $documents_submitted,
            $manager,
            $assets_given,
            $basic_salary,
            $phone,
            $emergency_phone,
            $father_name,
            $address,
            $city,
            $state,
            $qualification,
            $previous_employers,
            $bank_name,
            $branch_name,
            $account_number,
            $ifsc_code
        );
        $stmt->execute();
        $stmt->close();
        header("Location: employees.php");
        exit;
    }
}

// Handle archive (with password check)
$archive_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['archive_id'])) {
    session_start();
    $input_password = $_POST['archive_password'] ?? '';
    $emp_id = intval($_POST['archive_id']);
    // Use the session password for validation (replace with your actual session logic)
    $admin_password = $_SESSION['password'] ?? 'password123'; // fallback for demo
    if ($input_password === $admin_password) {
        $stmt = $conn->prepare("UPDATE employees SET status = 'inactive' WHERE id = ?");
        $stmt->bind_param('i', $emp_id);
        $stmt->execute();
        $stmt->close();
        header("Location: employees.php?archived=1");
        exit;
    } else {
        $archive_error = 'Incorrect password. Employee was not archived.';
    }
}

// Fetch only active employees, with search and department filter
$employees = [];
$where = ["status = 'active'"];
$params = [];
$types = '';
if (!empty($_GET['search'])) {
    $where[] = 'name LIKE ?';
    $params[] = '%' . $_GET['search'] . '%';
    $types .= 's';
}
if (!empty($_GET['department'])) {
    $where[] = 'department = ?';
    $params[] = $_GET['department'];
    $types .= 's';
}
$sql = "SELECT * FROM employees";
if ($where) {
    $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " ORDER BY id DESC";
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
    .table-responsive {
      overflow-x: auto;
    }
    th.sticky-col, td.sticky-col {
      position: sticky;
      left: 0;
      background: #fff;
      z-index: 2;
    }
    th.sticky-col-2, td.sticky-col-2 {
      position: sticky;
      left: 50px; /* Adjust if S.No column width changes */
      background: #fff;
      z-index: 2;
    }
    th.sticky-header {
      position: sticky;
      top: 0;
      z-index: 3;
      background: #f8f9fa;
    }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
<div class="container">
    <?php if (isset($_GET['archived'])): ?>
        <div style="color:green; margin-bottom:10px;">Employee archived successfully.</div>
    <?php endif; ?>
    <?php if (!empty($archive_error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($archive_error) ?></div>
    <?php endif; ?>
    <h1>Employees</h1>
    <form method="POST" class="employee-form">
        <div class="form-group">
            <label for="name">Employee Name</label>
            <input type="text" name="name" id="name" required>
        </div>
        <div class="form-group">
            <label for="department">Department</label>
            <select name="department" id="department" required>
                <option value="">Select Department</option>
                <option value="Social Media">Social Media</option>
                <option value="Developer">Developer</option>
                <option value="SEO">SEO</option>
                <option value="Google Ads">Google Ads</option>
                <option value="Meta Ads">Meta Ads</option>
                <option value="Counselor">Counselor</option>
                <option value="HR">HR</option>
                <option value="Sales">Sales</option>
                <option value="Video Editing">Video Editing</option>
                <option value="Graphic Designing">Graphic Designing</option>
                <option value="CEO">CEO</option>
                <option value="Accountant">Accountant</option>
            </select>
        </div>
        <div class="form-group">
            <label for="email">Personal Email</label>
            <input type="email" name="email" id="email">
        </div>
        <div class="form-group">
            <label for="dob">Date of Birth</label>
            <input type="date" name="dob" id="dob">
        </div>
        <div class="form-group">
            <label for="company_email">Company Email</label>
            <input type="email" name="company_email" id="company_email">
        </div>
        <div class="form-group">
            <label for="date_of_joining">Date of Joining</label>
            <input type="date" name="date_of_joining" id="date_of_joining">
        </div>
        <div class="form-group">
            <label for="documents_submitted">Documents Submitted</label>
            <input type="text" name="documents_submitted" id="documents_submitted" placeholder="e.g. Aadhar, PAN, Resume">
        </div>
        <div class="form-group">
            <label for="manager">Manager</label>
            <input type="text" name="manager" id="manager">
        </div>
        <div class="form-group">
            <label for="basic_salary">Basic Salary</label>
            <input type="number" name="basic_salary" id="basic_salary" step="0.01" min="0">
        </div>
        <div class="form-group">
            <label for="assets_given">Assets Given</label>
            <input type="text" name="assets_given" id="assets_given" placeholder="e.g. Laptop, Mouse">
        </div>
        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="text" name="phone" id="phone">
        </div>
        <div class="form-group">
            <label for="emergency_phone">Emergency Phone Number</label>
            <input type="text" name="emergency_phone" id="emergency_phone">
        </div>
        <div class="form-group">
            <label for="father_name">Father's Name</label>
            <input type="text" name="father_name" id="father_name">
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" name="address" id="address">
        </div>
        <div class="form-group">
            <label for="city">City</label>
            <input type="text" name="city" id="city">
        </div>
        <div class="form-group">
            <label for="state">State</label>
            <input type="text" name="state" id="state">
        </div>
        <div class="form-group">
            <label for="qualification">Highest Qualification</label>
            <input type="text" name="qualification" id="qualification">
        </div>
        <div class="form-group">
            <label for="previous_employers">Previous Employer(s)</label>
            <input type="text" name="previous_employers" id="previous_employers">
        </div>
        <div class="form-group">
            <label for="bank_name">Bank Name</label>
            <input type="text" name="bank_name" id="bank_name">
        </div>
        <div class="form-group">
            <label for="branch_name">Branch Name</label>
            <input type="text" name="branch_name" id="branch_name">
        </div>
        <div class="form-group">
            <label for="account_number">Account Number</label>
            <input type="text" name="account_number" id="account_number">
        </div>
        <div class="form-group">
            <label for="ifsc_code">IFSC Code</label>
            <input type="text" name="ifsc_code" id="ifsc_code">
        </div>
        <button type="submit">Add Employee</button>
    </form>
    <h2>Employee List</h2>
    <a href="archived_employees.php" style="margin-bottom: 10px; display: inline-block;">View Archived Employees</a>
    <!-- Employee search/filter and list removed. Use employee_list.php for listing. -->
</div>
<!-- Archive Modal -->
<div class="modal fade" id="archiveModal" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" id="archiveForm">
        <div class="modal-header">
          <h5 class="modal-title" id="archiveModalLabel">Archive Employee</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="archive_id" id="archive_id">
          <div class="mb-3">
            <label for="archive_emp_name" class="form-label">Employee</label>
            <input type="text" class="form-control" id="archive_emp_name" disabled>
          </div>
          <div class="mb-3">
            <label for="archive_password" class="form-label">Enter Your Password</label>
            <input type="password" class="form-control" name="archive_password" id="archive_password" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Archive</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Fill modal with employee info
var archiveModal = document.getElementById('archiveModal');
archiveModal.addEventListener('show.bs.modal', function (event) {
  var button = event.relatedTarget;
  var empId = button.getAttribute('data-emp-id');
  var empName = button.getAttribute('data-emp-name');
  document.getElementById('archive_id').value = empId;
  document.getElementById('archive_emp_name').value = empName;
  document.getElementById('archive_password').value = '';
});
</script>
</body>
</html>