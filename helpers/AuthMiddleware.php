<?php
class AuthMiddleware {
    public static function handle($requiredPermission = null) {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user is logged in
        if (empty($_SESSION['user_id'])) {
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            $_SESSION['error_message'] = "Please login to continue";
            header("Location: /index.php");
            exit();
        }
        
        // Check session activity timeout (30 minutes)
        if (time() - $_SESSION['last_activity'] > 1800) {
            session_unset();
            session_destroy();
            $_SESSION['error_message'] = "Session expired";
            header("Location: /index.php");
            exit();
        }
        
        // Update last activity time
        $_SESSION['last_activity'] = time();
        
        // Check specific permission if required
        if ($requiredPermission && !in_array($requiredPermission, $_SESSION['permissions'])) {
            $_SESSION['error_message'] = "Unauthorized access";
            header("Location: /dashboard/index.php");
            exit();
        }
        
        return true;
    }
}