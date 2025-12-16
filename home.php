<?php
// home.php
session_start();
require 'db_connection.php';

// Protect page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Search
$search = '';
$params = [];

$sql = "
    SELECT
        id,
        movie_name,
        movie_poster,
        movie_genres,
        release_year,
        imdb_rating,
        language
    FROM movies
";

if (!empty($_GET['search'])) {
    $search = trim($_GET['search']);
    $sql .= " WHERE movie_name ILIKE :search OR movie_genres ILIKE :search ";
    $params['search'] = "%$search%";
}

$sql .= " ORDER BY upload_date DESC";

// Fetch movies
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$movies = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Home - Infinity</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="stylesheet" href="style.css">

<style>
body {
    background:#121212;
    color:#fff;
}
.movie-card img {
    height:300px;
    object-fit:cover;
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

<!-- Header -->
<nav class="navbar navbar-dark bg-dark px-3">
    <span class="navbar-brand">Infinity</span>

    <form method="GET" class="d-flex">
        <input class="form-control form-control-sm me-2"
               type="search"
               name="search"
               placeholder="Search movies..."
               value="<?php echo htmlspecialchars($search); ?>">
        <button class="btn btn-sm btn-primary">Search</button>
    </form>

    <div class="ms-auto d-flex gap-3">
        <a href="home.php" class="text-white text-decoration-none">Home</a>
        <a href="profile.php" class="text-white text-decoration-none">Profile</a>
        <a href="history.php" class="text-white text-decoration-none">History</a>
        <a href="about.php" class="text-white text-decoration-none">About</a>
        <a href="logout.php" class="text-danger text-decoration-none">Logout</a>
    </div>
</nav>

<!-- Movie Grid -->
<div class="container mt-4">
    <div class="row g-4">

        <?php if (count($movies) > 0): ?>
            <?php foreach ($movies as $m): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card bg-dark text-white movie-card h-100">

                        <a href="movie.php?id=<?php echo $m['id']; ?>">
                            <img src="<?php echo $m['movie_poster']; ?>"
                                 class="card-img-top"
                                 onerror="this.src='elements/default-poster.png';">
                        </a>

                        <div class="card-body text-center">
                            <h6><?php echo htmlspecialchars($m['movie_name']); ?></h6>
                            <small><?php echo $m['movie_genres']; ?></small><br>
                            <small><?php echo $m['release_year']; ?></small><br>
                            <img src="elements/logo/imdb.png" style="height:16px;">
                            <?php echo $m['imdb_rating']; ?>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <p>No movies found.</p>
            </div>
        <?php endif; ?>

    </div>
</div>

<script src="script.js"></script>
</body>
</html>
