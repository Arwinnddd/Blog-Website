<?php
session_start();
include 'db_connect.php';


header("Cache-Control: n-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$session_timeout = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $session_timeout){
    session_unset();
    session_destroy();
    header('Location: userLogin.php');
    exit;
}

$_SESSION['last_activity'] = time ();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['username']) || !isset($_POST['password'])) {
        echo "<div class='alert alert-danger'>Please fill in all fields.</div>";
    } else {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        // Prepare SQL statement
        $stmt = $conn->prepare("SELECT id, username, role, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['user_id'] = $user['id'];
                
                // Redirect user based on role
                if ($user['role'] === 'admin') {
                    header("Location: adminDashboard.php");
                } else {
                    header("Location: user_dashboard.php");
                }
                exit();
            } else {
                echo "<div class='alert alert-danger'>Invalid username or password.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Invalid username or password.</div>";
        }
        
        $stmt->close();
    }
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagsulat - Log In</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css">
    <style>

        *{
            font-family: 'Segoe UI', Poppins, Tahoma, Geneva, Verdana, sans-serif;

        }

        body {
            background: url(images/background.jpg) no-repeat center center fixed;
            background-size: cover;
            color: white;
        }

        .form-container {
            background-color: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            padding: 30px;
            margin-top: 100px;
        }

        .navbar {
            background-color: rgba(0, 0, 0, 0.7); 
            backdrop-filter: blur(10px); 
            -webkit-backdrop-filter: blur(10px); 
            padding: 10px 20px;
        }

        .navbar-brand {
            font-size: 1.8rem;
            font-weight: bold;
        }
        

        .btn{
            display: inline-block;
            padding: 13px 35px;
            background: black;
            color: #fffffffe;
            font-size: 15px;
            font-weight: 600;
            border: 30px;
            border-color: #edfcbdfe;
            border-radius: 30px;
            transition: all .50s ease;
            cursor: pointer;
        }

        .btn:hover{
            transform: translatey(-2px);
            letter-spacing: 1px;
            background: #83896ffe;
        }

        
        .mb-3 input {
            padding-top: 2px;
            padding: 10px;
            background-color: transparent;
            border-bottom: 1px solid #ccc;
            box-sizing: border-box;
            outline: none;
            color: white;
            
        }

        .mb-3 input:focus {
            background-color: transparent;
            box-sizing: border-box;
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
            font-size: 1.2rem;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .h-right a:hover {
            color: #a5ab90fe;
            transform: translateY(-3px);
            
        }


    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container-fluid">
            <div class="row w-100 d-flex align-items-center justify-content-between">

                <div class="col-sm-3 d-flex justify-content-center">
                    <a class="navbar-brand text-white text-center" href="#"> p a g s u l a t  </a>
                </div>

                <div class="col-sm-4 d-flex justify-content-end h-right">
                    <a style="transform:none; color:white;" href="#">Follow us</a>
                    <a href="#"><i class="ri-instagram-fill"></i></a>
                    <a href="#"><i class="ri-facebook-circle-fill"></i></a>
                    <a href="#"><i class="ri-github-fill"></i></a>
                </div>
            </div>
        </div>
    </nav>


    <div class="container mt-5">
        <div class="row d-flex justify-content-center align-items-center" >
            <div class="col-sm-4">
                <br><br><br><br>
                <h1 class="mt-4" style="color:black;"><b> Haven for words </b></h1>
                <p class="text-left mt-4" style="color: black; width: 400px; margin-right: 50px; font-size: 16px;"> Discover a world of stories, poems, and essays. Share your thoughts, connect with fellow writers, and let your imagination soar.</p>
                <br><br>

            </div>
                <div class="col-sm-5">
                    <div class="container d-flex justify-content-center align-items-center">
                        <div class="form-container col-md-10">
                            <h2 class="text-center"><b>Log In</b></h2>
                            <form action="userLogIn.php" method="POST">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input style= "color: white;" type="text" class="form-control" id="username" placeholder="...@gmail.com"  name="username" required autocomplete="off">
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input style= "color: white;" type="password" class="form-control" id="password" placeholder="8 characters minimum" minlength="1" name="password" required autocomplete="off">
                                </div>
                                <button type="submit" class="btn btn-success w-100">Log In</button>
                                <p class="text-center mt-3">
                                    Don't have an account? <a href="userRegistration.php" class="text-warning">Register here</a>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script> -->
</body>
</html>
