<?php
// helpers/CSRF.php
class CSRF {
    /**
     * Generate a CSRF token
     */
    public static function generateToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF token
     */
    public static function verifyToken($token) {
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            exit('CSRF token validation failed');
        }
        return true;
    }
    
    /**
     * HTML for CSRF token field
     */
    public static function tokenField() {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }
}
?>