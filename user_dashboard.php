<?php
session_start();  // Start the session

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));  // Generate a new CSRF token
}

// Check if the user is logged in
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header('Location: userLogin.php');
    exit;
}

// Define session timeout duration (30 seconds for testing)
$session_timeout = 30;

// Check for session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $session_timeout) {
    session_unset();
    session_destroy();
    header('Location: logout.php');
    exit;
}

// Update last activity timestamp
$_SESSION['last_activity'] = time();

// Security Headers
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'");
header("Referrer-Policy: no-referrer");

// Database connection
include 'db_connect.php';

// Fetch posts and associated comments
$query = "SELECT posts.id AS post_id, posts.title, posts.content, posts.created_at, users.username AS post_author,
          comments.comment AS comment_content, comments.created_at AS comment_date, comment_users.username AS comment_author, comments.id AS comment_id
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
            'id' => $row['comment_id'],
            'content' => $row['comment_content'],
            'created_at' => $row['comment_date'],
            'author' => $row['comment_author']
        ];
    }
}

// Handle new comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_comment'])) {
    // CSRF Token Validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF validation failed. Please refresh and try again.");
    }

    // Proceed with inserting the comment if CSRF token is valid
    $comment = $_POST['comment'];
    $postId = $_POST['post_id'];
    $userId = $_SESSION['user_id'];

    // Prepare and execute the comment insertion
    $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $postId, $userId, $comment);
    if ($stmt->execute()) {
        // Regenerate CSRF Token after successful request
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        header("Location: user_dashboard.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_comment_id'])) {
    // CSRF Token Validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF validation failed. Please refresh and try again.");
    }

    // Proceed with inserting the reply if CSRF token is valid
    $replyContent = $_POST['reply_content'];
    $commentId = $_POST['reply_comment_id'];
    $userId = $_SESSION['user_id'];

    // Prepare and execute the reply insertion
    $stmt = $conn->prepare("INSERT INTO comment_replies (comment_id, user_id, reply_content, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $commentId, $userId, $replyContent);
    if ($stmt->execute()) {
        // Regenerate CSRF Token after successful request
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        header("Location: user_dashboard.php");
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
    <title>Pagsulat - Welcome</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css">
    <link rel="stylesheet" href ="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href ="style.css">
    

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

        .post-container{
            background-color: rgba(0, 0, 0, 0.8);
            border-radius: 10px;
            padding: 30px;
            margin: 10px;
            margin-top: 30px;
        }

        .btn{
            display: inline-block;
            padding: 10px 30px;
            background: #83896ffe;
            color: #fffffffe;
            font-size: 15px;
            font-weight: 600;
            border: 2px solid #83896ffe;
            border-radius: 30px;
            transition: all .50s ease;
            cursor: pointer;
        }

        .btn:hover{
            transform: translatey(-2px);
            letter-spacing: 1px;
            background: transparent;
            border: 2px solid #83896ffe;

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
   <nav class="navbar fixed-top">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <!-- Logo Section -->
            <a class="navbar-brand text-white" href="#">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;p a g s u l a t</a>

            <!-- Navigation Links -->
            <div class="nav-links d-flex align-items-center ">
                <a class="nav-link text-white" href="#" ><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Home &nbsp;</b></a>
                <a class="nav-link text-white" href="logout.php"><b>Log Out &nbsp;</b></a>
                <span class="nav-separator mx-2 text-white">|</span>
                <a class="nav-link text-white hr" href="#">&nbsp;&nbsp;Follow us &nbsp;&nbsp;</a>
                <div class = "nav-links d-flex align-items-center h-right">
                    <a class="nav-icon" href="#"><i class="ri-instagram-fill"></i></a>
                    <a class="nav-icon" href="#"><i class="ri-facebook-circle-fill"></i></a>
                    <a class="nav-icon" href="#"><i class="ri-github-fill"></i></a>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </div>
            </div>
        </div>
    </nav>
    <div class="main-content align-items-center text-dark">
        <h1>Welcome to Pagsulat!</h1>
        <h3>Unleash your creativity and share your stories with the world.</h3>
        <br>
        <hr style="border: 1px solid black; width: 80%; color:black; margin: 20px auto;">
        
    </div>

    <div class="container mt-4 text-dark">
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
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden" name="reply_comment_id" value="<?php echo $comment['id']; ?>">
                                        <textarea name="reply_content" class="form-control" rows="3" placeholder="Write your reply..."></textarea>
                                        <button type="submit" class="btn btn-primary mt-2">Reply</button>
                                    </form>

                                    <!-- Display replies if any -->
                                    <?php 
                                    $replyQuery = "SELECT reply_content, created_at, users.username AS reply_author
                                                   FROM comment_replies
                                                   LEFT JOIN users ON comment_replies.user_id = users.id
                                                   WHERE comment_replies.comment_id = ?";
                                    $replyStmt = $conn->prepare($replyQuery);
                                    $replyStmt->bind_param("i", $comment['id']);
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
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="post_id" value="<?php echo $postId; ?>">
                            <textarea name="comment" class="form-control" rows="3" placeholder="Add a new comment..."></textarea>
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
