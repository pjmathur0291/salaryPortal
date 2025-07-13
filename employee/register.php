<?php
require '../db.php';
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // Check if email exists in employees table and is not already registered
    $stmt = $conn->prepare("SELECT id, username FROM employees WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $emp = $result->fetch_assoc();
    $stmt->close();

    if (!$emp) {
        $error = "No employee found with this email address.";
    } elseif (!empty($emp['username'])) {
        $error = "This email is already registered for the portal.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Check if username is unique
        $stmt = $conn->prepare("SELECT id FROM employees WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "Username already taken.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE employees SET username = ?, password_hash = ? WHERE id = ?");
            $stmt->bind_param('ssi', $username, $hash, $emp['id']);
            $stmt->execute();
            $success = "Registration successful! You can now <a href='index.php'>login</a>.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Employee Registration</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="container" style="max-width:400px;margin-top:60px;">
    <h2>Employee Registration</h2>
    <?php if ($error): ?><div style="color:red;"><?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div style="color:green;"><?= $success ?></div><?php endif; ?>
    <form method="post">
        <div class="form-group">
            <label>Company Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top:10px;">Register</button>
        <a href="index.php" class="btn btn-secondary" style="margin-top:10px;">Back to Login</a>
    </form>
</div>
</body>
</html>