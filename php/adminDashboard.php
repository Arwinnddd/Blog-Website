<?php
ini_set("session.cookie_httponly", 1);
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: userLogIn.php');
    exit;
}

include 'db_connect.php';

$query = "SELECT posts.id AS post_id, posts.title, posts.content, posts.created_at, users.username AS post_author,
                 comments.comment AS comment_content, comments.created_at AS comment_date, comment_users.username AS comment_author,
                 comments.id AS comment_id
          FROM posts
          LEFT JOIN users ON posts.user_id = users.id
          LEFT JOIN comments ON comments.post_id = posts.id
          LEFT JOIN users AS comment_users ON comments.user_id = comment_users.id
          ORDER BY posts.created_at DESC, comments.created_at ASC";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

$newsfeed = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $postId = $row['post_id'];
        if (!isset($newsfeed[$postId])) {
            $newsfeed[$postId] = [
                'title' => $row['title'],
                'content' => $row['content'],
                'created_at' => $row['created_at'],
                'author' => $row['post_author'],
                'comments' => []
            ];
        }
        if ($row['comment_content']) {
            $newsfeed[$postId]['comments'][] = [
                'content' => $row['comment_content'],
                'created_at' => $row['comment_date'],
                'author' => $row['comment_author'],
                'comment_id' => $row['comment_id']
            ];
        }
    }
}


// Handle new comment submission (admin role)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_comment'])) {
    $postId = $_POST['post_id'];
    $commentContent = $_POST['comment_content'];

    // Check for empty comment content
    if (empty($commentContent)) {
        echo "Error: Comment content cannot be empty.";
        exit;
    }

    // Insert new comment into the database
    $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $postId, $_SESSION['user_id'], $commentContent);

    if ($stmt->execute()) {
        header("Location: adminDashboard.php"); // Redirect to the dashboard after comment is added
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Handle comment reply submission (admin role)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_comment'])) {
    $commentId = $_POST['comment_id'];
    $replyContent = $_POST['reply_content'];

    // Check if the reply content is empty
    if (empty($replyContent)) {
        echo "Error: Reply content cannot be empty.";
        exit;
    }

    // Insert reply into the database
    $stmt = $conn->prepare("INSERT INTO comment_replies (comment_id, user_id, reply_content) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $commentId, $_SESSION['user_id'], $replyContent);

    if ($stmt->execute()) {
        header("Location: adminDashboard.php"); // Redirect to the dashboard after reply is added
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagsulat - Admin Dashboard</title>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-vtXRMe3mGCbOeY7l30aIg8H9p3GdeSe4IFlP6G8JMa7o7lXvnz3GFKzPxzJdPfGK" crossorigin="anonymous"></script>

    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css"> -->
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

        .form-container {
            background-color: rgba(0, 0, 0, 0.8);
            border-radius: 10px;
            padding: 30px;
            margin: 10px;
            margin-top: 30px;

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

        .nav-tabs {
            border-bottom: 1px solid black; /* Change the bottom border to dark grey */
        }

        .nav-tabs .nav-link {
            color: black;
        }

        .nav-tabs .nav-link.active {
            background-color:  rgba(0, 0, 0, 0.7);
            color: white;

            border-color: 0.5px solid black; 
        }

        .table-bordered {
            border: 0.5px solid #343a40;
        }
        .table-bordered th,
        .table-bordered td {
            border: 0.5px solid #343a40;
        } 

    </style>
</head>
<body>

   <nav class="navbar fixed-top">
        <div class="container-fluid d-flex justify-content-between align-items-center">
             
            <a class="navbar-brand text-white" href="#">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;p a g s u l a t</a>
        
            <div class="nav-links d-flex align-items-center">
                <a class="nav-link " href="#"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Home &nbsp;</b></a>
                <a class="nav-link " href="post_dashboard.php"><b>&nbsp;Post Dashboard &nbsp;</b></a>
                <a class="nav-link " href="comments_dashboard.php"><b>&nbsp;Comment Dashboard &nbsp;</b></a>
                <a class="nav-link " href="accountsManagement.php"><b>&nbsp;Account Management &nbsp;</b></a>
                <span class="nav-separator mx-2 text-white">|</span>
                <a class="nav-link " href="logout.php"><b>&nbsp;&nbsp;&nbsp;&nbsp;Log Out &nbsp;</b></a>

            </div>
        </div>
    </nav>

<br><br><br><br>

<div class="container mt-4">
        <h2>Welcome, Admin!</h2>
        <p>Manage posts, comments, and user accounts using the options in the navigation bar.</p>


        <!-- Display success or error message -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['message']; ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error']; ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <h2>Newsfeed</h2>
        <?php if (!empty($newsfeed)): ?>
            <?php foreach ($newsfeed as $postId => $post): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                        <p class="text-muted">Posted by <?php echo htmlspecialchars($post['author']); ?> on <?php echo htmlspecialchars($post['created_at']); ?></p>
                    </div>
                    <div class="card-footer">
                        <h6>Comments</h6>
                        <?php if (!empty($post['comments'])): ?>
                            <?php foreach ($post['comments'] as $comment): ?>
                                <div class="mb-2">
                                    <strong><?php echo htmlspecialchars($comment['author']); ?>:</strong>
                                    <span><?php echo htmlspecialchars($comment['content']); ?></span>
                                    <br>
                                    <small class="text-muted">Posted on <?php echo htmlspecialchars($comment['created_at']); ?></small>
                                

                                    <!-- Reply form -->
                                    <form method="post">
                                            <input type="hidden" name="comment_id" value="<?php echo $comment['comment_id']; ?>">
                                            <textarea name="reply_content" class="form-control" rows="3" placeholder="Reply to this comment..."></textarea>
                                            <button type="submit" name="reply_comment" class="btn btn-dark mt-2">Reply</button>
                                        </form> 

                                        <!-- Display replies if any -->
                                        <?php 
                                        $replyQuery = "SELECT reply_content, created_at, users.username AS reply_author
                                                    FROM comment_replies
                                                    LEFT JOIN users ON comment_replies.user_id = users.id
                                                    WHERE comment_replies.comment_id = ?";
                                        $replyStmt = $conn->prepare($replyQuery);
                                        $replyStmt->bind_param("i", $comment['comment_id']);
                                        $replyStmt->execute();
                                        $replyResult = $replyStmt->get_result();

                                        if ($replyResult->num_rows > 0): ?>
                                            <div class="mt-2">
                                                <?php while ($reply = $replyResult->fetch_assoc()): ?>
                                                    <div class="mb-2">
                                                        <strong><?php echo htmlspecialchars($reply['reply_author']); ?>:</strong>
                                                        <span><?php echo htmlspecialchars($reply['reply_content']); ?></span>
                                                        <br>
                                                        <small class="text-muted">Replied on <?php echo htmlspecialchars($reply['created_at']); ?></small>
                                                    </div>
                                                <?php endwhile; ?>
                                            </div>
                                        <?php endif; ?>
                                </div> 
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No comments yet. Be the first to comment!</p>
                        <?php endif; ?>
                        <!-- New comment form -->
                        <form method="post">
                            <input type="hidden" name="post_id" value="<?php echo $postId; ?>">
                            <textarea name="comment_content" class="form-control" rows="3" placeholder="Add a new comment..."></textarea>
                            <button type="submit" name="new_comment" class="btn btn-dark mt-2">Add Comment</button>
                        </form>



                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">No posts available. Start sharing content!</p>
        <?php endif; ?>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
