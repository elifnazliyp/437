<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php'; // Database connection

// Step 1: Fetch RSS Feed
$rssUrl = "https://rss.app/feeds/v1.1/_XwwiGIQsUmb6Z8hc.json";
$response = file_get_contents($rssUrl);

if (!$response) {
    die("Failed to fetch the RSS feed. Please try again later.");
}

$articles = json_decode($response, true)['items'] ?? [];

// Step 2: Insert New Articles into the Database
foreach ($articles as $article) {
    $rss_id = isset($article['id']) ? $article['id'] : uniqid("rss_");
    $title = isset($article['title']) && !empty($article['title']) 
             ? mysqli_real_escape_string($conn, $article['title']) 
             : "No Title Available";

    $content_text = isset($article['content_text']) && !empty($article['content_text']) 
                    ? mysqli_real_escape_string($conn, $article['content_text']) 
                    : "No content available.";

    $image_url = isset($article['image']) && !empty($article['image']) 
                 ? mysqli_real_escape_string($conn, $article['image']) 
                 : null;

    $date_published = isset($article['date_published']) && !empty($article['date_published']) 
                      ? date('Y-m-d H:i:s', strtotime($article['date_published'])) 
                      : date('Y-m-d H:i:s');

    // Check for duplicates
    $check_query = "SELECT id FROM news_articles WHERE rss_id = '$rss_id'";
    $result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result) == 0) {
        $insert_query = "INSERT INTO news_articles (rss_id, title, content_text, image_url, date_published)
                         VALUES ('$rss_id', '$title', '$content_text', '$image_url', '$date_published')";
        mysqli_query($conn, $insert_query);
    }
}

// Step 3: Retrieve Articles for Display
$query = "SELECT * FROM news_articles WHERE is_deleted = 0 ORDER BY date_published DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>World News</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Header -->
    <header>
        <nav>
            <div class="logo">
                <a href="index.php">World News</a>
            </div>
            <!-- Add search bar -->
            <form class="search-bar" action="search.php" method="GET">
                <input type="text" name="q" placeholder="Search news...">
                <button type="submit">Search</button>
            </form>
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

    <!-- Main Content -->
    <main>
        <h1 class="page-title">Latest News</h1>
        <div class="news-grid">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="news-card">
                        <?php if (!empty($row['image_url'])): ?>
                            <div class="news-image">
                                <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="News Image" style="width: 100%; height: auto;">
                            </div>
                        <?php endif; ?>
                        <div class="news-content">
                            <h2 class="news-title">
                                <a href="news-detail.php?id=<?php echo urlencode($row['id']); ?>">
                                    <?php echo htmlspecialchars($row['title']); ?>
                                </a>
                            </h2>
                            <p class="news-excerpt">
                                <?php echo substr(htmlspecialchars($row['content_text']), 0, 100) . '...'; ?>
                            </p>
                            <p class="news-meta">
                                Published on: <?php echo date('F j, Y', strtotime($row['date_published'])); ?>
                            </p>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No articles available at the moment. Please check back later.</p>
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
