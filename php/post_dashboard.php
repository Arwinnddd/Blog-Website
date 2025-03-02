 <?php

session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: userLogin.php');
    exit;
}

// Database connection
include 'db_connect.php';

// Handle post creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_post'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $userId = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO posts (user_id, title, content, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $userId, $title, $content);

    if ($stmt->execute()) {
        echo "Post created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();  // Close the statement after execution
}

// Handle post update after password verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_post'])) {
    $postId = $_POST['post_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $adminPassword = $_POST['admin_password'];

    // Verify the admin password
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($storedPassword);
    $stmt->fetch();

    if (password_verify($adminPassword, $storedPassword)) {
        // Proceed to update the post
        $stmt->close();  // Close the previous statement before executing another

        $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
        $stmt->bind_param("ssi", $title, $content, $postId);

        if ($stmt->execute()) {
            echo "Post updated successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Incorrect password. Post not updated.";
    }
    $stmt->close();  // Close the statement after execution
}

// Handle post deletion after password verification
if (isset($_POST['delete_post_button']) && isset($_POST['delete_post']) && isset($_POST['delete_post_password'])) {
    $postId = $_POST['delete_post']; // Get post ID from POST
    $adminPassword = $_POST['delete_post_password'];

    // Verify the admin password
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($storedPassword);
    $stmt->fetch();

    if (password_verify($adminPassword, $storedPassword)) {
        // Proceed to delete the post
        $stmt->close();  // Close the previous statement before executing another

        $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->bind_param("i", $postId);

        if ($stmt->execute()) {
            echo "Post deleted successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Incorrect password. Post not deleted.";
    }
    $stmt->close();  // Close the statement after execution
}


// Fetch all posts
$query = "SELECT id, title, content, created_at FROM posts ORDER BY created_at DESC";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagsulat - Post Dashboard</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
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

    <nav class="navbar  fixed-top">
        <div class="container-fluid d-flex justify-content-between align-items-center">
             
            <a class="navbar-brand text-white" href="#">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;p a g s u l a t</a>
        
            <div class="nav-links d-flex align-items-center">
                <a class="nav-link " href="adminDashboard.php"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Home &nbsp;</b></a>
                <a class="nav-link " href="#" style="color: #a5ab90fe;"><b>&nbsp;Post Dashboard &nbsp;</b></a>
                <a class="nav-link " href="comments_dashboard.php"><b>&nbsp;Comment Dashboard &nbsp;</b></a>
                <a class="nav-link " href="accountsManagement.php"><b>&nbsp;Account Management &nbsp;</b></a>
                <span class="nav-separator mx-2 text-white">|</span>
                <a class="nav-link " href="logout.php"><b>&nbsp;&nbsp;&nbsp;&nbsp;Log Out &nbsp;</b></a>
                
            </div>
        </div>
    </nav>
    <br><br><br><br>

    <div class="container mt-4">
        <h2>Create a New Post</h2>
        <form method="POST" action="post_dashboard.php">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Content</label>
                <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
            </div>
            <button type="submit" name="create_post" class="btn btn-dark">Create Post</button>
        </form>

        <hr>

<h2>Existing Posts</h2>
<?php if ($result->num_rows > 0): ?>
    <div class="list-group">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="list-group-item">
                <h5><?php echo htmlspecialchars($row['title']); ?></h5>
                <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                <p><small>Created on <?php echo $row['created_at']; ?></small></p>

                <!-- Update Post Form -->
                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#updatePostModal<?php echo $row['id']; ?>">Update</button>

                <!-- Delete Post Link -->
                <a href="?delete_post=<?php echo $row['id']; ?>" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deletePostModal<?php echo $row['id']; ?>">Delete</a>

                <!-- Update Post Modal -->
                <div class="modal fade" id="updatePostModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="updatePostModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="updatePostModalLabel">Update Post</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="post_dashboard.php">
                                    <input type="hidden" name="post_id" value="<?php echo $row['id']; ?>">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($row['title']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="content" class="form-label">Content</label>
                                        <textarea class="form-control" id="content" name="content" rows="5" required><?php echo htmlspecialchars($row['content']); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="admin_password" class="form-label">Enter Admin Password</label>
                                        <input type="password" class="form-control" id="admin_password" name="admin_password" required>
                                    </div>
                                    <button type="submit" name="update_post" class="btn btn-primary">Update Post</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delete Post Modal -->
              <!-- Delete Post Modal -->
                <div class="modal fade" id="deletePostModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="deletePostModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deletePostModalLabel">Delete Post</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="post_dashboard.php">
                            <!-- Add a hidden field for post_id -->
                            <input type="hidden" name="delete_post" value="<?php echo $row['id']; ?>">
                            <div class="mb-3">
                                <label for="delete_post_password" class="form-label">Enter Admin Password</label>
                                <input type="password" class="form-control" id="delete_post_password" name="delete_post_password" required>
                            </div>
                            <button type="submit" class="btn btn-danger" name="delete_post_button">Delete Post</button>
                        </form>
                    </div>
                </div>
                </div>
                </div>

                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p>No posts found.</p>
                <?php endif; ?>



    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>







