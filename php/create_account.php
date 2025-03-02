<?php
session_start();
include 'db_connect.php';

// Ensure the user is logged in and has 'admin' role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: userLogin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $role);

    if ($stmt->execute()) {
        header('Location: accountsManagement.php');
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Account</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="images/logo.ico">

    <style>
        
        *{
            font-family: 'Segoe UI', Poppins, Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: url(images/background.jpg) no-repeat center center fixed;
            background-size: cover;
            color: black;
        }
        .navbar {
            background-color: rgba(0, 0, 0, 0.7); 
            backdrop-filter: blur(10px); 
            -webkit-backdrop-filter: blur(10px);
            padding: 10px 20px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
        }
        .navbar-brand {
            font-size: 1.8rem;
            font-weight: bold;
        }
        .main-content {
            margin-top: 150px;
            text-align: center;
            color: black;
        }
        .main-content h1 {
            font-size: 3.5rem;
            font-weight: bold;
        }
        .main-content h3 {
            font-size: 1.5rem;
            font-weight: 300;
            
        }
        .btn-custom {
            background-color: black;
            border: none;
            padding: 10px 20px;
            font-size: 1.2rem;
            font-weight: bold;
            color: white;
            border-radius: 25px;
            margin-top: 20px;
        }
        .btn-custom:hover {
            background-color: #218838;
        }

        .nav-links .nav-link {
            margin-right: 15px;
            font-size: 1rem;
            text-decoration: none;
            color: white;
        }

        .nav-links .nav-icon {
            font-size: 1.2rem;
            margin-right: 10px;
            transition: all 0.3s ease;

        }

        .nav-separator {
            font-size: 1.2rem;
            color: white;
        }

        .h-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .h-right a:first-child {
            text-decoration: none;
            color: white;
            font-size: 1rem;
            margin-right: 10px;
        }

        .h-right a {
            text-decoration: none;
            font-size: 1.2rem;
            color: white;
            transition: all 0.3s ease;
        }

        .h-right a:hover {
            color: #a5ab90fe;
            transform: translateY(-3px);
            color: #a5ab90fe;
        }

        .nav-links .nav-link:hover {
            transition: all 0.4s ease;
            color: #a5ab90fe;

        }


    </style>

</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Create New Account</h2>
    <form method="POST" action="create_account.php">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select class="form-control" id="role" name="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Create Account</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>