<?php
// config/session.php - Include this at the top of your entry points
// Session configuration for security
ini_set('session.cookie_httponly', 1); // Prevent JavaScript access to session cookie
ini_set('session.use_only_cookies', 1); // Only allow cookies for sessions
ini_set('session.cookie_samesite', 'Lax'); // Prevent CSRF
session_start();
// In production with HTTPS:
// ini_set('session.cookie_secure', 1);

// Regenerate session ID regularly to prevent fixation attacks
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} else if (time() - $_SESSION['created'] > 1800) { // 30 minutes
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

// Optional: Set session timeout
$_SESSION['last_activity'] = time();
?>