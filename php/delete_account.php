<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    echo "Unauthorized access.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_password = $_POST['password'];
    $user_id = $_POST['user_id'];
    $admin_username = $_SESSION['username'];

    // Verify admin password
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ? AND role = 'admin'");
    $stmt->bind_param("s", $admin_username);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin_data = $result->fetch_assoc();

    if ($admin_data && password_verify($admin_password, $admin_data['password'])) {
        // Delete user and related comments
        $stmt = $conn->prepare("DELETE FROM comments WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            echo "Account successfully deleted.";
        } else {
            echo "Error deleting account.";
        }
    } else {
        echo "Incorrect password.";
    }
} else {
    echo "Invalid request.";
}
?>

