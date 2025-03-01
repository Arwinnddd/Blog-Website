<?php
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$nonce = base64_encode(random_bytes(16));
// header_remove("X-Frame-Options"); // Remove duplicates
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-$nonce'; style-src 'self'");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
// header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: no-referrer");

include 'db_connect.php';
$username_error = "";

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

    // Check for duplicate username
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_stmt->store_result();
    
    if ($check_stmt->num_rows > 0) {
        $username_error = "Username is already taken";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
        $stmt->bind_param("sss", $username, $email, $password);
        
        if ($stmt->execute()) {
            header("Location: userLogIn.php");
        } else {
            echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }
        
        $stmt->close();
    }
    
    $check_stmt->close();
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
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>

    <nav class="navbar">
        <div class="container-fluid">
            <div class="row w-100 d-flex align-items-center justify-content-between">

                <div class="col-sm-3 d-flex justify-content-center">
                    <a class="navbar-brand text-white text-center" href="#"> p a g s u l a t </a>
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
                    <input type="text" class="form-control <?php echo $username_error ? 'is-invalid' : ''; ?>" id="username" placeholder="@" name="username" required>
                    <?php if ($username_error): ?>
                        <div class="invalid-feedback"> <?php echo $username_error; ?> </div>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" placeholder="...@gmail.com" name="email" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                        <div class= input-box>
                                <input style= "color: white !important;" type="password" class="form-control" id="password" placeholder="8 characters minimum" minlength="8" name="password" required autocomplete="new-password">
                                <p>   &nbsp;&nbsp;   </p>
                                <img src="images/hide.png" id="eyeicon"> </input> </div>
                                <p id="message"> Password is <span id="strength"></span></p>
                </div>
                <br>
                <button type="submit" class="btn btn-success w-100">Sign Up</button>
                
                <p class="text-center mt-3">
                    Already have an account? <a href="userLogIn.php" class="text-warning">Log in here</a>
                </p>
            </form>
        </div>
    </div>
    <script src="script.js?v=<?php echo time(); ?>" nonce="<?php echo $nonce; ?>"></script>
</body>
</html>
