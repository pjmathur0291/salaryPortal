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
<!-- Font Awesome Icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
body {
    background: #f4f6fa;
}
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: 60px;
    background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
    color: #fff;
    z-index: 1040;
    box-shadow: 2px 0 10px rgba(0,0,0,0.08);
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 1rem;
    transition: width 0.2s;
}
.sidebar.expanded {
    width: 240px;
    align-items: flex-start;
    transition: width 0.2s;
}
.sidebar .sidebar-brand {
    font-weight: 700;
    font-size: 1.5rem;
    letter-spacing: 0.5px;
    margin-bottom: 2rem;
    color: #fff;
    text-decoration: none;
    width: 100%;
    text-align: center;
    white-space: nowrap;
    transition: opacity 0.2s;
}
.sidebar:not(.expanded) .sidebar-brand-text {
    display: none;
}
.sidebar .hamburger {
    background: none;
    border: none;
    color: #fff;
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    margin-left: 0.5rem;
    cursor: pointer;
    outline: none;
    align-self: flex-start;
}
.sidebar .nav-link {
    color: #fff;
    font-weight: 500;
    border-radius: 6px;
    margin: 2px 0;
    padding: 0.75rem 1.5rem;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    width: 100%;
    white-space: nowrap;
}
.sidebar .nav-link .nav-text {
    margin-left: 1rem;
    transition: opacity 0.2s;
}
.sidebar:not(.expanded) .nav-text {
    display: none;
}
.sidebar .nav-link.active, .sidebar .nav-link:hover {
    background: rgba(255,255,255,0.13);
    color: #fff;
    font-weight: 600;
}
.sidebar .sidebar-section {
    margin-bottom: 1.5rem;
    width: 100%;
}
.sidebar .sidebar-section-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: #bbdefb;
    margin: 1.2rem 1.5rem 0.5rem 1.5rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: opacity 0.2s;
}
.sidebar:not(.expanded) .sidebar-section-title {
    display: none;
}
.sidebar .logout-btn {
    margin-top: auto;
    margin-bottom: 1.5rem;
    width: 80%;
    align-self: center;
}
.sidebar:not(.expanded) .logout-btn .btn {
    width: 40px;
    padding: 0.5rem 0.5rem;
}
.sidebar .logout-btn .logout-text {
    margin-left: 0.5rem;
    transition: opacity 0.2s;
}
.sidebar:not(.expanded) .logout-text {
    display: none;
}
.main-content {
    margin-left: 60px;
    padding: 2rem 2rem 0 2rem;
    transition: margin-left 0.2s;
}
.sidebar.expanded ~ .main-content {
    margin-left: 240px;
    transition: margin-left 0.2s;
}
@media (max-width: 991.98px) {
    .sidebar, .sidebar.expanded {
        width: 100vw;
        height: auto;
        position: static;
        flex-direction: row;
        padding: 0.5rem 0;
    }
    .main-content, .sidebar.expanded ~ .main-content {
        margin-left: 0;
        padding: 1rem;
    }
}
</style>
<div class="sidebar" id="sidebar">
    <button class="hamburger" id="sidebarToggle" aria-label="Toggle navigation">
        <i class="fas fa-bars"></i>
    </button>
    <a href="dashboard.php" class="sidebar-brand">
        <span class="sidebar-brand-text">Salary Portal</span>
    </a>
    <div class="sidebar-section">
        <div class="sidebar-section-title">Navigation</div>
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">
            <i class="fas fa-tachometer-alt"></i><span class="nav-text">Dashboard</span>
        </a>
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'employee_list.php' ? 'active' : '' ?>" href="employee_list.php">
            <i class="fas fa-list"></i><span class="nav-text">Employee List</span>
        </a>
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'employees.php' ? 'active' : '' ?>" href="employees.php">
            <i class="fas fa-plus"></i><span class="nav-text">Add Employee</span>
        </a>
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'archived_employees.php' ? 'active' : '' ?>" href="archived_employees.php">
            <i class="fas fa-archive"></i><span class="nav-text">Archived Employees</span>
        </a>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-section-title">Salary</div>
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'salary_entry.php' ? 'active' : '' ?>" href="salary_entry.php">
            <i class="fas fa-plus-circle"></i><span class="nav-text">Salary Entry</span>
        </a>
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'salary_report.php' ? 'active' : '' ?>" href="salary_report.php">
            <i class="fas fa-chart-bar"></i><span class="nav-text">Salary Report</span>
        </a>
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'inactive_salary_report.php' ? 'active' : '' ?>" href="inactive_salary_report.php">
            <i class="fas fa-chart-line"></i><span class="nav-text">Inactive Salary Report</span>
        </a>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-section-title">Leave</div>
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'leave_applications.php' ? 'active' : '' ?>" href="leave_applications.php">
            <i class="fas fa-clipboard-list"></i><span class="nav-text">Leave Applications</span>
        </a>
    </div>
    <form action="logout.php" method="post" class="logout-btn">
        <button type="submit" class="btn btn-danger w-100 d-flex align-items-center justify-content-center">
            <i class="fas fa-sign-out-alt"></i><span class="logout-text">Logout</span>
        </button>
    </form>
</div>
<!-- Main content wrapper: add <div class="main-content"> in your main pages after including header.php -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const sidebar = document.getElementById('sidebar');
const sidebarToggle = document.getElementById('sidebarToggle');
sidebarToggle.addEventListener('click', function() {
    sidebar.classList.toggle('expanded');
    // Also toggle main-content margin if present
    const mainContent = document.querySelector('.main-content');
    if (mainContent) {
        mainContent.classList.toggle('sidebar-expanded');
    }
});
</script>