<?php
session_start();
if (!isset($_SESSION['employee_id'])) {
    header('Location: index.php');
    exit;
}
require '../db.php';
$id = $_SESSION['employee_id'];
$stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$emp = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            height: 100%;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .action-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            text-align: center;
            height: 100%;
        }
        
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }
        
        .action-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
        }
        
        .btn-custom {
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .profile-info {
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 1.5rem;
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body style="background: #f8f9fa;">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2">
                        <i class="fas fa-user-circle me-3"></i>
                        Welcome back, <?= htmlspecialchars($emp['name']) ?>!
                    </h1>
                    <p class="mb-0 opacity-75">Employee Dashboard</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="logout.php" class="btn btn-outline-light btn-custom">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Profile Information -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="card-title mb-3">
                                    <i class="fas fa-user me-2 text-primary"></i>Profile Information
                                </h5>
                                <div class="row">
                                    <div class="col-sm-6 mb-2">
                                        <strong>Name:</strong><br>
                                        <span class="text-muted"><?= htmlspecialchars($emp['name']) ?></span>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <strong>Department:</strong><br>
                                        <span class="text-muted"><?= htmlspecialchars($emp['department']) ?></span>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <strong>Email:</strong><br>
                                        <span class="text-muted"><?= htmlspecialchars($emp['email']) ?></span>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <strong>Employee ID:</strong><br>
                                        <span class="text-muted">#<?= htmlspecialchars($emp['id']) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5 class="card-title mb-3">
                                    <i class="fas fa-calendar me-2 text-success"></i>Quick Actions
                                </h5>
                                <div class="d-grid gap-2">
                                    <a href="apply_leave.php" class="btn btn-primary btn-custom">
                                        <i class="fas fa-plus me-2"></i>Apply for Leave
                                    </a>
                                    <a href="leave_history.php" class="btn btn-outline-primary btn-custom">
                                        <i class="fas fa-history me-2"></i>View Leave History
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="stat-card">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-number text-primary">0</div>
                    <div class="text-muted">Approved Leaves</div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-number text-warning">0</div>
                    <div class="text-muted">Pending Leaves</div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card">
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stat-number text-success">0</div>
                    <div class="text-muted">Total Applications</div>
                </div>
            </div>
        </div>

        <!-- Action Cards -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="action-card">
                    <div class="action-icon bg-primary text-white">
                        <i class="fas fa-plus"></i>
                    </div>
                    <h5>Apply for Leave</h5>
                    <p class="text-muted">Submit a new leave application for approval</p>
                    <a href="apply_leave.php" class="btn btn-primary btn-custom">
                        <i class="fas fa-arrow-right me-2"></i>Get Started
                    </a>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="action-card">
                    <div class="action-icon bg-success text-white">
                        <i class="fas fa-history"></i>
                    </div>
                    <h5>Leave History</h5>
                    <p class="text-muted">View all your previous leave applications</p>
                    <a href="leave_history.php" class="btn btn-success btn-custom">
                        <i class="fas fa-arrow-right me-2"></i>View History
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>