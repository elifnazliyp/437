<?php
/**
 * Article Deletion System
 * Demonstrates CWE-862: Missing Authorization vulnerability
 * 
 * VULNERABILITY NOTE:
 * This code intentionally lacks authorization checks to demonstrate
 * the Missing Authorization vulnerability. Any user can delete any article
 * regardless of their permissions or authentication status.
 */

session_start();
include 'db.php';

// Show available articles
$query = "SELECT id, title FROM news_articles WHERE is_deleted = 0";
$result = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Article Management System</title>
</head>
<body>
    <h2>Article Management System</h2>

    <!-- Article Deletion Form -->
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <select name="article_id">
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <option value="<?php echo $row['id']; ?>">
                    <?php echo htmlspecialchars($row['title']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Delete Article</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['article_id'])) {
            $article_id = mysqli_real_escape_string($conn, $_POST['article_id']);
            
            // VULNERABLE: No authorization check!
            // Any user can delete any article regardless of their role or authentication status
            $query = "UPDATE news_articles SET is_deleted = 1 WHERE id = '$article_id'";
            if (mysqli_query($conn, $query)) {
                echo "<p>Article deleted successfully</p>";
            } else {
                echo "<p>Error deleting article: " . mysqli_error($conn) . "</p>";
            }
        }
    }
    ?>
</body>
</html>