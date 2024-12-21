<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database credentials
$host = "localhost";
$user = "root";         // Replace with your MySQL username
$password = "";         // Replace with your MySQL password
$database = "news_website";

// Step 1: Connect to MySQL without database
$conn = mysqli_connect($host, $user, $password);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Step 2: Create the database if not exists
$db_query = "CREATE DATABASE IF NOT EXISTS $database";
if (!mysqli_query($conn, $db_query)) {
    die("Error creating database: " . mysqli_error($conn));
}

// Select the database
mysqli_select_db($conn, $database);

// Step 3: Create `news_articles` table (to store news articles)
$news_articles_table = "
CREATE TABLE IF NOT EXISTS news_articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rss_id VARCHAR(255) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    content_text TEXT NOT NULL,
    image_url VARCHAR(255) DEFAULT NULL,
    date_published DATETIME NOT NULL,
    is_deleted TINYINT(1) DEFAULT 0
)";
if (!mysqli_query($conn, $news_articles_table)) {
    die("Error creating `news_articles` table: " . mysqli_error($conn));
}

// Step 4: Create `users` table (to store registered users)
$users_table = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, -- Hashed password
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if (!mysqli_query($conn, $users_table)) {
    die("Error creating `users` table: " . mysqli_error($conn));
}

// Step 5: Create `comments` table (to store user comments)
$comments_table = "
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    news_id INT NOT NULL, -- Foreign key referencing news_articles
    username VARCHAR(50) NOT NULL, -- Commenter's name
    comment TEXT NOT NULL, -- Comment text
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (news_id) REFERENCES news_articles(id) ON DELETE CASCADE
)";
if (!mysqli_query($conn, $comments_table)) {
    die("Error creating `comments` table: " . mysqli_error($conn));
}

// Step 6: Confirmation
// Uncomment this line for debugging to confirm the tables are created
// echo "Database and tables initialized successfully.";
?>