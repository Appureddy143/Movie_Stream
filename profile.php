<?php
// profile.php
session_start();
require 'db_connection.php';

// Protect page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user
$stmt = $pdo->prepare("
    SELECT username, email, dob, avatar
    FROM users
    WHERE id = :id
");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Profile - Infinity</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="stylesheet" href="style.css">

<style>
body {
    background:#121212;
    color:#fff;
}
.profile-container {
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    min-height:100vh;
}
.avatar {
    width:150px;
    height:150px;
    border-radius:50%;
    margin-bottom:20px;
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

<div class="profile-container">
    <img src="elements/img/ava/<?php echo htmlspecialchars($user['avatar']); ?>"
         class="avatar"
         onerror="this.src='elements/img/ava/avatar1.gif';">

    <h4><?php echo htmlspecialchars($user['username']); ?></h4>
    <p><?php echo htmlspecialchars($user['email']); ?></p>
    <p>Date of Birth: <?php echo htmlspecialchars($user['dob']); ?></p>

    <a href="logout.php" class="btn btn-danger mt-3">Logout</a>
</div>

<script src="script.js"></script>
</body>
</html>
