<?php
// Include database connection
include 'db_connect.php';

// Check if reply ID is set in GET request
if (isset($_GET['reply_id'])) {
    $replyId = $_GET['reply_id'];

    // Fetch the reply from the database
    $stmt = $conn->prepare("SELECT reply_content FROM comment_replies WHERE id = ?");
    $stmt->bind_param("i", $replyId);
    $stmt->execute();
    $result = $stmt->get_result();
    $reply = $result->fetch_assoc();

    if (!$reply) {
        echo "Reply not found.";
        exit;
    }

    // Handle reply update
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updated_reply'])) {
        $updatedReplyContent = $_POST['reply_content'];

        // Update the reply in the database
        $updateStmt = $conn->prepare("UPDATE comment_replies SET reply_content = ? WHERE id = ?");
        $updateStmt->bind_param("si", $updatedReplyContent, $replyId);

        if ($updateStmt->execute()) {
            header("Location: adminDashboard.php");
            exit;
        } else {
            echo "Error updating reply: " . $updateStmt->error;
        }
    }
} else {
    echo "No reply ID provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Reply</title>
</head>
<body>
    <h2>Update Reply</h2>
    <form method="POST">
        <textarea name="reply_content" class="form-control" rows="3"><?php echo htmlspecialchars($reply['reply_content']); ?></textarea>
        <button type="submit" name="updated_reply" class="btn btn-primary mt-2">Update Reply</button>
    </form>
</body>
</html>


