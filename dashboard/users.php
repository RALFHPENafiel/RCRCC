<?php
// Start session securely
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => true,
    'cookie_samesite' => 'Strict'
]);

// Define root path and include required files
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/config/db.php';
require_once ROOT_PATH . '/helpers/AuthMiddleware.php';
require_once ROOT_PATH . '/helpers/Auth.php';
require_once ROOT_PATH . '/helpers/CSRF.php';

// Verify database connection
if (!isset($conn)) {
    die("Database connection failed");
}

// Check permissions
Auth::requirePermission('manage_users');

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        CSRF::verifyToken($_POST['csrf_token'] ?? '');
        
        if (isset($_POST['create_user'])) {
            $name = trim($_POST['name'] ?? '');
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
            $password = $_POST['password'] ?? '';
            $roleId = (int)($_POST['role_id'] ?? 0);
            
            // Validate inputs
            if (empty($name) || empty($email) || empty($password) || $roleId <= 0) {
                throw new Exception("All fields are required");
            }
            
            // Validate password strength
            if (strlen($password) < 8) {
                throw new Exception("Password must be at least 8 characters");
            }
            if (!preg_match('/[A-Z]/', $password)) {
                throw new Exception("Password must contain at least one uppercase letter");
            }
            if (!preg_match('/[0-9]/', $password)) {
                throw new Exception("Password must contain at least one number");
            }
            if (!preg_match('/[^A-Za-z0-9]/', $password)) {
                throw new Exception("Password must contain at least one special character");
            }
            
            // Check if email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                throw new Exception("Email address already in use");
            }
            
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            
            $stmt = $conn->prepare("INSERT INTO users 
                                   (name, email, password, role_id, created_at) 
                                   VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$name, $email, $passwordHash, $roleId]);
            
            $_SESSION['success_message'] = "User created successfully!";
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        $_SESSION['form_data'] = $_POST; // Save form data for repopulation
    }
    
    header("Location: users.php");
    exit();
}
if (isset($_POST['update_user'])) {
    $userId = (int)$_POST['user_id'];
    $name = trim($_POST['name'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $roleId = (int)($_POST['role_id'] ?? 0);
    
    // Validate inputs
    if (empty($name) || empty($email) || $roleId <= 0) {
        throw new Exception("All fields are required");
    }
    
    // Check if email already exists for other users
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $userId]);
    if ($stmt->fetch()) {
        throw new Exception("Email address already in use");
    }
    
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role_id = ? WHERE id = ?");
    $stmt->execute([$name, $email, $roleId, $userId]);
    
    $_SESSION['success_message'] = "User updated successfully!";
}

if (isset($_POST['delete_user']) && isset($_POST['user_id'])) {
    try {
        // Verify CSRF token
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            error_log("CSRF token validation failed");
            throw new Exception("Security validation failed");
        }
        
        $userId = (int)$_POST['user_id'];
        
        // Prevent deleting yourself
        if ($userId === (int)($_SESSION['user_id'] ?? 0)) {
            throw new Exception("You cannot delete your own account");
        }
        
        // Check if user exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user) {
            throw new Exception("User not found");
        }
        
        // Begin transaction
        $conn->beginTransaction();
        
        try {
            // Handle projects created by this user (transferring to admin or another user)
            // Assuming admin has user_id = 1 or you can get this from configuration
            $adminUserId = 1; // Replace with appropriate admin ID
            
            // Transfer projects to admin
            $stmt = $conn->prepare("UPDATE projects SET created_by = ? WHERE created_by = ?");
            $stmt->execute([$adminUserId, $userId]);
            
            // Transfer project updates to admin
            $stmt = $conn->prepare("UPDATE project_updates SET created_by = ? WHERE created_by = ?");
            $stmt->execute([$adminUserId, $userId]);
            
            // Now delete the user - other constraints will be handled automatically
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            
            if ($stmt->rowCount() === 0) {
                throw new Exception("Failed to delete user");
            }
            
            $conn->commit();
            $_SESSION['success_message'] = "User deleted successfully!";
            
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    } catch (Exception $e) {
        error_log("Error deleting user: " . $e->getMessage());
        $_SESSION['error_message'] = "Error deleting user: " . $e->getMessage();
    }
    
    // Redirect
    header("Location: " . $_SERVER['HTTP_REFERER'] ?? $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_POST['reset_password'])) {
    $userId = (int)$_POST['user_id'];
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Password validation
    if ($password !== $confirmPassword) {
        throw new Exception("Passwords do not match");
    }
    
    // [Use your existing password validation code]
    
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$passwordHash, $userId]);
    
    $_SESSION['success_message'] = "Password updated successfully!";
}

if (isset($_POST['toggle_status'])) {
    $userId = (int)$_POST['user_id'];
    $activeStatus = (int)$_POST['active_status'];
    
    // Prevent deactivating yourself
    if ($userId === (int)$_SESSION['user_id'] && $activeStatus === 0) {
        throw new Exception("You cannot deactivate your own account");
    }
    
    $stmt = $conn->prepare("UPDATE users SET is_active = ? WHERE id = ?");
    $stmt->execute([$activeStatus, $userId]);
    
    $status = $activeStatus ? "activated" : "deactivated";
    $_SESSION['success_message'] = "User $status successfully!";
}

// Get all users with their roles
$users = [];
$roles = [];
try {
    $users = $conn->query("SELECT u.*, r.role_name 
                          FROM users u 
                          JOIN roles r ON u.role_id = r.id 
                          ORDER BY u.created_at DESC")
                  ->fetchAll(PDO::FETCH_ASSOC);
                  
    $roles = $conn->query("SELECT * FROM roles ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $_SESSION['error_message'] = "Error loading user data. Please try again later.";
}
?>

<?php
// posts.php
$page_title = "Manage Postss";
require_once 'header.php';


// Your page-specific content here
?>

<div class="container-fluid mt-4">
    <title>User Management</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 25px;
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .badge {
            font-size: 0.85em;
            padding: 0.5em 0.75em;
        }
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .password-strength {
            height: 5px;
            margin-top: 5px;
            background-color: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
        }
        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.3s ease;
        }
        .form-label.required:after {
            content: " *";
            color: #dc3545;
        }
    </style>
</head>
<body>
    
    <div class="container mt-4">
        <!-- Display success/error messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['success_message'], ENT_QUOTES) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['error_message'], ENT_QUOTES) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-users me-2"></i>User Management</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <i class="fas fa-plus me-1"></i> Create New User
            </button>
        </div>

        <!-- Users Table -->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= ucfirst(htmlspecialchars($user['role_name'])) ?></td>
                        <td>
                            <span class="badge bg-<?= $user['is_active'] ? 'success' : 'danger' ?>">
                                <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                        <td><?= $user['last_login'] ? date('M j, Y H:i', strtotime($user['last_login'])) : 'Never' ?></td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-warning edit-user" 
                                        data-id="<?= htmlspecialchars($user['id']) ?>"
                                        data-name="<?= htmlspecialchars($user['name']) ?>"
                                        data-email="<?= htmlspecialchars($user['email']) ?>"
                                        data-role="<?= htmlspecialchars($user['role_id']) ?>"
                                        data-active="<?= htmlspecialchars($user['is_active']) ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php if ($user['id'] != ($_SESSION['user_id'] ?? 0)): ?>
                                    <form method="POST" action="" class="d-inline">
                                        <?= CSRF::tokenField(); ?>
                                        <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id']) ?>">
                                        <input type="hidden" name="delete_user" value="1">
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No users found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Create User Modal -->
        <div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="POST" id="createUserForm">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Create New User</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <?= CSRF::tokenField(); ?>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label required">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?= htmlspecialchars($_SESSION['form_data']['name'] ?? '', ENT_QUOTES) ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label required">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= htmlspecialchars($_SESSION['form_data']['email'] ?? '', ENT_QUOTES) ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label required">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <div class="password-strength">
                                        <div class="password-strength-bar" id="passwordStrengthBar"></div>
                                    </div>
                                    <small class="text-muted">Must be at least 8 characters with uppercase, number, and special character</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label required">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    <div class="invalid-feedback" id="passwordMatchFeedback">
                                        Passwords do not match
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="role_id" class="form-label required">Role</label>
                                <select class="form-select" id="role_id" name="role_id" required>
                                    <?php foreach ($roles as $role): ?>
                                    <option value="<?= htmlspecialchars($role['id']) ?>" 
                                        <?= isset($_SESSION['form_data']['role_id']) && $_SESSION['form_data']['role_id'] == $role['id'] ? 'selected' : '' ?>>
                                        <?= ucfirst(htmlspecialchars($role['role_name'])) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="create_user" class="btn btn-primary">Create User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit User Modal -->
        <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="POST" action="" id="editUserForm">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i>Edit User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <?= CSRF::tokenField(); ?>
                            <input type="hidden" name="user_id" id="edit_user_id">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_name" class="form-label required">Full Name</label>
                                    <input type="text" class="form-control" id="edit_name" name="name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_email" class="form-label required">Email</label>
                                    <input type="email" class="form-control" id="edit_email" name="email" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_role_id" class="form-label required">Role</label>
                                    <select class="form-select" id="edit_role_id" name="role_id" required>
                                        <?php foreach ($roles as $role): ?>
                                        <option value="<?= htmlspecialchars($role['id']) ?>"><?= ucfirst(htmlspecialchars($role['role_name'])) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3 d-flex align-items-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="edit_is_active" name="is_active" value="1">
                                        <label class="form-check-label" for="edit_is_active">Active User</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password">
                                <small class="text-muted">Leave blank to keep current password</small>
                                <div class="password-strength">
                                    <div class="password-strength-bar" id="editPasswordStrengthBar"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_new_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password">
                                <div class="invalid-feedback" id="editPasswordMatchFeedback">
                                    Passwords do not match
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JavaScript for user management -->
    <script>
    // Edit user handler
    document.querySelectorAll('.edit-user').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            const userName = this.getAttribute('data-name');
            const userEmail = this.getAttribute('data-email');
            const userRole = this.getAttribute('data-role');
            const userActive = this.getAttribute('data-active') === '1';
            
            document.getElementById('edit_user_id').value = userId;
            document.getElementById('edit_name').value = userName;
            document.getElementById('edit_email').value = userEmail;
            document.getElementById('edit_role_id').value = userRole;
            document.getElementById('edit_is_active').checked = userActive;
            
            const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
            editModal.show();
        });
    });

    // Password strength indicator
    function checkPasswordStrength(password) {
        let strength = 0;
        
        // Length check
        if (password.length >= 8) strength += 1;
        if (password.length >= 12) strength += 1;
        
        // Complexity checks
        if (/[A-Z]/.test(password)) strength += 1;
        if (/[0-9]/.test(password)) strength += 1;
        if (/[^A-Za-z0-9]/.test(password)) strength += 1;
        
        return strength;
    }

    function updatePasswordStrengthBar(password, barId) {
        const strength = checkPasswordStrength(password);
        const bar = document.getElementById(barId);
        
        // Reset
        bar.style.width = '0%';
        bar.style.backgroundColor = '#dc3545';
        
        if (password.length === 0) return;
        
        // Update based on strength
        if (strength >= 1) {
            bar.style.width = '25%';
            bar.style.backgroundColor = '#dc3545';
        }
        if (strength >= 3) {
            bar.style.width = '50%';
            bar.style.backgroundColor = '#fd7e14';
        }
        if (strength >= 4) {
            bar.style.width = '75%';
            bar.style.backgroundColor = '#ffc107';
        }
        if (strength >= 5) {
            bar.style.width = '100%';
            bar.style.backgroundColor = '#28a745';
        }
    }

    // Create user form validation
    const createForm = document.getElementById('createUserForm');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    
    passwordInput.addEventListener('input', function() {
        updatePasswordStrengthBar(this.value, 'passwordStrengthBar');
    });
    
    confirmPasswordInput.addEventListener('input', function() {
        if (passwordInput.value !== this.value) {
            this.classList.add('is-invalid');
            document.getElementById('passwordMatchFeedback').style.display = 'block';
        } else {
            this.classList.remove('is-invalid');
            document.getElementById('passwordMatchFeedback').style.display = 'none';
        }
    });
    
    createForm.addEventListener('submit', function(e) {
        if (passwordInput.value !== confirmPasswordInput.value) {
            e.preventDefault();
            confirmPasswordInput.classList.add('is-invalid');
            document.getElementById('passwordMatchFeedback').style.display = 'block';
            confirmPasswordInput.focus();
        }
    });

    // Edit user form validation
    const editForm = document.getElementById('editUserForm');
    const newPasswordInput = document.getElementById('new_password');
    const confirmNewPasswordInput = document.getElementById('confirm_new_password');
    
    newPasswordInput.addEventListener('input', function() {
        updatePasswordStrengthBar(this.value, 'editPasswordStrengthBar');
    });
    
    confirmNewPasswordInput.addEventListener('input', function() {
        if (newPasswordInput.value !== this.value && newPasswordInput.value !== '') {
            this.classList.add('is-invalid');
            document.getElementById('editPasswordMatchFeedback').style.display = 'block';
        } else {
            this.classList.remove('is-invalid');
            document.getElementById('editPasswordMatchFeedback').style.display = 'none';
        }
    });
    
    editForm.addEventListener('submit', function(e) {
        if (newPasswordInput.value !== confirmNewPasswordInput.value && newPasswordInput.value !== '') {
            e.preventDefault();
            confirmNewPasswordInput.classList.add('is-invalid');
            document.getElementById('editPasswordMatchFeedback').style.display = 'block';
            confirmNewPasswordInput.focus();
        }
    });

    // Clear form data from session when modal is closed
    document.getElementById('createUserModal').addEventListener('hidden.bs.modal', function() {
        <?php unset($_SESSION['form_data']); ?>
    });
    </script>

<?php
require_once 'footer.php';
?>