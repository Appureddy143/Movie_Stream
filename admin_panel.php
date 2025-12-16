<?php
// admin_panel.php
session_start();
require 'db_connection.php';

// Protect page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message = "";

// ADD MOVIE (URL-based)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add') {

    $movie_name   = trim($_POST['movie_name']);
    $movie_poster = trim($_POST['movie_poster']); // image URL
    $movie_file   = trim($_POST['movie_file']);   // video URL
    $movie_genres = implode(", ", $_POST['movie_genres']);
    $release_year = (int) $_POST['release_year'];
    $imdb_rating  = (float) $_POST['imdb_rating'];
    $language     = trim($_POST['language']);
    $description  = trim($_POST['description']);

    if (
        empty($movie_name) || empty($movie_poster) || empty($movie_file)
    ) {
        $message = "All fields are required.";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO movies (
                movie_name,
                movie_poster,
                movie_file,
                movie_genres,
                release_year,
                imdb_rating,
                language,
                description,
                upload_date
            ) VALUES (
                :name, :poster, :file, :genres,
                :year, :rating, :lang, :descr, NOW()
            )
        ");

        $stmt->execute([
            'name'   => $movie_name,
            'poster' => $movie_poster,
            'file'   => $movie_file,
            'genres' => $movie_genres,
            'year'   => $release_year,
            'rating' => $imdb_rating,
            'lang'   => $language,
            'descr'  => $description
        ]);

        $message = "Movie added successfully!";
    }
}

// DELETE MOVIE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete') {
    $movie_id = (int) $_POST['movie_id'];

    $stmt = $pdo->prepare("DELETE FROM movies WHERE id = :id");
    $stmt->execute(['id' => $movie_id]);
}

// FETCH MOVIES
$movies = $pdo->query("
    SELECT * FROM movies
    ORDER BY upload_date DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Panel - Infinity</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="stylesheet" href="style.css">

<style>
body { background:#121212; color:#fff; }
.movie-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
    gap: 20px;
}
.movie-card {
    background:#1e1e1e;
    padding:15px;
    border-radius:10px;
    text-align:center;
}
.movie-card img {
    width:100%;
    height:300px;
    object-fit:cover;
    border-radius:8px;
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

    <h2>Admin Panel</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>

    <!-- ADD MOVIE FORM -->
    <form method="POST" class="mb-5">
        <input type="hidden" name="action" value="add">

        <div class="mb-3">
            <label>Movie Name</label>
            <input type="text" name="movie_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Poster Image URL</label>
            <input type="url" name="movie_poster" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Movie Video URL</label>
            <input type="url" name="movie_file" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Genres</label>
            <select name="movie_genres[]" class="form-control" multiple required>
                <option>Action</option>
                <option>Comedy</option>
                <option>Drama</option>
                <option>Horror</option>
                <option>Thriller</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Release Year</label>
            <input type="number" name="release_year" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>IMDb Rating</label>
            <input type="text" name="imdb_rating" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Language</label>
            <input type="text" name="language" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3" required></textarea>
        </div>

        <button class="btn btn-success">Add Movie</button>
    </form>

    <!-- MOVIE LIST -->
    <h3>Uploaded Movies</h3>

    <div class="movie-grid">
        <?php foreach ($movies as $m): ?>
            <div class="movie-card">
                <img src="<?php echo $m['movie_poster']; ?>"
                     onerror="this.src='elements/default-poster.png';">
                <h6 class="mt-2"><?php echo htmlspecialchars($m['movie_name']); ?></h6>
                <p><?php echo $m['movie_genres']; ?></p>

                <form method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="movie_id" value="<?php echo $m['id']; ?>">
                    <button class="btn btn-danger btn-sm">Delete</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<script src="script.js"></script>
</body>
</html>
