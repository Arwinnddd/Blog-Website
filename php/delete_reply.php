<?php
// Include database connection
include 'db_connect.php';

// Check if reply ID is set in GET request
if (isset($_GET['reply_id'])) {
    $replyId = $_GET['reply_id'];

    // Delete the reply from the database
    $deleteStmt = $conn->prepare("DELETE FROM comment_replies WHERE id = ?");
    $deleteStmt->bind_param("i", $replyId);

    if ($deleteStmt->execute()) {
        header("Location: adminDashboard.php");
        exit;
    } else {
        echo "Error deleting reply: " . $deleteStmt->error;
    }
} else {
    echo "No reply ID provided.";
    exit;
}
?>
