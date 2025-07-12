<?php
session_start();
// If already logged in, redirect
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: salary_entry.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    // Hardcoded credentials
    if ($username === 'admin' && $password === 'password123') {
        $_SESSION['logged_in'] = true;
        header('Location: salary_entry.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Salary Calculator</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .login-container { max-width: 350px; margin: 80px auto; background: #fff; border-radius: 10px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); padding: 32px 24px; }
        .login-container h2 { text-align: center; margin-bottom: 24px; }
        .login-container .form-groups { margin-bottom: 18px; }
        .login-container label { display: block; margin-bottom: 6px; font-weight: 500; }
        .login-container input { width: 100%; padding: 10px; border: 1px solid #bbb; border-radius: 6px; font-size: 1rem; background: #f9f9f9; }
        .login-container button { width: 100%; padding: 12px; background: #1976d2; color: #fff; border: none; border-radius: 6px; font-size: 1.1rem; font-weight: 600; cursor: pointer; margin-top: 10px; }
        .login-container button:hover { background: #1256a3; }
        .login-container .error { color: #d32f2f; text-align: center; margin-bottom: 12px; }
        .loginPortal{width: 100% !important;}
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Salary Portal Login</h2>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-groups loginPortal">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>
            <div class="form-groups loginPortal">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html> 