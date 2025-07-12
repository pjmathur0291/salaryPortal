<?php
require 'db.php';
require 'pdf/fpdf.php'; // Adjust path if needed

if (!isset($_GET['id'])) {
    die('No salary record specified.');
}
$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT sr.*, e.name AS employee_name, e.department FROM salary_records sr JOIN employees e ON sr.employee_id = e.id WHERE sr.id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
if (!$result || $result->num_rows === 0) {
    die('Salary record not found.');
}
$row = $result->fetch_assoc();

$pdf = new FPDF();
$pdf->AddPage();

// Company Header
// $pdf->Image('path/to/logo.png',10,10,30); // Uncomment and set path if you have a logo
$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(0, 12, 'Your Company Name Pvt. Ltd.', 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, 'Salary Slip', 0, 1, 'C');
$pdf->Ln(2);
$pdf->SetDrawColor(100,100,100);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

// Employee Info Section
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(50, 8, 'Employee Name:', 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(60, 8, $row['employee_name'], 0, 0);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(30, 8, 'Department:', 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, $row['department'], 0, 1);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(50, 8, 'Month:', 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(60, 8, date('F', mktime(0,0,0,$row['month'],10)).' '.$row['year'], 0, 1);

$pdf->Ln(5);

// Salary Details Table
$pdf->SetFillColor(230,230,250);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(60, 10, 'Earnings', 1, 0, 'C', true);
$pdf->Cell(60, 10, 'Deductions', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(60, 8, 'Basic Salary: '.number_format($row['basic_salary'],2), 1, 0);
$pdf->Cell(60, 8, 'Leaves: '.$row['leaves'], 1, 1);
$pdf->Cell(60, 8, 'Allowances: '.number_format($row['allowances'],2), 1, 0);
$pdf->Cell(60, 8, 'Half Days: '.$row['half_days'], 1, 1);
$pdf->Cell(60, 8, '', 0, 0);
$pdf->Cell(60, 8, 'Early Leaves: '.$row['early_leaves'], 1, 1);
$pdf->Cell(60, 8, '', 0, 0);
$pdf->Cell(60, 8, 'Other Deductions: '.number_format($row['deductions'],2), 1, 1);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(60, 10, 'Net Salary', 1, 0, 'C', true);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(60, 10, number_format($row['net_salary'],2), 1, 1, 'C');

$pdf->Ln(10);

// Note/Content Section
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 8, "This is a computer-generated salary slip for your records. Please contact HR for any queries.\n\nThank you for your service!");

$pdf->Ln(15);
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 8, 'HR Signature: ______________________', 0, 1, 'R');

// Clean up employee name for filename
$clean_name = preg_replace('/[^A-Za-z0-9_]/', '_', $row['employee_name']);
$month_name = date('F', mktime(0,0,0,$row['month'],10));
$filename = $clean_name . '_' . $month_name . '_' . $row['year'] . '.pdf';

// Save to pdf folder and force download
$pdf_folder = __DIR__ . '/pdf/';
if (!is_dir($pdf_folder)) {
    mkdir($pdf_folder, 0777, true);
}
$pdf_path = $pdf_folder . $filename;
$pdf->Output('F', $pdf_path); // Save to server

// Force download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
readfile($pdf_path);
exit;