<?php

session_start();
include 'db_connect.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_password = $_POST['password'];
    $user_id = $_POST['user_id'];
    $admin_username = $_SESSION['username'];

    // 🔹 Validate user_id (Prevent Path Traversal)
    if (!ctype_digit($user_id)) {
        echo json_encode(["status" => "error", "message" => "Invalid user ID."]);
        exit;
    }
    
    // 🔹 Verify admin password securely
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ? AND role = 'admin'");
    $stmt->bind_param("s", $admin_username);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin_data = $result->fetch_assoc();

    if ($admin_data && password_verify($admin_password, $admin_data['password'])) {
        // 🔹 Begin transaction for safe deletion
        $conn->begin_transaction();

        try {
            // 🔹 Delete related comments first
            $stmt = $conn->prepare("DELETE FROM comments WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            // 🔹 Delete user account
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            // 🔹 Commit transaction if all good
            $conn->commit();
            echo json_encode("Account successfully deleted.");
        } catch (Exception $e) {
            // 🔹 Rollback if an error occurs
            $conn->rollback();
            echo json_encode("Error deleting account.");
        }
    } else {
        echo json_encode("Incorrect password.");
    }
} else {
    echo json_encode("Invalid request.");
}

// session_start();
// include 'db_connect.php';

// if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
//     echo "Unauthorized access.";
//     exit;
// }

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $admin_password = $_POST['password'];
//     $user_id = $_POST['user_id'];
//     $admin_username = $_SESSION['username'];

//     // Verify admin password
//     $stmt = $conn->prepare("SELECT password FROM users WHERE username = ? AND role = 'admin'");
//     $stmt->bind_param("s", $admin_username);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     $admin_data = $result->fetch_assoc();

//     if ($admin_data && password_verify($admin_password, $admin_data['password'])) {
//         // Delete user and related comments
//         $stmt = $conn->prepare("DELETE FROM comments WHERE user_id = ?");
//         $stmt->bind_param("i", $user_id);
//         $stmt->execute();

//         $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
//         $stmt->bind_param("i", $user_id);
//         if ($stmt->execute()) {
//             echo "Account successfully deleted.";
//         } else {
//             echo "Error deleting account.";
//         }
//     } else {
//         echo "Incorrect password.";
//     }
// } else {
//     echo "Invalid request.";
// }
?>

