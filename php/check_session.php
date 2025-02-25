<?php
session_start();

// Set session timeout (in seconds)
$session_expiry_time = 10;

// Check if session exists and has not expired
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_expiry_time)) {
    // Session expired
    session_unset();
    session_destroy();
    http_response_code(401); // Unauthorized
    echo json_encode(['active' => false, 'message' => 'Session expired.']);
    exit;
}

// Update last activity timestamp
$_SESSION['last_activity'] = time();

// Session is still active
http_response_code(200); // OK
echo json_encode(['active' => true]);
exit;
?>
