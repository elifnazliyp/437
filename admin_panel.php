<?php
session_start();
include 'db.php';

// VULNERABLE: No check for admin role!
// Should check $_SESSION['role'] === 'admin' but doesn't
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
    <header>
        <nav>
            <div class="logo">
                <a href="index.php">Global News Network</a>
            </div>
            <div class="auth-links">
                <?php if (isset($_SESSION['username'])): ?>
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="logout.php" class="logout-btn">Logout</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main>
        <div class="admin-container">
            <h1>Admin Control Panel</h1>
            
            <!-- Article Management -->
            <section class="admin-section">
                <h2>Article Management</h2>
                <form action="delete_article.php" method="POST">
                    <select name="article_id">
                        <?php
                        $query = "SELECT id, title FROM news_articles WHERE is_deleted = 0";
                        $result = mysqli_query($conn, $query);
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['title']) . "</option>";
                        }
                        ?>
                    </select>
                    <button type="submit" name="delete">Delete Article</button>
                </form>
            </section>

            <!-- User Management -->
            <section class="admin-section">
                <h2>User Management</h2>
                <form action="add_admin.php" method="POST">
                    <input type="text" name="username" placeholder="Username">
                    <input type="password" name="password" placeholder="Password">
                    <button type="submit">Add New Admin</button>
                </form>
            </section>
        </div>
    </main>
</body>
</html>