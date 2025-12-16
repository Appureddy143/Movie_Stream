<?php
// login.php
session_start();
require 'db_connection.php';

$error = "";

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } else {
        // Fetch user by email
        $stmt = $pdo->prepare("
            SELECT id, username, password, is_admin
            FROM users
            WHERE email = :email
        ");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {

            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['is_admin'] ? 'admin' : 'user';

            // Redirect
            if ($_SESSION['role'] === 'admin') {
                header("Location: admin_panel.php");
            } else {
                header("Location: home.php");
            }
            exit;

        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - Infinity</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="stylesheet" href="style.css">

<style>
body {
    margin: 0;
    padding: 0;
    height: 100vh;
    overflow: hidden;
}

.bg-video {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: -1;
}

.container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.form-box {
    background: rgba(255, 255, 255, 0.5);
    padding: 25px;
    border-radius: 10px;
    width: 350px;
}

.logo img {
    max-width: 150px;
}
</style>
</head>

<body>

<!-- Loader -->
<div id="loading-screen">
    <div class="spinner-box">
        <div class="circle-border">
            <div class="circle-core"></div>
        </div>
        <h3>Loading, please wait...</h3>
    </div>
</div>

<!-- Background Video -->
<video class="bg-video" autoplay muted loop>
    <source src="elements/video/back.mp4" type="video/mp4">
</video>

<div class="container">
    <div class="form-box">

        <div class="logo text-center mb-3">
            <img src="elements/logo/logo.png" alt="Infinity">
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button class="btn btn-primary w-100">Login</button>
        </form>

        <div class="text-center mt-3">
            <a href="register.php">Create new account</a>
        </div>

    </div>
</div>

<script src="script.js"></script>
</body>
</html>
