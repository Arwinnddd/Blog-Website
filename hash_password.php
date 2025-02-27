<?php
$plainPassword = "11111111"; // Replace with your desired admin password
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
echo "Hashed Password: " . $hashedPassword;
?>
