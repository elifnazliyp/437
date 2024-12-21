<?php
include 'db.php'; // Include database connection

$adminName = "John Doe";
$currentYear = date("Y");

// Delete article logic (SQL Injection vulnerable)
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id']; // UNSAFE input
    $query = "DELETE FROM news_articles WHERE id = $delete_id";
    mysqli_query($conn, $query);
    echo "<p style='color: red; text-align:center;'>Article with ID $delete_id has been deleted.</p>";
}

// Fetch all news articles
$query = "SELECT * FROM news_articles";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Global News Network</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header class="admin-header">
        <nav class="admin-nav">
            <span class="admin-user">Admin: <?php echo $adminName; ?></span>
            <a href="index.php" class="view-site-btn">View Site</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </nav>
    </header>

    <main>
        <h2 style="text-align: center; margin: 2rem 0; color: #1a237e;">Admin Dashboard</h2>

        <div class="news-detail-container">
            <h3 style="margin-bottom: 1rem; color: #283593;">Manage News Articles</h3>
            <table border="1" width="100%" style="border-collapse: collapse; text-align: left;">
                <tr style="background-color: #283593; color: white;">
                    <th style="padding: 0.75rem;">ID</th>
                    <th style="padding: 0.75rem;">Title</th>
                    <th style="padding: 0.75rem;">Date Published</th>
                    <th style="padding: 0.75rem; text-align: center;">Actions</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr style="background-color: #f9f9f9; border-bottom: 1px solid #ddd;">
                    <td style="padding: 0.75rem;"><?php echo $row['id']; ?></td>
                    <td style="padding: 0.75rem;"><?php echo htmlspecialchars($row['title']); ?></td>
                    <td style="padding: 0.75rem;"><?php echo $row['date_published']; ?></td>
                    <td style="text-align: center; padding: 0.75rem;">
                        <a href="?delete_id=<?php echo $row['id']; ?>" class="admin-btn" onclick="return confirm('Are you sure?');">Delete</a>
                        <a href="edit_article.php?id=<?php echo $row['id']; ?>" class="view-site-btn">Edit</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </main>
</body>
</html>
