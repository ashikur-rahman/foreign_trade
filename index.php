<?php
session_start();
include 'db_connect.php';


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
    <style>
        .form-container {
            max-width: 400px;
            margin: auto;
            margin-top: 100px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            background-color: #f8f9fa;
        }
    </style>
</head>
<body class="container d-flex flex-column align-items-center justify-content-center vh-100">
    <h1 class="text-center">Foreign Trade Management System</h1>
    <div class="form-container">
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
                    <!-- <option value="Super">User</option> -->
                    <option value="Admin">Admin</option>
                </select>
            </div>
            <button type="submit" name="login" class="btn btn-success w-100">Login</button>
            <button type="submit" name="register" class="btn btn-primary w-100 mt-2">Register</button>
            
        </form>
    </div>
</body>
</html>
