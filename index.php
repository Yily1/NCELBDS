<?php
session_start();
require_once 'config/config.php';
require_once 'config/functions.php';
//require_once 'includes/activity-logger.php';

if (isLoggedIn()) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: app/admin/dashboard.php");
    } else {
        header("Location: app/user/dashboard.php");
    }
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // For testing: all users have password "password123"
    if ($user && $password === "password123") {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        logActivity($conn, $user['user_id'], "login");

        if ($user['role'] === 'admin') {
            header("Location: app/admin/dashboard.php");
        } else {
            header("Location: app/user/dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Native Chicken Egg Laying Behavior Detection System</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<h2>Native Chicken Egg Laying Behavior Detection System</h2>
<div class="login-box">
<h3>Login</h3>
<?php if($error){ ?>
<p class="error"><?php echo $error; ?></p>
<?php } ?>
<form method="POST">
<input type="text" name="username" placeholder="Username" required>
<input type="password" name="password" placeholder="Password" required>
<button type="submit">Login</button>
</form>
</div>
</body>
</html>