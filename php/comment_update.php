<?php
// Include database connection
include 'db_connect.php';

// Check if comment ID is set in GET request
if (isset($_GET['comment_id'])) {
    $commentId = $_GET['comment_id'];

    // Fetch the comment from the database
    $stmt = $conn->prepare("SELECT comment FROM comments WHERE id = ?");
    $stmt->bind_param("i", $commentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $comment = $result->fetch_assoc();

    if (!$comment) {
        echo "Comment not found.";
        exit;
    }

    // Handle comment update
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updated_comment'])) {
        $updatedCommentContent = $_POST['comment_content'];

        // Update the comment in the database
        $updateStmt = $conn->prepare("UPDATE comments SET comment = ? WHERE id = ?");
        $updateStmt->bind_param("si", $updatedCommentContent, $commentId);

        if ($updateStmt->execute()) {
            header("Location: adminDdashboard.php");
            exit;
        } else {
            echo "Error updating comment: " . $updateStmt->error;
        }
    }
} else {
    echo "No comment ID provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Comment</title>
    <link rel="icon" type="image/x-icon" href="images/logo.ico">
</head>
<body>
    <h2>Update Comment</h2>
    <form method="POST">
        <textarea name="comment_content" class="form-control" rows="3"><?php echo htmlspecialchars($comment['comment']); ?></textarea>
        <button type="submit" name="updated_comment" class="btn btn-primary mt-2">Update Comment</button>
    </form>
</body>
</html>
