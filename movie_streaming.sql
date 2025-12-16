CREATE DATABASE movie_streaming;

USE movie_streaming;

-- USERS
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    dob DATE NOT NULL,
    avatar VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ADMIN USER
INSERT INTO users (username, email, dob, avatar, password, is_admin)
VALUES (
    'Admin',
    'admin@example.com',
    '2000-01-01',
    'avatar1.gif',
    '$2a$12$uo9eAnC0vWwtc2sfJKrYWu7wpUnetlPbHzylAtEW2zThJomC/fUpG',
    TRUE
);

-- MOVIES
CREATE TABLE movies (
    id SERIAL PRIMARY KEY,
    movie_name VARCHAR(255) NOT NULL,
    movie_poster TEXT NOT NULL,  -- URL
    movie_file TEXT NOT NULL,    -- URL
    movie_genres VARCHAR(255) NOT NULL,
    release_year INT NOT NULL,
    imdb_rating FLOAT NOT NULL,
    language VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- WATCH HISTORY
CREATE TABLE watch_history (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id) ON DELETE CASCADE,
    movie_id INT REFERENCES movies(id) ON DELETE CASCADE,
    watch_percentage INT DEFAULT 0,
    last_watched TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
