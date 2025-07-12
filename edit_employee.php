<?php
require 'db.php';

if (!isset($_GET['id'])) {
    header('Location: employees.php');
    exit;
}
$id = intval($_GET['id']);

// Fetch employee data
$stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$emp = $result->fetch_assoc();
$stmt->close();

if (!$emp) {
    echo "Employee not found.";
    exit;
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $department = trim($_POST['department']);
    $email = trim($_POST['email']);
    $dob = $_POST['dob'] ?? null;
    $company_email = trim($_POST['company_email']);
    $date_of_joining = $_POST['date_of_joining'] ?? null;
    $documents_submitted = trim($_POST['documents_submitted']);
    $manager = trim($_POST['manager']);
    $assets_given = trim($_POST['assets_given']);
    $basic_salary = isset($_POST['basic_salary']) ? floatval($_POST['basic_salary']) : 0;

    $stmt = $conn->prepare("UPDATE employees SET name=?, department=?, email=?, dob=?, company_email=?, date_of_joining=?, documents_submitted=?, manager=?, assets_given=?, basic_salary=? WHERE id=?");
    $stmt->bind_param('ssssssssddi', $name, $department, $email, $dob, $company_email, $date_of_joining, $documents_submitted, $manager, $assets_given, $basic_salary, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: employees.php?updated=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Employee</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'header.php'; ?>
<div class="container mt-4">
    <h1>Edit Employee</h1>
    <form method="POST" class="row g-3">
        <div class="col-md-6">
            <label for="name" class="form-label">Employee Name</label>
            <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($emp['name']) ?>" required>
        </div>
        <div class="col-md-6">
            <label for="department" class="form-label">Department</label>
            <select name="department" id="department" class="form-control" required>
                <option value="">Select Department</option>
                <option value="Social Media" <?= $emp['department'] == 'Social Media' ? 'selected' : '' ?>>Social Media</option>
                <option value="Developer" <?= $emp['department'] == 'Developer' ? 'selected' : '' ?>>Developer</option>
                <option value="SEO" <?= $emp['department'] == 'SEO' ? 'selected' : '' ?>>SEO</option>
                <option value="Google Ads" <?= $emp['department'] == 'Google Ads' ? 'selected' : '' ?>>Google Ads</option>
                <option value="Meta Ads" <?= $emp['department'] == 'Meta Ads' ? 'selected' : '' ?>>Meta Ads</option>
                <option value="Counselor" <?= $emp['department'] == 'Counselor' ? 'selected' : '' ?>>Counselor</option>
                <option value="HR" <?= $emp['department'] == 'HR' ? 'selected' : '' ?>>HR</option>
                <option value="Sales" <?= $emp['department'] == 'Sales' ? 'selected' : '' ?>>Sales</option>
                <option value="Video Editing" <?= $emp['department'] == 'Video Editing' ? 'selected' : '' ?>>Video Editing</option>
                <option value="Graphic Designing" <?= $emp['department'] == 'Graphic Designing' ? 'selected' : '' ?>>Graphic Designing</option>
                <option value="CEO" <?= $emp['department'] == 'CEO' ? 'selected' : '' ?>>CEO</option>
                <option value="Accountant" <?= $emp['department'] == 'Accountant' ? 'selected' : '' ?>>Accountant</option>
            </select>
        </div>
        <div class="col-md-6">
            <label for="email" class="form-label">Personal Email</label>
            <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($emp['email']) ?>">
        </div>
        <div class="col-md-6">
            <label for="dob" class="form-label">Date of Birth</label>
            <input type="date" name="dob" id="dob" class="form-control" value="<?= htmlspecialchars($emp['dob']) ?>">
        </div>
        <div class="col-md-6">
            <label for="company_email" class="form-label">Company Email</label>
            <input type="email" name="company_email" id="company_email" class="form-control" value="<?= htmlspecialchars($emp['company_email']) ?>">
        </div>
        <div class="col-md-6">
            <label for="date_of_joining" class="form-label">Date of Joining</label>
            <input type="date" name="date_of_joining" id="date_of_joining" class="form-control" value="<?= htmlspecialchars($emp['date_of_joining']) ?>">
        </div>
        <div class="col-md-6">
            <label for="documents_submitted" class="form-label">Documents Submitted</label>
            <input type="text" name="documents_submitted" id="documents_submitted" class="form-control" value="<?= htmlspecialchars($emp['documents_submitted']) ?>">
        </div>
        <div class="col-md-6">
            <label for="manager" class="form-label">Manager</label>
            <input type="text" name="manager" id="manager" class="form-control" value="<?= htmlspecialchars($emp['manager']) ?>">
        </div>
        <div class="col-md-6">
            <label for="assets_given" class="form-label">Assets Given</label>
            <input type="text" name="assets_given" id="assets_given" class="form-control" value="<?= htmlspecialchars($emp['assets_given']) ?>">
        </div>
        <div class="col-md-6">
            <label for="basic_salary" class="form-label">Basic Salary</label>
            <input type="number" name="basic_salary" id="basic_salary" class="form-control" step="0.01" min="0" value="<?= htmlspecialchars($emp['basic_salary']) ?>">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-success">Update Employee</button>
            <a href="employees.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
</body>
</html>