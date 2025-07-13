<?php
session_start();
require '../db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $stmt = $conn->prepare("SELECT * FROM employees WHERE username = ? LIMIT 1");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $emp = $result->fetch_assoc();
    if ($emp && password_verify($password, $emp['password_hash'])) {
        $_SESSION['employee_id'] = $emp['id'];
        $_SESSION['employee_name'] = $emp['name'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Employee Login</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="container" style="max-width:400px;margin-top:60px;">
    <h2>Employee Login</h2>
    <?php if ($error): ?><div style="color:red;"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required autofocus>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top:10px;">Login</button>
    </form>
    <div style="margin-top:10px;">
        <a href="register.php">New user? Register here</a>
    </div>
</div>
</body>
</html>