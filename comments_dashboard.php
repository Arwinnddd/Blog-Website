<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: userLogin.php');
    exit;
}

// Database connection
include 'db_connect.php';

// Fetch comments
$query = "SELECT comments.id, comments.comment, comments.created_at, users.username AS comment_author, posts.title AS post_title
          FROM comments
          LEFT JOIN users ON comments.user_id = users.id
          LEFT JOIN posts ON comments.post_id = posts.id
          ORDER BY comments.created_at DESC";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

if (isset($_POST['delete'])) {
    $comment_id = $_POST['comment_id'];
 
    // First, delete related replies
    $delete_replies_query = "DELETE FROM comment_replies WHERE comment_id = ?";
    $stmt = $conn->prepare($delete_replies_query);
    $stmt->bind_param('i', $comment_id);
    $stmt->execute();
 
    // Now, delete the comment
    $delete_query = "DELETE FROM comments WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param('i', $comment_id);
    $stmt->execute();
 
    header('Location: adminDashboard.php');
    exit;
}

// Handle comment update
if (isset($_POST['update'])) {
    $comment_id = $_POST['comment_id'];
    $updated_comment = $_POST['updated_comment'];
    $update_query = "UPDATE comments SET comment = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('si', $updated_comment, $comment_id);
    $stmt->execute();
    header('Location: adminDashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagsulat - Comments Dashboard</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">

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
                <a class="nav-link " href="userLogin.php"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Home &nbsp;</b></a>
                <a class="nav-link " href="post_dashboard.php"><b>&nbsp;Post Dashboard &nbsp;</b></a>
                <a class="nav-link " href="#" style="color: #a5ab90fe;"><b>&nbsp;Comment Dashboard &nbsp;</b></a>
                <a class="nav-link " href="accountsManagement.php"><b>&nbsp;Account Management &nbsp;</b></a>
                <span class="nav-separator mx-2 text-white">|</span>
                <a class="nav-link " href="logout.php"><b>&nbsp;&nbsp;&nbsp;&nbsp;Log Out &nbsp;</b></a>
            
            </div>
        </div>
    </nav>
    <br><br><br><br>

    <div class="container mt-4">
        <h2>Comments Management</h2>
        <?php if ($result->num_rows > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Comment</th>
                        <th>Author</th>
                        <th>Post</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['comment']); ?></td>
                            <td><?php echo htmlspecialchars($row['comment_author']); ?></td>
                            <td><?php echo htmlspecialchars($row['post_title']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td>
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="comment_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                                <button class="btn btn-primary btn-sm" onclick="editComment(<?php echo $row['id']; ?>, '<?php echo addslashes($row['comment']); ?>')">Edit</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">No comments available.</p>
        <?php endif; ?>
    </div>

    <div class="modal" id="editModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Comment</h5>
                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="post">
                        <input type="hidden" id="comment_id" name="comment_id">
                        <div class="form-group">
                            <label for="updated_comment">Comment:</label>
                            <textarea class="form-control" id="updated_comment" name="updated_comment" required></textarea>
                        </div>
                        <button type="submit" name="update" class="btn btn-primary mt-2">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function editComment(id, comment) {
            document.getElementById('comment_id').value = id;
            document.getElementById('updated_comment').value = comment;
            var editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
