<?php
session_start();
if (!isset($_SESSION['employee_id'])) {
    header('Location: index.php');
    exit;
}
require '../db.php';
$employee_id = $_SESSION['employee_id'];
$result = $conn->query("SELECT * FROM leave_applications WHERE employee_id = $employee_id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Leave History</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container" style="max-width:1000px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-history me-2"></i>My Leave History</h2>
        <a href="dashboard.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
        </a>
    </div>
    
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th><i class="fas fa-tag me-1"></i>Type</th>
                            <th><i class="fas fa-calendar-day me-1"></i>From</th>
                            <th><i class="fas fa-calendar-day me-1"></i>To</th>
                            <th><i class="fas fa-comment me-1"></i>Reason</th>
                            <th><i class="fas fa-info-circle me-1"></i>Status</th>
                            <th><i class="fas fa-clock me-1"></i>Applied At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <span class="badge <?= $row['leave_type'] === 'Paid' ? 'bg-success' : 'bg-warning' ?>">
                                        <i class="fas <?= $row['leave_type'] === 'Paid' ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> me-1"></i>
                                        <?= htmlspecialchars($row['leave_type']) ?>
                                    </span>
                                </td>
                                <td><?= date('d M Y', strtotime($row['from_date'])) ?></td>
                                <td><?= date('d M Y', strtotime($row['to_date'])) ?></td>
                                <td>
                                    <span class="text-muted" title="<?= htmlspecialchars($row['reason']) ?>">
                                        <?= strlen($row['reason']) > 30 ? substr(htmlspecialchars($row['reason']), 0, 30) . '...' : htmlspecialchars($row['reason']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $status = $row['status'] ?? 'pending';
                                    $statusClass = '';
                                    $statusIcon = '';
                                    switch($status) {
                                        case 'approved': 
                                            $statusClass = 'bg-success'; 
                                            $statusIcon = 'fa-check-circle';
                                            break;
                                        case 'rejected': 
                                            $statusClass = 'bg-danger'; 
                                            $statusIcon = 'fa-times-circle';
                                            break;
                                        default: 
                                            $statusClass = 'bg-warning'; 
                                            $statusIcon = 'fa-clock';
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?= $statusClass ?>">
                                        <i class="fas <?= $statusIcon ?> me-1"></i>
                                        <?= ucfirst($status) ?>
                                    </span>
                                </td>
                                <td><?= date('d M Y, h:i A', strtotime($row['created_at'])) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No leave applications found.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>