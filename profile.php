<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$siteName = "World News";
$currentYear = date("Y");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - <?php echo $siteName; ?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <a href="index.php"><?php echo $siteName; ?></a>
            </div>
            <div class="auth-links">
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </nav>
    </header>

    <main class="profile-container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p>This is your profile page. You can customize it as needed.</p>
    </main>

    <footer>
        <p>&copy; <?php echo $currentYear; ?> <?php echo $siteName; ?>. All rights reserved.</p>
    </footer>
</body>
</html>
