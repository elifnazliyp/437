<?php
session_start();
include 'db.php'; // Include the database connection

$siteName = "Global News Network";
$currentYear = date("Y");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = trim($_POST['password']);

    // Basic validation
    if (empty($username) || empty($password)) {
        $error = "All fields are required.";
    } else {
        // Check if the username exists
        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);

            // Verify password
            if (password_verify($password, $user['password'])) {
                // Start session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Redirect to home or dashboard
                header("Location: ../index.php");
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "User not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo $siteName; ?></title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <a href="../index.php"><?php echo $siteName; ?></a>
            </div>
        </nav>
    </header>

    <!-- Login Form -->
    <div class="login-container">
        <h2>Login</h2>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form class="login-form" action="" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="submit-btn">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>

    <footer>
        <p>&copy; <?php echo $currentYear; ?> <?php echo $siteName; ?>. All rights reserved.</p>
    </footer>
</body>
</html>
