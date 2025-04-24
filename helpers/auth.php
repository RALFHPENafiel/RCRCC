<?php
class Auth {
    /**
     * Check if user is logged in
     */
    public static function check() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Get current user's role
     */
    public static function role() {
        return $_SESSION['role_id'] ?? null;
    }
    
    /**
     * Check if user has required role
     */
    public static function hasRole($roles) {
        if (!self::check()) {
            return false;
        }
        
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        
        return in_array($_SESSION['role_id'], $roles);
    }
    
    /**
     * Check if user has specific permission
     */
    public static function hasPermission($permission) {
        if (!self::check()) return false;
        
        // If you have permissions stored in session or database
        $permissions = $_SESSION['permissions'] ?? [];
        return in_array($permission, $permissions);
    }
    
    /**
     * Require user to be logged in
     */
    public static function requireLogin() {
        if (!self::check()) {
            $_SESSION['error_message'] = "Please log in to access this page.";
            header("Location: ../index.php");
            exit();
        }
    }
    
    /**
     * Require user to have specific role(s)
     */
    public static function requireRole($roles) {
        self::requireLogin();
        
        if (!self::hasRole($roles)) {
            $_SESSION['error_message'] = "You don't have permission to access this page.";
            header("Location: ../dashboard/index.php");
            exit();
        }
    }
    
    /**
     * NEW: Require specific permission
     */
    public static function requirePermission($permission) {
        self::requireLogin();
        
        if (!self::hasPermission($permission)) {
            $_SESSION['error_message'] = "You don't have permission to access this page.";
            header("Location: ../dashboard/index.php");
            exit();
        }
    }
    
    /**
     * Login method
     */
    public static function login($email, $password) {
        global $conn;
        
        $stmt = $conn->prepare("SELECT users.*, roles.role_name 
                               FROM users 
                               JOIN roles ON users.role_id = roles.id 
                               WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['role_name'] = $user['role_name'];
            
            // Set default permissions based on role
            self::setDefaultPermissions($user['role_id']);
            
            session_regenerate_id(true);
            return true;
        }
        
        return false;
    }
    
    /**
     * Set default permissions based on role
     */
    public static function setDefaultPermissions($roleId) {
        $permissions = [];
        
        switch ($roleId) {
            case 1: // Admin
                $permissions = ['manage_posts', 'manage_media', 'manage_users', 'manage_settings', 'manage_projects'];
                break;
            case 2: // Editor
                $permissions = ['manage_posts', 'manage_media'];
                break;
            case 3: // Viewer
                $permissions = [];
                break;
        }
        
        $_SESSION['permissions'] = $permissions;
    }
}
?>