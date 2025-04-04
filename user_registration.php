<?php
session_start();
$host = 'localhost';
$db = 'user_database';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// User Registration
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role']; // Admin or User
    $query = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
    if ($conn->query($query)) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $conn->error;
    }
}

// User Login
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user'] = $username;
            $_SESSION['role'] = $row['role'];
            header("Location: dashboard.php");
        } else {
            echo "Invalid credentials!";
        }
    } else {
        echo "User not found!";
    }
}

// User Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Authentication</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2 class="text-center">User Authentication</h2>
    <form method="post" class="mt-3">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select name="role" id="role" class="form-select">
                <option value="User">User</option>
                <option value="Admin">Admin</option>
            </select>
        </div>
        <button type="submit" name="register" class="btn btn-primary">Register</button>
        <button type="submit" name="login" class="btn btn-success">Login</button>
    </form>

    <hr>
    <h3 class="text-center">Admin Panel</h3>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'Admin'): ?>
        <div class="text-center">
            <a href="admin_panel.php" class="btn btn-warning">Go to Admin Panel</a>
        </div>
    <?php endif; ?>
</body>
</html>
