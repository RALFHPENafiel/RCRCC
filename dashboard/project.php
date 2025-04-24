<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "User session not found. Please log in again.";
    header("Location: login.php");
    exit();
}
$data['created_by'] = $_SESSION['user_id'];

define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/config/db.php';
require_once ROOT_PATH . '/helpers/AuthMiddleware.php';
require_once ROOT_PATH . '/helpers/Auth.php';
require_once ROOT_PATH . '/helpers/CSRF.php';

// Verify permissions
Auth::requirePermission('manage_projects');

// Handle all actions
$action = $_GET['action'] ?? 'list';
$projectId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// File upload settings
$uploadDir = ROOT_PATH . '/uploads/projects/';
$allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
$maxFileSize = 5 * 1024 * 1024; // 5MB

$engineers = $conn->query("SELECT * FROM engineers ORDER BY name")->fetchAll();

// Create upload directory if not exists
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Database operations
try {
    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        CSRF::verifyToken($_POST['csrf_token']);
        
        // Create/Update project
        if (isset($_POST['save_project'])) {
            // Handle engineer_id properly
            $engineer_id = null;
            if (!empty($_POST['engineer_id'])) {
                $engineer_id = (int)$_POST['engineer_id'];
                
                // Verify engineer exists
                $stmt = $conn->prepare("SELECT id FROM engineers WHERE id = ?");
                $stmt->execute([$engineer_id]);
                if (!$stmt->fetch()) {
                    $_SESSION['error_message'] = "Selected engineer does not exist";
                    header("Location: project.php?action=".($projectId ? 'edit&id='.$projectId : 'create'));
                    exit();
                }
            }
            
            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description']),
                'location' => trim($_POST['location']),
                'structure' => trim($_POST['structure']),
                'client_id' => (int)$_POST['client_id'],
                'contract_amount' => (float)str_replace(',', '', $_POST['contract_amount']),
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'status' => $_POST['status'],
                'engineer_id' => $engineer_id,
                'industry' => trim($_POST['industry'] ?? null)
            ];
            
            if ($projectId > 0) {
                // Update existing project
                $stmt = $conn->prepare("UPDATE projects SET 
                    name=:name, 
                    description=:description, 
                    location=:location, 
                    structure=:structure, 
                    client_id=:client_id,
                    contract_amount=:contract_amount, 
                    start_date=:start_date, 
                    end_date=:end_date, 
                    status=:status, 
                    engineer_id=:engineer_id, 
                    industry=:industry
                    WHERE id=:id");
                    
                $stmt->bindParam(':name', $data['name']);
                $stmt->bindParam(':description', $data['description']);
                $stmt->bindParam(':location', $data['location']);
                $stmt->bindParam(':structure', $data['structure']);
                $stmt->bindParam(':client_id', $data['client_id'], PDO::PARAM_INT);
                $stmt->bindParam(':contract_amount', $data['contract_amount']);
                $stmt->bindParam(':start_date', $data['start_date']);
                $stmt->bindParam(':end_date', $data['end_date']);
                $stmt->bindParam(':status', $data['status']);
                $stmt->bindParam(':engineer_id', $data['engineer_id'], PDO::PARAM_INT);
                $stmt->bindParam(':industry', $data['industry']);
                $stmt->bindParam(':id', $projectId, PDO::PARAM_INT);
                
                $stmt->execute();
                $message = "Project updated successfully!";
            } else {
                // Create new project
                $stmt = $conn->prepare("INSERT INTO projects
                    (name, description, location, structure, client_id,
                    contract_amount, start_date, end_date, status, created_by, engineer_id, industry)
                    VALUES (:name, :description, :location, :structure, :client_id,
                    :contract_amount, :start_date, :end_date, :status, :created_by, :engineer_id, :industry)");
                
                $created_by = $_SESSION['user_id'];
                
                $stmt->bindParam(':name', $data['name']);
                $stmt->bindParam(':description', $data['description']);
                $stmt->bindParam(':location', $data['location']);
                $stmt->bindParam(':structure', $data['structure']);
                $stmt->bindParam(':client_id', $data['client_id'], PDO::PARAM_INT);
                $stmt->bindParam(':contract_amount', $data['contract_amount']);
                $stmt->bindParam(':start_date', $data['start_date']);
                $stmt->bindParam(':end_date', $data['end_date']);
                $stmt->bindParam(':status', $data['status']);
                $stmt->bindParam(':created_by', $created_by, PDO::PARAM_INT);
                $stmt->bindParam(':engineer_id', $data['engineer_id'], PDO::PARAM_INT);
                $stmt->bindParam(':industry', $data['industry']);
                
                $stmt->execute();
                $projectId = $conn->lastInsertId();
                $message = "Project created successfully!";
            }
            
            $_SESSION['success_message'] = $message;
            header("Location: project.php?action=view&id=$projectId");
            exit();
        }
    
        // Handle image uploads
        if (isset($_FILES['project_images'])) {
            $conn->beginTransaction();
            $hasThumbnail = !empty($_POST['existing_thumbnail']);
            
            foreach ($_FILES['project_images']['tmp_name'] as $key => $tmpName) {
                if ($_FILES['project_images']['error'][$key] !== UPLOAD_ERR_OK) continue;
                
                // Verify file type and size
                $fileType = mime_content_type($tmpName);
                if (!in_array($fileType, $allowedTypes) || 
                    $_FILES['project_images']['size'][$key] > $maxFileSize) {
                    continue;
                }
                
                // Generate unique filename
                $extension = pathinfo($_FILES['project_images']['name'][$key], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $extension;
                $targetPath = $uploadDir . $filename;
                
                if (move_uploaded_file($tmpName, $targetPath)) {
                    $isThumbnail = (!$hasThumbnail && $key === 0); // First image as thumbnail if none exists
                    
                    $stmt = $conn->prepare("INSERT INTO project_images 
                                          (project_id, file_name, file_path, is_thumbnail) 
                                          VALUES (?, ?, ?, ?)");
                    $stmt->execute([
                        $projectId,
                        $_FILES['project_images']['name'][$key],
                        $filename,
                        $isThumbnail ? 1 : 0
                    ]);
                    
                    // If setting this as thumbnail, unset previous thumbnail
                    if ($isThumbnail) {
                        $conn->prepare("UPDATE project_images SET is_thumbnail = 0 
                                      WHERE project_id = ? AND id != ?")
                             ->execute([$projectId, $conn->lastInsertId()]);
                        $hasThumbnail = true;
                    }
                }
            }
            
            $conn->commit();
            $_SESSION['success_message'] = "Images uploaded successfully!";
            header("Location: project.php?action=view&id=$projectId");
            exit();
        }
        
        // Add project update
        if (isset($_POST['add_update'])) {
            $updateText = trim($_POST['update_text']);
            
            $stmt = $conn->prepare("INSERT INTO project_updates 
                                  (project_id, update_text, created_by) 
                                  VALUES (?, ?, ?)");
            $stmt->execute([
                $projectId,
                $updateText,
                $_SESSION['user_id']
            ]);
            
            $_SESSION['success_message'] = "Update added successfully!";
            header("Location: project.php?action=view&id=$projectId");
            exit();
        }
    }
    
    // Handle thumbnail setting
    if (isset($_GET['set_thumbnail'])) {
        CSRF::verifyToken($_GET['token']);
        $imageId = (int)$_GET['set_thumbnail'];
        
        $conn->beginTransaction();
        $conn->prepare("UPDATE project_images SET is_thumbnail = 0 
                      WHERE project_id = ?")
             ->execute([$projectId]);
        $conn->prepare("UPDATE project_images SET is_thumbnail = 1 
                      WHERE id = ? AND project_id = ?")
             ->execute([$imageId, $projectId]);
        $conn->commit();
        
        $_SESSION['success_message'] = "Thumbnail updated!";
        header("Location: project.php?action=view&id=$projectId");
        exit();
    }
    
    // Handle image deletion
    if (isset($_GET['delete_image'])) {
        CSRF::verifyToken($_GET['token']);
        $imageId = (int)$_GET['delete_image'];
        
        // Get file path first
        $stmt = $conn->prepare("SELECT file_path FROM project_images WHERE id = ?");
        $stmt->execute([$imageId]);
        $image = $stmt->fetch();
        
        if ($image) {
            $filePath = $uploadDir . $image['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            $conn->prepare("DELETE FROM project_images WHERE id = ?")->execute([$imageId]);
            $_SESSION['success_message'] = "Image deleted!";
        }
        
        header("Location: project.php?action=view&id=$projectId");
        exit();
    }
    
   // Handle project deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete_project') {
    try {
        // Ensure we have an ID parameter
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            throw new Exception("Missing project ID");
        }

        $projectId = (int)$_GET['id'];
        
        // Verify CSRF token
        CSRF::verifyToken($_GET['token']);
        
        // Start a transaction
        $conn->beginTransaction();
        
        // Delete the project
        $deleteStmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
        $deleteStmt->execute([$projectId]);
        
        // Check if rows were affected
        if ($deleteStmt->rowCount() == 0) {
            throw new Exception("No project found with ID: $projectId");
        }
        
        // Commit the transaction
        $conn->commit();
        
        $_SESSION['success_message'] = "Project deleted successfully!";
        header("Location: project.php");
        exit();
        
    } catch (Exception $e) {
        // Roll back the transaction on error
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        
        $_SESSION['error_message'] = "Failed to delete project: " . $e->getMessage();
        header("Location: project.php");
        exit();
    }
}

    // Get all projects for list view
    $projects = $conn->query("SELECT p.*, c.name as client_name 
                             FROM projects p
                             JOIN clients c ON p.client_id = c.id
                             ORDER BY p.created_at DESC")
                     ->fetchAll();
                     
    // Get all clients for dropdown
    $clients = $conn->query("SELECT * FROM clients ORDER BY name")->fetchAll();
    
    // Get project details for view/edit
    if (in_array($action, ['view','edit']) && $projectId > 0) {
        $stmt = $conn->prepare("SELECT p.*, c.name as client_name, c.contact_person, 
            c.email as client_email, c.phone as client_phone,
            e.name as engineer_name, e.email as engineer_email, e.phone as engineer_phone
            FROM projects p
            JOIN clients c ON p.client_id = c.id
            LEFT JOIN engineers e ON p.engineer_id = e.id
            WHERE p.id = ?");
        $stmt->execute([$projectId]);
        $project = $stmt->fetch();
        
            $stmt = $conn->prepare("SELECT * FROM project_images 
            WHERE project_id = ? 
            ORDER BY is_thumbnail DESC, uploaded_at DESC");
            $stmt->execute([$projectId]);
            $images = $stmt->fetchAll();
        
            $stmt = $conn->prepare("SELECT pu.*, u.name as created_by_name
            FROM project_updates pu
            JOIN users u ON pu.created_by = u.id
            WHERE pu.project_id = ?
            ORDER BY pu.created_at DESC");
            $stmt->execute([$projectId]);
            $updates = $stmt->fetchAll();
    }
} catch (Exception $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    $_SESSION['error_message'] = "Error: " . $e->getMessage();
    header("Location: project.php");
    exit();
}


?>

<?php
// posts.php
$page_title = "Manage Postss";
require_once 'header.php';


// Your page-specific content here
?>
    <title>Project Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .img-thumbnail {
            transition: transform 0.2s;
        }
        .img-thumbnail:hover {
            transform: scale(1.05);
        }
        .update-item {
            transition: background-color 0.2s;
        }
        .update-item:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body class="bg-light">
<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded shadow-sm">
        <h2 class="mb-0">
            <?php 
            if ($action === 'create') {
                echo 'Create Project';
            } elseif ($action === 'view' && isset($project)) {
                echo 'Project: ' . htmlspecialchars($project['name']);
            } elseif ($action === 'edit') {
                echo 'Edit Project';
            } else {
                echo 'Project Management';
            }
            ?>
        </h2>
        <div>
            <?php if ($action !== 'list'): ?>
                <a href="project.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Projects
                </a>
            <?php endif; ?>
            <?php if ($action === 'list'): ?>
                <a href="project.php?action=create" class="btn btn-primary">
                    <i class="bi bi-plus"></i> New Project
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo htmlspecialchars($_SESSION['success_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo htmlspecialchars($_SESSION['error_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <!-- Main Content -->
    <?php if ($action === 'list'): ?>
        <!-- Projects List -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Client</th>
                                <th>Engineer</th>
                                <th>Location</th>
                                <th>Contract</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($projects as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['name']); ?></td>
                                <td><?php echo htmlspecialchars($p['client_name']); ?></td>
                                <td>
                                    <?php 
                                    $stmt = $conn->prepare("SELECT name FROM engineers WHERE id = ?");
                                    $stmt->execute([$p['engineer_id']]);
                                    $engineer = $stmt->fetch();
                                    echo $engineer ? htmlspecialchars($engineer['name']) : 'Not assigned';
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($p['location']); ?></td>
                                <td>$<?php echo number_format($p['contract_amount'], 2); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        switch($p['status']) {
                                            case 'completed': echo 'success'; break;
                                            case 'in_progress': echo 'primary'; break;
                                            case 'on_hold': echo 'warning'; break;
                                            default: echo 'secondary';
                                        }
                                    ?>">
                                        <?php echo ucwords(str_replace('_', ' ', $p['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="project.php?action=view&id=<?php echo $p['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="project.php?action=edit&id=<?php echo $p['id']; ?>" 
                                           class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="project.php?action=delete_project&id=<?php echo $p['id']; ?>&token=<?php echo CSRF::generateToken(); ?>" 
                                           class="btn btn-sm btn-outline-danger" title="Delete"
                                           onclick="return confirm('Are you sure you want to delete this project?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php elseif ($action === 'view' && isset($project)): ?>
        <!-- Project View -->
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <i class="bi bi-info-circle"></i> Project Details
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($project['name']); ?></h5>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <strong><i class="bi bi-geo-alt"></i> Location:</strong> 
                                <?php echo htmlspecialchars($project['location']); ?>
                            </li>
                            <li class="list-group-item">
                                <strong><i class="bi bi-building"></i> Structure:</strong> 
                                <?php echo htmlspecialchars($project['structure']); ?>
                            </li>
                            <li class="list-group-item">
                                <strong><i class="bi bi-tag"></i> Status:</strong> 
                                <span class="badge bg-<?php 
                                    switch($project['status']) {
                                        case 'completed': echo 'success'; break;
                                        case 'in_progress': echo 'primary'; break;
                                        case 'on_hold': echo 'warning'; break;
                                        default: echo 'secondary';
                                    }
                                ?>">
                                    <?php echo ucwords(str_replace('_', ' ', $project['status'])); ?>
                                </span>
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="project.php?action=edit&id=<?php echo $projectId; ?>" class="btn btn-primary">
                            <i class="bi bi-pencil"></i> Edit Project
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <i class="bi bi-file-earmark-text"></i> Contract Details
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <strong><i class="bi bi-building"></i> Client:</strong> 
                                <?php echo htmlspecialchars($project['client_name']); ?>
                            </li>
                            <li class="list-group-item">
                                <strong><i class="bi bi-person"></i> Contact:</strong> 
                                <?php echo htmlspecialchars($project['contact_person']); ?>
                            </li>
                            <li class="list-group-item">
                                <strong><i class="bi bi-envelope"></i> Email:</strong> 
                                <a href="mailto:<?php echo htmlspecialchars($project['client_email']); ?>">
                                    <?php echo htmlspecialchars($project['client_email']); ?>
                                </a>
                            </li>
                            <li class="list-group-item">
                                <strong><i class="bi bi-telephone"></i> Phone:</strong> 
                                <?php echo htmlspecialchars($project['client_phone']); ?>
                            </li>
                            <?php if (!empty($project['engineer_name'])): ?>
                            <li class="list-group-item">
                                <strong><i class="bi bi-person-gear"></i> Assigned Engineer:</strong> 
                                <?php echo htmlspecialchars($project['engineer_name']); ?>
                            </li>
                            <li class="list-group-item">
                                <strong><i class="bi bi-envelope"></i> Engineer Email:</strong> 
                                <a href="mailto:<?php echo htmlspecialchars($project['engineer_email']); ?>">
                                    <?php echo htmlspecialchars($project['engineer_email']); ?>
                                </a>
                            </li>
                            <li class="list-group-item">
                                <strong><i class="bi bi-telephone"></i> Engineer Phone:</strong> 
                                <?php echo htmlspecialchars($project['engineer_phone']); ?>
                            </li>
                            <?php endif; ?>
                            <li class="list-group-item">
                                <strong><i class="bi bi-cash-stack"></i> Contract Amount:</strong> 
                                $<?php echo number_format($project['contract_amount'], 2); ?>
                            </li>
                            <li class="list-group-item">
                                <strong><i class="bi bi-calendar-range"></i> Timeline:</strong> 
                                <?php echo date('M j, Y', strtotime($project['start_date'])); ?> to 
                                <?php echo date('M j, Y', strtotime($project['end_date'])); ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Gallery -->
        <div class="card mt-4 shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span><i class="bi bi-images"></i> Project Images</span>
                <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#uploadImagesModal">
                    <i class="bi bi-plus"></i> Add Images
                </button>
            </div>
            <div class="card-body">
                <?php if (!empty($images)): ?>
                    <div class="row g-3">
                        <?php foreach ($images as $image): ?>
                            <div class="col-md-4 col-lg-3">
                                <div class="card h-100">
                                    <img src="../uploads/projects/<?php echo htmlspecialchars($image['file_path']); ?>" 
                                         class="card-img-top img-thumbnail" 
                                         style="height: 200px; object-fit: cover;"
                                         alt="<?php echo htmlspecialchars($image['file_name']); ?>">
                                    <div class="card-footer p-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <?php if ($image['is_thumbnail']): ?>
                                                <span class="badge bg-success">Thumbnail</span>
                                            <?php else: ?>
                                                <a href="project.php?action=view&id=<?php echo $projectId; ?>&set_thumbnail=<?php echo $image['id']; ?>&token=<?php echo CSRF::generateToken(); ?>" 
                                                   class="btn btn-sm btn-outline-secondary" title="Set as Thumbnail">
                                                   <i class="bi bi-star"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="project.php?action=view&id=<?php echo $projectId; ?>&delete_image=<?php echo $image['id']; ?>&token=<?php echo CSRF::generateToken(); ?>" 
                                               class="btn btn-sm btn-outline-danger" title="Delete"
                                               onclick="return confirm('Are you sure you want to delete this image?')">
                                               <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mb-0">No images uploaded yet</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Upload Images Modal -->
        <div class="modal fade" id="uploadImagesModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="modal-header">
                            <h5 class="modal-title">Upload Project Images</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <?php echo CSRF::tokenField(); ?>
                            <div class="mb-3">
                                <label class="form-label">Select Images (Max 5MB each)</label>
                                <input type="file" class="form-control" name="project_images[]" multiple accept="image/*">
                                <small class="text-muted">Allowed formats: JPEG, PNG, WEBP</small>
                            </div>
                            <div id="image-previews" class="row g-2 mb-3"></div>
                            <?php if (!empty($images)): ?>
                                <div class="mb-3">
                                    <label class="form-label">Current Thumbnail</label>
                                    <select class="form-select" name="existing_thumbnail">
                                        <?php foreach ($images as $img): ?>
                                            <option value="<?php echo $img['id']; ?>" <?php echo $img['is_thumbnail'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($img['file_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Upload Images</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Project Updates -->
        <div class="card mt-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-activity"></i> Project Updates
            </div>
            <div class="card-body">
                <form method="POST" class="mb-4">
                    <?php echo CSRF::tokenField(); ?>
                    <input type="hidden" name="project_id" value="<?php echo $projectId; ?>">
                    <div class="mb-3">
                        <label for="update_text" class="form-label">Add New Update</label>
                        <textarea class="form-control" id="update_text" name="update_text" rows="3" required></textarea>
                    </div>
                    <button type="submit" name="add_update" class="btn btn-primary">
                        <i class="bi bi-plus"></i> Add Update
                    </button>
                </form>
                
                <div class="updates-list">
                    <?php if (!empty($updates)): ?>
                        <?php foreach ($updates as $update): ?>
                            <div class="update-item card mb-2">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong><?php echo htmlspecialchars($update['created_by_name']); ?></strong>
                                        <small class="text-muted"><?php echo date('M j, Y g:i a', strtotime($update['created_at'])); ?></small>
                                    </div>
                                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($update['update_text'])); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-info mb-0">No updates yet</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    <?php elseif (in_array($action, ['create','edit']) && ($action !== 'edit' || isset($project))): ?>
        <!-- Project Form -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-pencil"></i> <?php echo $action === 'create' ? 'Create New Project' : 'Edit Project'; ?>
            </div>
            <div class="card-body">
                <form method="POST">
                    <?php echo CSRF::tokenField(); ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Project Name*</label>
                                <input type="text" class="form-control" name="name" 
                                       value="<?php echo isset($project['name']) ? htmlspecialchars($project['name']) : ''; ?>" 
                                       required>
                            </div>
                        </div>
                        <div class="col-md-6">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Client*</label>
                                    <select class="form-select" name="client_id" required>
                                        <option value="">Select Client</option>
                                        <?php foreach ($clients as $c): ?>
                                        <option value="<?php echo $c['id']; ?>" 
                                            <?php echo (isset($project) && $project['client_id'] == $c['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($c['name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Assigned Engineer</label>
                                    <select class="form-select" name="engineer_id">
                                        <option value="">No Engineer Assigned</option>
                                        <?php foreach ($engineers as $e): ?>
                                            <option value="<?= $e['id'] ?>"
                                                <?= (isset($project) && $project['engineer_id'] == $e['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($e['name']) ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"><?php echo isset($project['description']) ? htmlspecialchars($project['description']) : ''; ?></textarea>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" class="form-control" name="location" 
                                       value="<?php echo isset($project['location']) ? htmlspecialchars($project['location']) : ''; ?>">
                            </div>
                        </div>
                        <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Industry</label>
                                <input type="text" class="form-control" name="industry" 
                                    value="<?php echo isset($project['industry']) ? htmlspecialchars($project['industry']) : ''; ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Structure Type</label>
                                <input type="text" class="form-control" name="structure" 
                                       value="<?php echo isset($project['structure']) ? htmlspecialchars($project['structure']) : ''; ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <?php 
                                    $statuses = ['planning', 'in_progress', 'on_hold', 'completed', 'cancelled'];
                                    foreach ($statuses as $status): ?>
                                    <option value="<?php echo $status; ?>" 
                                        <?php echo (isset($project) && $project['status'] === $status) ? 'selected' : ''; ?>>
                                        <?php echo ucwords(str_replace('_', ' ', $status)); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Contract Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" class="form-control" name="contract_amount" 
                                           value="<?php echo isset($project['contract_amount']) ? number_format($project['contract_amount'], 2) : ''; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" name="start_date" 
                                       value="<?php echo isset($project['start_date']) ? $project['start_date'] : ''; ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" name="end_date" 
                                       value="<?php echo isset($project['end_date']) ? $project['end_date'] : ''; ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end mt-3">
                        <a href="project.php<?php echo $action === 'edit' ? '?action=view&id='.$projectId : ''; ?>" 
                           class="btn btn-outline-secondary me-2">
                            Cancel
                        </a>
                        <button type="submit" name="save_project" class="btn btn-primary">
                            <?php echo $action === 'create' ? 'Create Project' : 'Save Changes'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Image preview functionality
document.querySelector('input[name="project_images[]"]')?.addEventListener('change', function(e) {
    const files = e.target.files;
    const previewContainer = document.getElementById('image-previews');
    previewContainer.innerHTML = '';
    
    for (let i = 0; i < files.length; i++) {
        if (!files[i].type.match('image.*')) continue;
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const col = document.createElement('div');
            col.className = 'col-6 col-md-4 col-lg-3';
            col.innerHTML = `
                <div class="card">
                    <img src="${e.target.result}" class="card-img-top" style="height: 100px; object-fit: cover;">
                    <div class="card-body p-2">
                        <small class="text-muted d-block text-truncate">${files[i].name}</small>
                        <small class="text-muted">${(files[i].size / 1024 / 1024).toFixed(2)} MB</small>
                    </div>
                </div>`;
            previewContainer.appendChild(col);
        }
        reader.readAsDataURL(files[i]);
    }
});

// Format currency input
document.querySelector('input[name="contract_amount"]')?.addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
});
</script>
<?php
require_once 'footer.php';
?>