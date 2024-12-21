<?php
session_start();
include 'db.php'; // Include the database connection

$siteName = "Global News Network";
$currentYear = date("Y");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data and sanitize
    $fullname = mysqli_real_escape_string($conn, trim($_POST['fullname']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Basic validation
    if (empty($fullname) || empty($email) || empty($username) || empty($password)) {
        $error = "All fields are required.";
    } else {
        // Check if email or username already exists
        $check_query = "SELECT * FROM users WHERE email = '$email' OR username = '$username'";
        $result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($result) > 0) {
            $error = "Email or username already exists.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Insert user into the database
            $insert_query = "INSERT INTO users (fullname, email, username, password) 
                             VALUES ('$fullname', '$email', '$username', '$hashed_password')";

            if (mysqli_query($conn, $insert_query)) {
                $success = "Registration successful. You can now log in.";
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?php echo $siteName; ?></title>
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

    <!-- Registration Form -->
    <div class="registration-container">
        <h2>Create an Account</h2>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php elseif (isset($success)): ?>
            <p style="color: green;"><?php echo $success; ?></p>
        <?php endif; ?>
        <form class="registration-form" action="" method="POST">
            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input type="text" id="fullname" name="fullname" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="submit-btn">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>

    <footer>
        <p>&copy; <?php echo $currentYear; ?> <?php echo $siteName; ?>. All rights reserved.</p>
    </footer>
</body>
</html>
