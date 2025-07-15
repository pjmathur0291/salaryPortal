    <?php
    session_start();
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: login.php');
        exit;
    }
    require 'db.php';

    // Handle approve/reject actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['leave_id'])) {
        $leave_id = intval($_POST['leave_id']);
        $action = $_POST['action'];
        $status = ($action === 'approve') ? 'approved' : 'rejected';
        
        $stmt = $conn->prepare("UPDATE leave_applications SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $status, $leave_id);
        $stmt->execute();
        $stmt->close();
        
        header('Location: leave_applications.php?success=1');
        exit;
    }

    // Fetch employee list for filter dropdown
    $employeeList = [];
    $empResult = $conn->query("SELECT id, name FROM employees WHERE status = 'active' ORDER BY name ASC");
    while ($row = $empResult->fetch_assoc()) {
        $employeeList[] = $row;
    }

    // Filtering logic
    $where = [];
    $params = [];
    $types = '';
    if (!empty($_GET['employee_id'])) {
        $where[] = 'la.employee_id = ?';
        $params[] = intval($_GET['employee_id']);
        $types .= 'i';
    }
    if (!empty($_GET['month'])) {
        $where[] = 'MONTH(la.from_date) = ?';
        $params[] = intval($_GET['month']);
        $types .= 'i';
    }
    $sql = "SELECT la.*, e.name as employee_name, e.department 
            FROM leave_applications la 
            JOIN employees e ON la.employee_id = e.id";
    if ($where) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }
    $sql .= " ORDER BY la.created_at DESC";

    if ($params) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($sql);
    }
    $applications = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $applications[] = $row;
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Leave Applications</title>
        <link rel="stylesheet" href="style.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <?php include 'header.php'; ?>
    <div class="container">
        <h1>Leave Applications</h1>
        <?php if (isset($_GET['success'])): ?>
            <div style="color:green; margin-bottom:10px;">Leave application updated successfully.</div>
        <?php endif; ?>
        <?php if (isset($_GET['deleted'])): ?>
            <div style="color:green; margin-bottom:10px;">Leave applications deleted successfully.</div>
        <?php endif; ?>
        
        <!-- Filter Form -->
        <form method="GET" class="row g-3 mb-3">
            <div class="col-md-4">
                <label for="employee_id" class="form-label">Employee</label>
                <select name="employee_id" id="employee_id" class="form-select">
                    <option value="">All Employees</option>
                    <?php foreach ($employeeList as $emp): ?>
                        <option value="<?= $emp['id'] ?>" <?= isset($_GET['employee_id']) && $_GET['employee_id'] == $emp['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($emp['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="month" class="form-label">Month</label>
                <select name="month" id="month" class="form-select">
                    <option value="">All Months</option>
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>" <?= isset($_GET['month']) && $_GET['month'] == $m ? 'selected' : '' ?>>
                            <?= date('F', mktime(0, 0, 0, $m, 10)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Filter</button>
                <a href="leave_applications.php" class="btn btn-secondary">Reset</a>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Leave Type</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>Days</th>
                        <th>Reason</th>
                        <th>Half Day Option</th>
                        <th>Late Join Time</th>
                        <th>Status</th>
                        <th>Applied On</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($applications) > 0): ?>
                        <?php foreach ($applications as $app): ?>
                            <tr>
                                <td><?= htmlspecialchars($app['employee_name']) ?></td>
                                <td><?= htmlspecialchars($app['department']) ?></td>
                                <td>
                                    <?php
                                    $type = trim($app['leave_type']);
                                    $badgeClass = 'bg-secondary';
                                    if ($type === 'Paid') $badgeClass = 'bg-success';
                                    elseif ($type === 'Unpaid') $badgeClass = 'bg-warning text-dark';
                                    elseif (strtolower(str_replace(' ', '', $type)) === 'halfday') $badgeClass = 'bg-info text-dark';
                                    elseif ($type === 'Late Join') $badgeClass = 'bg-primary';
                                    elseif ($type === 'Early') $badgeClass = 'bg-dark';
                                    $displayType = $type !== '' ? htmlspecialchars($type) : 'N/A';
                                    ?>
                                    <span class="badge <?= $badgeClass ?>">
                                        <?= $displayType ?>
                                    </span>
                                </td>
                                <td><?= date('d M Y', strtotime($app['from_date'])) ?></td>
                                <td><?= date('d M Y', strtotime($app['to_date'])) ?></td>
                                <td>
                                    <?php
                                    $from = new DateTime($app['from_date']);
                                    $to = new DateTime($app['to_date']);
                                    $days = $from->diff($to)->days + 1;
                                    echo $days;
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($app['reason']) ?></td>
                                <td>
                                    <?php
                                    // Show half_day_option if not empty, otherwise N/A
                                    if (!empty($app['half_day_option'])) {
                                        echo '<span class="badge bg-info text-dark">' . htmlspecialchars($app['half_day_option']) . '</span>';
                                    } else {
                                        echo '<span class="text-muted">N/A</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if ($app['leave_type'] === 'Late Join') {
                                        echo !empty($app['late_join_time']) ? '<span class="badge bg-secondary text-light">' . htmlspecialchars($app['late_join_time']) . '</span>' : '<span class="text-muted">N/A</span>';
                                    } else {
                                        echo '<span class="text-muted">N/A</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $status = $app['status'] ?? 'pending';
                                    $statusClass = '';
                                    switch($status) {
                                        case 'approved': $statusClass = 'bg-success'; break;
                                        case 'rejected': $statusClass = 'bg-danger'; break;
                                        case 'cancelled': $statusClass = 'bg-secondary'; break;
                                        default: $statusClass = 'bg-warning'; break;
                                    }
                                    ?>
                                    <span class="badge <?= $statusClass ?>">
                                        <?= ucfirst($status) ?>
                                    </span>
                                </td>
                                <td><?= date('d M Y', strtotime($app['created_at'])) ?></td>
                                <td>
                                    <?php if (($app['status'] ?? 'pending') === 'pending'): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="leave_id" value="<?= $app['id'] ?>">
                                            <button type="submit" name="action" value="approve" class="btn btn-success btn-sm" onclick="return confirm('Approve this leave application?')">Approve</button>
                                            <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm" onclick="return confirm('Reject this leave application?')">Reject</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted">Processed</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="12" class="text-center">No leave applications found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
    // No delete or select all logic needed
    </script>
    </body>
    </html> 