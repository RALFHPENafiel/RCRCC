<?php
session_start();
require '../config/db.php';
require '../helpers/Auth.php';
require '../helpers/CSRF.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !CSRF::verifyToken($_POST['csrf_token'])) {
        $_SESSION['error_message'] = "Invalid form submission. Please try again.";
        header("Location: ../index.php");
        exit();
    }

    // Validate inputs
    $email = filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL);
    $password = trim($_POST["password"]);
    
    if (!$email || !$password) {
        $_SESSION['error_message'] = "Email and password are required.";
        header("Location: ../index.php");
        exit();
    }

    try {
        // Get user from database
        $stmt = $conn->prepare("SELECT users.*, roles.role_name 
                               FROM users 
                               JOIN roles ON users.role_id = roles.id 
                               WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {
            // Regenerate session first for security
            session_regenerate_id(true);
            
            // Set fresh session data
            $_SESSION = [
                "user_id" => $user["id"],
                "user_name" => $user["name"],
                "email" => $user["email"],
                "role_id" => $user["role_id"],
                "role_name" => $user["role_name"],
                "created" => time(),
                "IPaddress" => $_SERVER['REMOTE_ADDR'],
                "userAgent" => $_SERVER['HTTP_USER_AGENT']
            ];
            
            // Set permissions
            Auth::setDefaultPermissions($user['role_id']);
            
            // Update last login
            $updateStmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $updateStmt->execute([$user['id']]);
            
            // Redirect
            $redirect = $_SESSION['redirect_url'] ?? '../dashboard/index.php';
            unset($_SESSION['redirect_url']);
            header("Location: $redirect");
            exit();
        } else {
            // Track failed attempts
            $_SESSION['failed_attempts'] = ($_SESSION['failed_attempts'] ?? 0) + 1;
            $_SESSION['error_message'] = "Invalid email or password.";
            header("Location: ../index.php");
            exit();
        }
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        $_SESSION['error_message'] = "A system error occurred. Please try again later.";
        header("Location: ../index.php");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>