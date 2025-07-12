<?php
require 'db.php';

// Fetch only active employees for dropdown
$employees = [];
$result = $conn->query("SELECT * FROM employees WHERE status = 'active' ORDER BY name ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
}

// Handle filter
$where = [];
$params = [];
$types = '';

if (!empty($_GET['employee_id'])) {
    $where[] = 'employee_id = ?';
    $params[] = intval($_GET['employee_id']);
    $types .= 'i';
}
if (!empty($_GET['month'])) {
    $where[] = 'month = ?';
    $params[] = intval($_GET['month']);
    $types .= 'i';
}
if (!empty($_GET['year'])) {
    $where[] = 'year = ?';
    $params[] = intval($_GET['year']);
    $types .= 'i';
}

$sql = "SELECT sr.*, e.name AS employee_name, e.department FROM salary_records sr JOIN employees e ON sr.employee_id = e.id AND e.status = 'active'";
if ($where) {
    $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " ORDER BY year DESC, month DESC, employee_name ASC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$records = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Salary Report</title>
    <link rel="stylesheet" href="style.css">
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php if (file_exists("header.php")) include "header.php"; ?>
<div class="container mt-4">
    <h1 class="mb-4">Salary Report</h1>
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-5">
            <label for="employee_id" class="form-label">Employee</label>
            <select name="employee_id" id="employee_id" class="form-select">
                <option value="">All Employees</option>
                <?php foreach ($employees as $emp): ?>
                    <option value="<?= htmlspecialchars($emp['id']) ?>" <?= (isset($_GET['employee_id']) && $_GET['employee_id'] == $emp['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($emp['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-5">
            <label for="month" class="form-label">Month</label>
            <select name="month" id="month" class="form-select">
                <option value="">All Months</option>
                <?php
                for ($m = 1; $m <= 12; $m++) {
                    $monthName = date('F', mktime(0, 0, 0, $m, 10));
                    $selected = (isset($_GET['month']) && $_GET['month'] == $m) ? 'selected' : '';
                    echo "<option value=\"$m\" $selected>$monthName</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-4">
            <label for="year" class="form-label">Year</label>
            <input type="number" name="year" id="year" class="form-control" value="<?= isset($_GET['year']) ? htmlspecialchars($_GET['year']) : date('Y') ?>">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary" style="width: 100px !important;">Filter</button>
        </div>
    </form>
    <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>Employee</th>
                <th>Department</th>
                <th>Month</th>
                <th>Year</th>
                <th>Basic</th>
                <th>Allowances</th>
                <th>Deductions</th>
                <th>Leaves</th>
                <th>Half Days</th>
                <th>Early Leaves</th>
                <th>Net Salary</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($records && $records->num_rows > 0): ?>
            <?php while ($row = $records->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['employee_name']) ?></td>
                    <td><?= htmlspecialchars($row['department']) ?></td>
                    <td><?= date('F', mktime(0, 0, 0, $row['month'], 10)) ?></td>
                    <td><?= htmlspecialchars($row['year']) ?></td>
                    <td><?= htmlspecialchars($row['basic_salary']) ?></td>
                    <td><?= htmlspecialchars($row['allowances']) ?></td>
                    <td><?= htmlspecialchars($row['deductions']) ?></td>
                    <td><?= htmlspecialchars($row['leaves']) ?></td>
                    <td><?= htmlspecialchars($row['half_days']) ?></td>
                    <td><?= htmlspecialchars($row['early_leaves']) ?></td>
                    <td><?= htmlspecialchars($row['net_salary']) ?></td>
                    <!--<td><a href="edit_salary.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn btn-sm btn-warning">Edit</a></td>-->
                    <td>
    <a href="edit_salary.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
    <a href="generate_slip.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn btn-sm btn-success" target="_blank">Slip</a>
</td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="12" class="text-center">No records found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>
</body>
</html>