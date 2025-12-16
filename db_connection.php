<?php
patch-1
// db_connection.php (Neon PostgreSQL + Render compatible)

$dsn = getenv('DATABASE_URL');
=======
// db_connection.php (Neon PostgreSQL)

$dsn = getenv('psql 'postgresql://neondb_owner:npg_nfz4ISgq9BcR@ep-odd-meadow-adrtd94e-pooler.c-2.us-east-1.aws.neon.tech/neondb?sslmode=require&channel_binding=require'');
 main

try {
    $pdo = new PDO($dsn, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die("Database connection failed");
}
?>
