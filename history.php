<?php
// history.php
session_start();
require 'db_connection.php';

// Protect page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch watch history
$stmt = $pdo->prepare("
    SELECT 
        m.movie_name,
        m.movie_poster,
        w.watch_percentage,
        w.last_watched
    FROM watch_history w
    JOIN movies m ON w.movie_id = m.id
    WHERE w.user_id = :uid
    ORDER BY w.last_watched DESC
");
$stmt->execute(['uid' => $user_id]);
$history = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Watch History - Infinity</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="stylesheet" href="style.css">

<style>
body {
    background:#121212;
    color:#fff;
}
.history-item {
    display:flex;
    gap:15px;
    background:#1e1e1e;
    padding:15px;
    border-radius:10px;
    margin-bottom:15px;
}
.history-item img {
    width:120px;
    height:180px;
    object-fit:cover;
    border-radius:8px;
}
.progress {
    height: 8px;
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

<div class="container mt-4">
    <h2>Your Watch History</h2>

    <?php if (count($history) > 0): ?>
        <?php foreach ($history as $row): ?>
            <div class="history-item">
                <img src="<?php echo $row['movie_poster']; ?>"
                     onerror="this.src='elements/default-poster.png';">

                <div class="flex-grow-1">
                    <h5><?php echo htmlspecialchars($row['movie_name']); ?></h5>

                    <div class="progress mb-2">
                        <div class="progress-bar bg-success"
                             style="width: <?php echo $row['watch_percentage']; ?>%">
                        </div>
                    </div>

                    <small>
                        Watched: <?php echo $row['watch_percentage']; ?>% <br>
                        Last watched: <?php echo $row['last_watched']; ?>
                    </small>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>You haven’t watched any movies yet.</p>
    <?php endif; ?>
</div>

<script src="script.js"></script>
</body>
</html>
