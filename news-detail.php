<?php
session_start();
include 'db.php'; // Include database connection

// 1. Fetch News Article Based on ID
$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid article ID.");
}

// Fetch article from database
$query = "SELECT * FROM news_articles WHERE id = '$id' AND is_deleted = 0";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Article not found.");
}

$article = mysqli_fetch_assoc($result);

// 2. Handle Comment Submission (Only for Logged-in Users)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $comment = $_POST['comment'] ?? '';

    if (!empty($comment)) {
        // Vulnerable SQL query (SQL Injection)
        $insert_query = "INSERT INTO comments (news_id, username, comment) 
                         VALUES ('$id', '$username', '$comment')";
        if (!mysqli_query($conn, $insert_query)) {
            echo "<p style='color: red;'>Error saving comment: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<p style='color: red;'>Please enter a comment.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Header -->
    <header>
        <nav>
            <div class="logo">
                <a href="index.php">World News</a>
            </div>
            <div class="auth-links">
                <?php if (isset($_SESSION['username'])): ?>
                    <a href="profile.php" class="profile-btn">Profile</a>
                    <a href="logout.php" class="logout-btn">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="login-btn">Login</a>
                    <a href="register.php" class="register-btn">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <!-- News Detail -->
    <main class="news-detail-container">
        <article class="news-detail">
            <h1 class="news-detail-title"><?php echo htmlspecialchars($article['title']); ?></h1>
            <p class="news-meta">
                Published on: <?php echo date('F j, Y', strtotime($article['date_published'])); ?>
            </p>
            <?php if (!empty($article['image_url'])): ?>
                <div class="news-detail-image">
                    <img src="<?php echo htmlspecialchars($article['image_url']); ?>" alt="Article Image">
                </div>
            <?php endif; ?>
            <div class="news-detail-content">
                <p><?php echo nl2br(htmlspecialchars($article['content_text'])); ?></p>
            </div>
        </article>

        <!-- Comments Section -->
        <div class="comments-section">
            <h3>Comments</h3>
            <?php
            // Fetch and display comments
            $comments_query = "SELECT * FROM comments WHERE news_id = '$id' ORDER BY created_at DESC";
            $comments_result = mysqli_query($conn, $comments_query);

            if (mysqli_num_rows($comments_result) > 0) {
                while ($row = mysqli_fetch_assoc($comments_result)) {
                    echo "<div class='comment'>
                            <strong>" . htmlspecialchars($row['username']) . ":</strong>
                            <p>" . nl2br(htmlspecialchars($row['comment'])) . "</p>
                            <small>Posted on: " . $row['created_at'] . "</small>
                          </div>";
                }
            } else {
                echo "<p>No comments yet. Be the first to comment!</p>";
            }
            ?>

            <!-- Comment Form -->
            <?php if (isset($_SESSION['username'])): ?>
                <h3>Leave a Comment</h3>
                <form method="POST" action="" class="comment-form">
                    <label for="comment">Comment:</label>
                    <textarea id="comment" name="comment" rows="4" required></textarea>
                    <button type="submit">Submit</button>
                </form>
            <?php else: ?>
                <p>You must <a href="login.php">log in</a> to leave a comment.</p>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            &copy; <?php echo date('Y'); ?> World News. All Rights Reserved.
        </div>
    </footer>
</body>
</html>