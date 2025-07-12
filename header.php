<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
?>
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container-fluid px-5">
    <a class="navbar-brand" href="dashboard.php">Salary Portal</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white" href="employees.php">Employees</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white" href="salary_entry.php">Salary Entry</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white" href="salary_report.php">Salary Report</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link text-white dropdown-toggle" href="#" id="inactiveDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Inactive
          </a>
          <ul class="dropdown-menu" aria-labelledby="inactiveDropdown">
            <li><a class="dropdown-item text-black" href="archived_employees.php">Archived Employees</a></li>
            <li><a class="dropdown-item text-black" href="inactive_salary_report.php">Inactive Salary Report</a></li>
          </ul>
        </li>
      </ul>
      <form action="logout.php" method="post" class="d-flex">
        <button type="submit" class="btn btn-danger" style="background: #D32F2F !important; width: 100px;">Logout</button>
      </form>
    </div>
  </div>
</nav>

<!-- Bootstrap 5 JS (for dropdown and mobile menu) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>