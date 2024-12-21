<?php
// Start session and enable error reporting
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php'; // Database connection

// Sanitize and validate search query
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';

// Prevent empty search
if (empty($search_query)) {
    $error_message = "Please enter a search term.";
}

// Prepare search query with parameterized statement to prevent SQL injection
$search_query_param = "%{$search_query}%";
$search_sql = "SELECT * FROM news_articles 
               WHERE is_deleted = 0 AND 
               (title LIKE ? OR content_text LIKE ?) 
               ORDER BY date_published DESC";

$stmt = mysqli_prepare($conn, $search_sql);
mysqli_stmt_bind_param($stmt, "ss", $search_query_param, $search_query_param);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - World News</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Header (same as index.php) -->
    <header>
        <nav>
            <div class="logo">
                <a href="index.php">World News</a>
            </div>
            <!-- Search bar -->
            <form class="search-bar" action="search.php" method="GET">
                <input type="text" id="searchInput" name="q" placeholder="Search news..." value="<?php echo htmlspecialchars($search_query); ?>">
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
        <h1 class="page-title">Search Results</h1>
        
        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

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
                <p>No articles found matching your search term.</p>
            <?php endif; ?>
        </div>

        <!-- Vulnerable DOM-based XSS Section -->
        <div id="vulnSection">
            <h2>Search Query Details</h2>
            <div id="searchQueryDisplay"></div>
        </div>
    </main>

    <!-- VULNERABLE: JavaScript for DOM-based XSS -->
    <script>
        // DOM-based XSS Vulnerability
        document.addEventListener('DOMContentLoaded', function() {
            // Get the search input from the URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const searchQuery = urlParams.get('q');

            // VULNERABILITY: Directly inserting user input into the DOM without sanitization
            if (searchQuery) {
                const searchQueryDisplay = document.getElementById('searchQueryDisplay');
                
                // Vulnerable line: directly inserting HTML
                searchQueryDisplay.innerHTML = 'You searched for: ' + searchQuery;
            }
        });

        // Additional trick to demonstrate vulnerability
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('input', function() {
                console.log('Current search input: ' + this.value);
            });
        });
    </script>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            &copy; <?php echo date('Y'); ?> World News. All Rights Reserved.
        </div>
    </footer>
</body>
</html>