<?php 
require 'config.php'; 
$error = ""; 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }
    $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE username = ?");
    $stmt->execute([$_POST['username']]);
    $user = $stmt->fetch();
    if ($user && password_verify($_POST['password'], $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: dashboard.php");
        exit;
    } else { $error = "Invalid username or password."; }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <style>
        /* PERFECT CENTERING: Uses Flexbox on the body to center the card [4, 5] */
        body { 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            background-color: #f7fafc; 
            margin: 0;
        }
        .login-card { 
            width: 500px; /* Standardized width [6] */
            padding: 40px; 
            background: white; 
            border-radius: 8px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); 
            text-align: center;
        }
        input { width: 100%; box-sizing: border-box; }
    </style>
</head>
<body>
    <div class="login-card">
        <form method="POST">
            <h1>Login</h1>
            <?php if ($error) echo "<p style='color:red'>".htmlspecialchars($error)."</p>"; ?>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn" style="width: 100%;">Login</button>
        </form>
    </div>
</body>
</html>
