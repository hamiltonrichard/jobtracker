<?php
/**
 * logout.php
 * Handles the secure termination of the user session.
 */
session_start();

// 1. Clear all session variables [2]
$_SESSION = [];

// 2. Invalidate the session cookie [2]
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Destroy the session and redirect [2]
session_destroy();
header("Location: login.php");
exit;
?>
