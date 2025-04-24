<?php
// auth/register.php - Updated version
session_start();
require '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $name = trim(htmlspecialchars($_POST["name"]));
    $email = filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL);
    $password = trim($_POST["password"]);
    
    // Validate inputs
    $errors = [];
    if (!$name || strlen($name) < 2) {
        $errors[] = "Valid name is required";
    }
    if (!$email) {
        $errors[] = "Valid email is required";
    }
    if (!$password || strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    }
    
    if (count($errors) > 0) {
        $_SESSION['register_errors'] = $errors;
        $_SESSION['register_data'] = ['name' => $name, 'email' => $email];
        header("Location: ../register.php");
        exit();
    }
    
    // Hash password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    
    // Important: Set role_id to 3 (Viewer) for all self-registrations
    // Only admins can create admin/editor accounts
    $role_id = 3; // Viewer role only
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['register_errors'] = ["Email already exists!"];
        $_SESSION['register_data'] = ['name' => $name, 'email' => $email];
        header("Location: ../register.php");
        exit();
    }

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role_id) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$name, $email, $password_hash, $role_id])) {
        $_SESSION['success_message'] = "Registration successful! You can now log in.";
        header("Location: ../index.php");
        exit();
    } else {
        $_SESSION['register_errors'] = ["Error registering user."];
        $_SESSION['register_data'] = ['name' => $name, 'email' => $email];
        header("Location: ../register.php");
        exit();
    }
}
?>