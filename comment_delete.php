<?php
session_start();
include 'db_connect.php';

// Check if comment_id is provided in the URL
if (!isset($_GET['comment_id']) || empty($_GET['comment_id'])) {
    die("Invalid request.");
}

$comment_id = intval($_GET['comment_id']); // Ensure it's an integer

// Prepare SQL statement to delete the comment
$stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
$stmt->bind_param("i", $comment_id);

if ($stmt->execute()) {
    $_SESSION['message'] = "Comment deleted successfully.";
} else {
    $_SESSION['error'] = "Failed to delete comment.";
}

$stmt->close();
$conn->close();

// Redirect back to the comment dashboard
header("Location: comments_dashboard.php");
exit();
?>
