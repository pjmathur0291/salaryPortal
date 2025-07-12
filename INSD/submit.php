<?php
// Turn on error reporting (for debugging only)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Capture form inputs
$firstName = $_POST['first_name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$campaign = 'INSD Lead Capture'; // Customize if needed
$eventId = 268; // As provided

// Encode the values for use in URLs
$firstName = urlencode($firstName);
$email = urlencode($email);
$phone = urlencode($phone);
$campaign = urlencode($campaign);

// Construct API URLs
$leadUrl = "https://prodn.expertrons.com/api/admin/marketingUnath/createCaptureLeadInLSQ?Phone=$phone&FirstName=$firstName&EmailAddress=$email&mx_Campaign=$campaign";
$activityUrl = "https://prodn.expertrons.com/api/admin/marketingUnath/addLSQActivity?mobile=$phone&eventId=$eventId";

// Send the requests
$leadResponse = file_get_contents($leadUrl);
$activityResponse = file_get_contents($activityUrl);

// Show a confirmation message
echo "<h2>Thank you! Your information has been submitted.</h2>";
echo "<p>Lead Response: $leadResponse</p>";
echo "<p>Activity Response: $activityResponse</p>";
?>
