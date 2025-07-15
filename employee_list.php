<?php
require 'db.php';

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
    <title>Employee Directory</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php if (file_exists('header.php')) include 'header.php'; ?>
<div class="container mt-4">
    <h2>Employee Directory</h2>
    <a href="archived_employees.php" style="margin-bottom: 10px; display: inline-block;">View Archived Employees</a>
    <form method="get" class="row g-3 mb-4" style="max-width:600px;">
        <div class="col-md-7">
            <input type="text" name="search" class="form-control" placeholder="Search by name..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        </div>
        <div class="col-md-4">
            <select name="department" class="form-select">
                <option value="">All Departments</option>
                <option value="Social Media" <?= (isset($_GET['department']) && $_GET['department'] == 'Social Media') ? 'selected' : '' ?>>Social Media</option>
                <option value="Developer" <?= (isset($_GET['department']) && $_GET['department'] == 'Developer') ? 'selected' : '' ?>>Developer</option>
                <option value="SEO" <?= (isset($_GET['department']) && $_GET['department'] == 'SEO') ? 'selected' : '' ?>>SEO</option>
                <option value="Google Ads" <?= (isset($_GET['department']) && $_GET['department'] == 'Google Ads') ? 'selected' : '' ?>>Google Ads</option>
                <option value="Meta Ads" <?= (isset($_GET['department']) && $_GET['department'] == 'Meta Ads') ? 'selected' : '' ?>>Meta Ads</option>
                <option value="Counselor" <?= (isset($_GET['department']) && $_GET['department'] == 'Counselor') ? 'selected' : '' ?>>Counselor</option>
                <option value="HR" <?= (isset($_GET['department']) && $_GET['department'] == 'HR') ? 'selected' : '' ?>>HR</option>
                <option value="Sales" <?= (isset($_GET['department']) && $_GET['department'] == 'Sales') ? 'selected' : '' ?>>Sales</option>
                <option value="Video Editing" <?= (isset($_GET['department']) && $_GET['department'] == 'Video Editing') ? 'selected' : '' ?>>Video Editing</option>
                <option value="Graphic Designing" <?= (isset($_GET['department']) && $_GET['department'] == 'Graphic Designing') ? 'selected' : '' ?>>Graphic Designing</option>
                <option value="CEO" <?= (isset($_GET['department']) && $_GET['department'] == 'CEO') ? 'selected' : '' ?>>CEO</option>
                <option value="Accountant" <?= (isset($_GET['department']) && $_GET['department'] == 'Accountant') ? 'selected' : '' ?>>Accountant</option>
            </select>
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-primary w-100">Search</button>
        </div>
    </form>
    <div class="employee-list">
        <?php if (count($employees) > 0): ?>
            <?php foreach ($employees as $emp): ?>
                <div class="employee-card" style="border:1px solid #ddd; border-radius:8px; padding:18px 20px; margin-bottom:18px; display:flex; align-items:center; justify-content:space-between; background:#fafbfc;">
                    <div>
                        <div style="font-size:1.2em; font-weight:600; color:#1976d2;">Name: <?= htmlspecialchars($emp['name']) ?></div>
                        <div style="font-size:0.98em; color:#444;">Employee ID: <?= htmlspecialchars($emp['id']) ?></div>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <a href="employee_detail.php?id=<?= $emp['id'] ?>" class="btn btn-primary" style="padding:8px 18px; border-radius:5px; font-weight:500;">View Details</a>
                        <a href="archive_employee.php?id=<?= $emp['id'] ?>" class="btn btn-warning" style="padding:8px 18px; border-radius:5px; font-weight:500;" onclick="return confirm('Are you sure you want to mark this employee as inactive?')">Inactive</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="color:#888;">No employees found.</div>
        <?php endif; ?>
    </div>
</div>
</body>
</html> 