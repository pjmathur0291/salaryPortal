<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}
require 'db.php';

$employee_id = intval($_POST['employee_id'] ?? 0);
$month = intval($_POST['month'] ?? 0);
$year = intval($_POST['year'] ?? date('Y'));

$response = [
    'success' => false,
    'allowances' => 0,
    'deductions' => 0,
    'leaves' => 0,
    'half_days' => 0,
    'early_leaves' => 0,
    'late_leaves' => 0,
    'basic_salary' => 0,
    'net_salary' => 0
];

if ($employee_id && $month && $year) {
    // Fetch employee base data
    $emp = $conn->query("SELECT * FROM employees WHERE id = $employee_id")->fetch_assoc();
    if ($emp) {
        $response['basic_salary'] = floatval($emp['basic_salary'] ?? 0);
        $response['allowances'] = floatval($emp['allowances'] ?? 0);
        $response['deductions'] = floatval($emp['deductions'] ?? 0);
    }

    // Fetch leaves for the month
    $leave_types = ['Paid', 'Unpaid', 'Half Day', 'Early', 'Late'];
    $leaves = [];
    foreach ($leave_types as $type) {
        $q = $conn->prepare("SELECT COUNT(*) as cnt FROM leave_applications WHERE employee_id=? AND leave_type=? AND status='approved' AND MONTH(from_date)=? AND YEAR(from_date)=?");
        $q->bind_param('isii', $employee_id, $type, $month, $year);
        $q->execute();
        $r = $q->get_result()->fetch_assoc();
        $leaves[$type] = intval($r['cnt']);
        $q->close();
    }
    $response['leaves'] = $leaves['Paid'] + $leaves['Unpaid'];
    $response['half_days'] = $leaves['Half Day'];
    $response['early_leaves'] = $leaves['Early'];
    $response['late_leaves'] = $leaves['Late'];

    // Fetch lifetime leaves for each type (for first-free logic)
    $lifetime = [];
    foreach ($leave_types as $type) {
        $q = $conn->prepare("SELECT COUNT(*) as cnt FROM leave_applications WHERE employee_id=? AND leave_type=? AND status='approved'");
        $q->bind_param('is', $employee_id, $type);
        $q->execute();
        $r = $q->get_result()->fetch_assoc();
        $lifetime[$type] = intval($r['cnt']);
        $q->close();
    }

    // Calculate per day salary
    $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $per_day_salary = $response['basic_salary'] / $days_in_month;

    // Deduction logic
    $deduction = 0;
    // Paid/Unpaid: full day, after first free
    $deductible_paid = max(0, $leaves['Paid'] - (1 - min(1, $lifetime['Paid'] - $leaves['Paid'])));
    $deductible_unpaid = max(0, $leaves['Unpaid'] - (1 - min(1, $lifetime['Unpaid'] - $leaves['Unpaid'])));
    $deduction += ($deductible_paid + $deductible_unpaid) * $per_day_salary;
    // Half Day: 0.5 per day, after first free
    $deductible_half = max(0, $leaves['Half Day'] - (1 - min(1, $lifetime['Half Day'] - $leaves['Half Day'])));
    $deduction += $deductible_half * ($per_day_salary / 2);
    // Early: 1/4 per day, after first free
    $deductible_early = max(0, $leaves['Early'] - (1 - min(1, $lifetime['Early'] - $leaves['Early'])));
    $deduction += $deductible_early * ($per_day_salary / 4);
    // Late: 1/4 per day, after first free
    $deductible_late = max(0, $leaves['Late'] - (1 - min(1, $lifetime['Late'] - $leaves['Late'])));
    $deduction += $deductible_late * ($per_day_salary / 4);

    // Add other deductions
    $total_deductions = $response['deductions'] + $deduction;

    // Net salary
    $response['net_salary'] = round($response['basic_salary'] + $response['allowances'] - $total_deductions, 2);
    $response['success'] = true;
}

header('Content-Type: application/json');
echo json_encode($response);
exit; 