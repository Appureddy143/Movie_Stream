<?php
// movie.php
session_start();
require 'db_connection.php';

// Protect page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Validate movie ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid movie.");
}

$movie_id = (int) $_GET['id'];
$user_id  = $_SESSION['user_id'];

// Fetch movie
$stmt = $pdo->prepare("
    SELECT *
    FROM movies
    WHERE id = :id
");
$stmt->execute(['id' => $movie_id]);
$movie = $stmt->fetch();

if (!$movie) {
    die("Movie not found.");
}

// ---- WATCH HISTORY (INSERT OR UPDATE) ----
$check = $pdo->prepare("
    SELECT id
    FROM watch_history
    WHERE user_id = :uid AND movie_id = :mid
");
$check->execute([
    'uid' => $user_id,
    'mid' => $movie_id
]);

if ($check->fetch()) {
    // Update last watched
    $update = $pdo->prepare("
        UPDATE watch_history
        SET last_watched = NOW()
        WHERE user_id = :uid AND movie_id = :mid
    ");
    $update->execute([
        'uid' => $user_id,
        'mid' => $movie_id
    ]);
} else {
    // Insert new
    $insert = $pdo->prepare("
        INSERT INTO watch_history (user_id, movie_id, watch_percentage)
        VALUES (:uid, :mid, 0)
    ");
    $insert->execute([
        'uid' => $user_id,
        'mid' => $movie_id
    ]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($movie['movie_name']); ?> - Infinity</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="stylesheet" href="style.css">

<style>
body {
    background:#121212;
    color:#fff;
}
.movie-poster {
    width:300px;
    height:450px;
    object-fit:cover;
    border-radius:10px;
}
iframe {
    border-radius:10px;
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

    <!-- Movie Info -->
    <div class="row g-4">
        <div class="col-md-4 text-center">
            <img src="<?php echo $movie['movie_poster']; ?>"
                 class="movie-poster"
                 onerror="this.src='elements/default-poster.png';">
        </div>

        <div class="col-md-8">
            <h2><?php echo htmlspecialchars($movie['movie_name']); ?></h2>
            <p><strong>Genres:</strong> <?php echo $movie['movie_genres']; ?></p>
            <p><strong>Release Year:</strong> <?php echo $movie['release_year']; ?></p>
            <p>
                <strong>IMDb:</strong>
                <img src="elements/logo/imdb.png" style="height:18px;">
                <?php echo $movie['imdb_rating']; ?>
            </p>
            <p><strong>Language:</strong> <?php echo $movie['language']; ?></p>
            <p><strong>Description:</strong><br>
                <?php echo nl2br(htmlspecialchars($movie['description'])); ?>
            </p>
        </div>
    </div>

    <!-- Player -->
    <div class="mt-5">
        <h4>Watch Now</h4>

        <!-- Cloud streaming via iframe -->
        <iframe
            src="<?php echo $movie['movie_file']; ?>"
            width="100%"
            height="500"
            allowfullscreen
            loading="lazy">
        </iframe>
    </div>

</div>

<script src="script.js"></script>
</body>
</html>
