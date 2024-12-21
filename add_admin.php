<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php';

// VULNERABLE: No authorization check!
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Anyone can add admins!
    $query = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', 'admin')";
    if(mysqli_query($conn, $query)) {
        $success_message = "Admin account created successfully! Username: " . htmlspecialchars($username);
    }
    else {
        $error_message = "Error creating admin account: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Admin - Global News Network</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Matching website style */
        .admin-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .admin-section {
            margin-top: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .submit-btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
        }
        .submit-btn:hover {
            background-color: #0056b3;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <a href="index.php">Global News Network</a>
            </div>
        </nav>
    </header>

    <main>
        <div class="admin-container">
            <h1>Add New Admin</h1>
            <?php if (isset($success_message)): ?>
                <div class="success-message">
                    <?php echo $success_message; ?>
                    <p>You can now login with these credentials at the login page.</p>
                </div>
            <?php endif; ?>
            
            <div class="admin-section">
                <form method="POST" action="add_admin.php">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="submit-btn">Create Admin Account</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>