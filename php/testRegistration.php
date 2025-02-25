<?php
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
 
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'");
header("Referrer-Policy: no-referrer");
 
include 'db_connect.php';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF validation failed!");
    }
 
    $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
 
 
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }
 
    // Password strength check (Server-side - optional, but good to have some basic check)
    $password_plain = $_POST['password']; // Get the plain password for basic check
    if (strlen($password_plain) < 8) {
        die("Password must be at least 8 characters long."); // Basic length check
    }
 
    // Use a prepared statement
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
    $stmt->bind_param("sss", $username, $email, $password);
 
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Registration successful! <a href='login.php'>Login here</a></div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }
 
    $stmt->close();
    $conn->close();
 
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagsulat - Sign Up</title>
 
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
            color: white;
        }
 
        .form-container {
            background-color: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            padding: 30px;
            margin-top: 80px;
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
 
        .btn a{
            font-size: 15px;
            font-weight: 600;
            color: white;
            text-decoration: none;
            transition: all 0.50s ease;
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
 
        /* Style for password strength bar */
        .password-strength-bar {
            width: 100%;
            height: 10px;
            background-color: #ddd;
            margin-top: 5px;
            border-radius: 5px;
        }
 
        .password-strength-bar-inner {
            height: 100%;
            width: 0%;
            background-color: red; /* Default weak color */
            border-radius: 5px;
            transition: width 0.3s ease-in-out, background-color 0.3s ease-in-out;
        }
 
        .password-strength-text {
            font-size: 0.9em;
            margin-top: 5px;
            color: #fff;
            text-align: left;
        }
 
 
    </style>
 
 
</head>
<body>
 
    <nav class="navbar">
        <div class="container-fluid">
            <div class="row w-100 d-flex align-items-center justify-content-between">
 
                <div class="col-sm-3 d-flex justify-content-center">
                    <a class="navbar-brand text-white text-center" href="userLogIn.php"> p a g s u l a t </a>
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
 
    <div class="container d-flex justify-content-center align-items-center">
        <div class="form-container col-md-5">
            <h2 class="text-center"><b>Create an account</b></h2>
 
            <form action="userRegistration.php" method="POST" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
 
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input style= "color: white;" type="text" class="form-control" id="username" placeholder="@" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input style= "color: white;" type="email" class="form-control" id="email" placeholder="...@gmail.com" name="email" required>
                </div>

                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input style= "color: white;" type="password" class="form-control" id="password" placeholder="8 characters minimum" minlength="8" name="password" required autocomplete="new-password" oninput="checkPasswordStrength()">
                        <div class="password-strength-bar">
                            <div class="password-strength-bar-inner" id="password-strength-bar-inner"></div>
                        </div>
                        <div class="password-strength-text" id="password-strength-text"></div>
                </div>


                <br>
                <button type="submit" class="btn btn-success w-100">
                <a href="userLogIn.php"> </a>
                Sign Up</button>
                <p class="text-center mt-3">
                    Already have an account? <a href="userLogIn.php" class="text-warning">Log in here</a>
                </p>
            </form>
        </div>
    </div>
 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
 
    <script>
        function checkPasswordStrength() {
            let password = document.getElementById("password").value;
            let strength = 0;
 
            if (password.length >= 8) {
                strength += 1;
            }
            if (password.length >= 12) {
                strength += 1;
            }
            if (password.match(/[a-z]+/)) {
                strength += 1;
            }
            if (password.match(/[A-Z]+/)) {
                strength += 1;
            }
            if (password.match(/[0-9]+/)) {
                strength += 1;
            }
            if (password.match(/[^a-zA-Z0-9]+/)) {
                strength += 1;
            }
 
            let strengthBar = document.getElementById("password-strength-bar-inner");
            let strengthText = document.getElementById("password-strength-text");
            let strengthPercentage = (strength / 6) * 100; // 6 is max possible strength points
 
            strengthBar.style.width = strengthPercentage + "%";
 
            if (strengthPercentage <= 16.66) {
                strengthBar.style.backgroundColor = "red";
                strengthText.textContent = "Very Weak";
                strengthText.style.color = "red";
            } else if (strengthPercentage <= 33.32) {
                strengthBar.style.backgroundColor = "orange";
                strengthText.textContent = "Weak";
                strengthText.style.color = "orange";
            } else if (strengthPercentage <= 49.98) {
                strengthBar.style.backgroundColor = "yellow";
                strengthText.textContent = "Medium";
                strengthText.style.color = "yellow";
            } else if (strengthPercentage <= 66.64) {
                strengthBar.style.backgroundColor = "#90EE90"; // Light green
                strengthText.textContent = "Strong";
                strengthText.style.color = "#90EE90";
            } else if (strengthPercentage <= 83.3) {
                strengthBar.style.backgroundColor = "green";
                strengthText.textContent = "Very Strong";
                strengthText.style.color = "green";
            } else if (strengthPercentage <= 100) {
                strengthBar.style.backgroundColor = "darkgreen";
                strengthText.textContent = "Excellent";
                strengthText.style.color = "darkgreen";
            }
        }
    </script>
 
</body>
</html>