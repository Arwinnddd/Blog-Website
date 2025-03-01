<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagsulat - Acount Management</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-vtXRMe3mGCbOeY7l30aIg8H9p3GdeSe4IFlP6G8JMa7o7lXvnz3GFKzPxzJdPfGK" crossorigin="anonymous"></script>


<style>
        
        *{
            font-family: 'Segoe UI', Poppins, Tahoma, Geneva, Verdana, sans-serif;
        }
        .modal-content {
            border-radius: 10px;
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
                <a class="nav-link " href="post_dashboard.php"><b>&nbsp;Post Dashboard &nbsp;</b></a>
                <a class="nav-link " href="comments_dashboard.php"><b>&nbsp;Comment Dashboard &nbsp;</b></a>
                <a class="nav-link " href="#" style="color: #a5ab90fe;"><b>&nbsp;Account Management &nbsp;</b></a>
                <span class="nav-separator mx-2 text-white">|</span>
                <a class="nav-link " href="logout.php"><b>&nbsp;&nbsp;&nbsp;&nbsp;Log Out &nbsp;</b></a>
                
            </div>
        </div>
    </nav>
    
    <br><br><br><br>

<div class="container mt-5">
    <h2 class="text-center">Account Management</h2>
    <a href="create_account.php" class="btn btn-success mb-3">Create New Account</a>

    <table class="table table-bordered table-striped mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            
            /////////////////////////
            include 'db_connect.php';
            $query = "SELECT id, username, email, role FROM users";
            $result = $conn->query($query);
            if ($result->num_rows > 0) {
                while ($user = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($user['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                    echo "<td>
                            <a href='edit_account.php?id=" . $user['id'] . "' class='btn btn-warning btn-sm'>Edit</a>
                            <button class='btn btn-danger btn-sm delete-btn' data-id='" . $user['id'] . "' data-bs-toggle='modal' data-bs-target='#deleteModal'>Delete</button>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='text-center'>No users found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Account Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this account?</p>
                <form id="deleteForm">
                    <input type="hidden" name="user_id" id="user_id">
                    <label for="adminPassword" class="form-label">Enter Admin Password:</label>
                    <input type="password" class="form-control" name="password" id="adminPassword" required autocomplete="off">
                    <div class="mt-3">
                        <button type="submit" class="btn btn-danger">Confirm Delete</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $(".delete-btn").click(function() {
        let userId = $(this).data("id");
        $("#user_id").val(userId);
    });

    $("#deleteForm").submit(function(event) {
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: "delete_account.php",
            data: $(this).serialize(),
            success: function(response) {
                alert(response);
                location.reload();
            }
        });
    });
});
</script>

</body>
</html>
