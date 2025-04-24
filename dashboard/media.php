<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH . '/config/db.php';
require_once ROOT_PATH . '/helpers/AuthMiddleware.php';
require_once ROOT_PATH . '/helpers/Auth.php';
require_once ROOT_PATH . '/helpers/CSRF.php';

Auth::requirePermission('manage_media');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_upload'])) {
    header('Content-Type: application/json');
    
    try {
        CSRF::verifyToken($_POST['csrf_token']);
        
        $uploadDir = '../uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        $response = [];
        
        if (isset($_FILES['files'])) {
            $conn->beginTransaction();
            
            foreach ($_FILES['files']['name'] as $key => $name) {
                $fileError = $_FILES['files']['error'][$key];
                $tmpName = $_FILES['files']['tmp_name'][$key];
                $fileSize = $_FILES['files']['size'][$key];
                $fileType = $_FILES['files']['type'][$key];
                
                // Skip if error occurred (except if no file was selected)
                if ($fileError !== UPLOAD_ERR_OK && $fileError !== UPLOAD_ERR_NO_FILE) {
                    $response['errors'][] = "Error uploading $name: " . $this->getUploadError($fileError);
                    continue;
                }
                
                // Skip if no file was uploaded
                if ($fileError === UPLOAD_ERR_NO_FILE) {
                    continue;
                }
                
                // Validate file size
                if ($fileSize > $maxFileSize) {
                    $response['errors'][] = "File too large: $name (max 5MB allowed)";
                    continue;
                }
                
                $originalName = basename($name);
                $fileExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                
                if (!in_array($fileExt, $allowedExtensions)) {
                    $response['errors'][] = "Invalid file type: $name";
                    continue;
                }
                
                // Generate unique filename
                $filename = uniqid() . '.' . $fileExt;
                $targetPath = $uploadDir . $filename;
                
                if (move_uploaded_file($tmpName, $targetPath)) {
                    $stmt = $conn->prepare("INSERT INTO media 
                                          (file_name, file_path, file_type, uploaded_by) 
                                          VALUES (?, ?, ?, ?)");
                    $stmt->execute([
                        $originalName,
                        $filename,
                        $fileType,
                        $_SESSION['user_id']
                    ]);
                    
                    $fileId = $conn->lastInsertId();
                    
                    // Get the newly uploaded file info for the response
                    $stmt = $conn->prepare("SELECT m.*, u.name as uploaded_by_name 
                                          FROM media m 
                                          JOIN users u ON m.uploaded_by = u.id 
                                          WHERE m.id = ?");
                    $stmt->execute([$fileId]);
                    $fileData = $stmt->fetch();
                    
                    $response['files'][] = [
                        'id' => $fileId,
                        'name' => $originalName,
                        'path' => $filename,
                        'type' => $fileType,
                        'uploaded_by' => $fileData['uploaded_by_name'],
                        'uploaded_at' => $fileData['uploaded_at']
                    ];
                } else {
                    $response['errors'][] = "Failed to move uploaded file: $name";
                }
            }
            
            $conn->commit();
            $response['success'] = true;
            $response['message'] = count($_FILES['files']['name']) . " files uploaded successfully!";
        } else {
            $response['success'] = false;
            $response['message'] = "No files were selected for upload.";
        }
    } catch (Exception $e) {
        $conn->rollBack();
        $response['success'] = false;
        $response['message'] = "Error uploading files: " . $e->getMessage();
    }
    
    echo json_encode($response);
    exit();
}
// Handle file deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    CSRF::verifyToken($_POST['csrf_token']);
    
    try {
        // Check if file is used in any posts
        $stmt = $conn->prepare("SELECT COUNT(*) FROM posts WHERE featured_image = ?");
        $stmt->execute([$_POST['file_id']]);
        $usedInPosts = $stmt->fetchColumn();
        
        // Check if file is used in any project images
        $stmt = $conn->prepare("SELECT COUNT(*) FROM project_images WHERE file_path = 
                               (SELECT file_path FROM media WHERE id = ?)");
        $stmt->execute([$_POST['file_id']]);
        $usedInProjects = $stmt->fetchColumn();
        
        if ($usedInPosts > 0 || $usedInProjects > 0) {
            $_SESSION['error_message'] = "Cannot delete file: It's being used in posts or projects.";
            header("Location: media.php");
            exit();
        }
        
        // Get file info
        $stmt = $conn->prepare("SELECT file_path FROM media WHERE id = ?");
        $stmt->execute([$_POST['file_id']]);
        $file = $stmt->fetch();
        
        if ($file) {
            $filePath = '../uploads/' . $file['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            $stmt = $conn->prepare("DELETE FROM media WHERE id = ?");
            $stmt->execute([$_POST['file_id']]);
            
            $_SESSION['success_message'] = "File deleted successfully!";
        } else {
            $_SESSION['error_message'] = "File not found in database.";
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error deleting file: " . $e->getMessage();
    }
    
    header("Location: media.php");
    exit();
}

// Get all media files with pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Get total count
$totalFiles = $conn->query("SELECT COUNT(*) FROM media")->fetchColumn();
$totalPages = ceil($totalFiles / $perPage);

// Get files with user info
$mediaFiles = $conn->query("SELECT m.*, u.name as uploaded_by_name 
                           FROM media m 
                           JOIN users u ON m.uploaded_by = u.id 
                           ORDER BY m.uploaded_at DESC
                           LIMIT $perPage OFFSET $offset")
                   ->fetchAll();

// Get file usage counts
$usageCounts = [];
$stmt = $conn->query("SELECT featured_image, COUNT(*) as count FROM posts WHERE featured_image IS NOT NULL GROUP BY featured_image");
while ($row = $stmt->fetch()) {
    $usageCounts[$row['featured_image']] = $row['count'];
}
?>

<?php
// posts.php
$page_title = "Manage Media";
require_once 'header.php';


// Your page-specific content here
?>

<div class="container-fluid mt-4">
    <h1>Manage Posts</h1>
    <title>Media Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .media-card {
            transition: transform 0.2s;
        }
        .media-card:hover {
            transform: scale(1.02);
        }
        .file-used {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(0,0,0,0.7);
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }
        .pagination {
            justify-content: center;
        }
        .dropzone {
            border: 2px dashed #ccc;
            border-radius: 5px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s;
        }
        .dropzone.active {
            border-color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.05);
        }
        .upload-progress {
            margin-top: 15px;
        }
        .progress-bar {
            transition: width 0.3s ease;
        }
        .file-list {
            max-height: 200px;
            overflow-y: auto;
            margin-top: 15px;
        }
        .file-item {
            display: flex;
            justify-content: space-between;
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        .file-item.success {
            background-color: rgba(25, 135, 84, 0.1);
        }
        .file-item.error {
            background-color: rgba(220, 53, 69, 0.1);
        }
        .file-status {
            margin-left: 10px;
        }
    </style>

<div class="container mt-4">
    
    <!-- Display messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_SESSION['success_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $_SESSION['error_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    
    <!-- Upload Form -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-cloud-upload-alt me-2"></i>Upload Files
        </div>
        <div class="card-body">
            <form id="ajaxUploadForm" method="POST" enctype="multipart/form-data">
                <?= CSRF::tokenField() ?>
                <input type="hidden" name="ajax_upload" value="1">
                
                <div class="dropzone" id="dropzone">
                    <div class="dz-message">
                        <i class="fas fa-cloud-upload-alt fa-3x mb-3 text-muted"></i>
                        <h5>Drag & Drop files here or click to browse</h5>
                        <p class="text-muted">Supported formats: JPG, PNG, GIF, WEBP, SVG (Max 5MB each)</p>
                        <button type="button" class="btn btn-outline-primary mt-2" id="browseBtn">
                            <i class="fas fa-folder-open me-2"></i>Select Files
                        </button>
                        <input type="file" name="files[]" id="fileInput" multiple 
                               style="display: none;" accept=".jpg,.jpeg,.png,.gif,.webp,.svg">
                    </div>
                    
                    <div class="upload-progress" id="uploadProgress" style="display: none;">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Uploading files...</span>
                            <span id="progressPercent">0%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" id="progressBar" role="progressbar" 
                                 style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    
                    <div class="file-list" id="fileList"></div>
                </div>
                
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary" id="uploadBtn" disabled>
                        <i class="fas fa-upload me-2"></i>Start Upload
                    </button>
                    <button type="button" class="btn btn-outline-secondary ms-2" id="clearBtn" disabled>
                        <i class="fas fa-times me-2"></i>Clear Selection
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Media Gallery -->
    <div class="row">
        <?php foreach ($mediaFiles as $file): 
            $fileExt = strtolower(pathinfo($file['file_path'], PATHINFO_EXTENSION));
            $isImage = in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
            $isUsed = isset($usageCounts[$file['id']]);
        ?>
        <div class="col-md-3 mb-4">
            <div class="card h-100 media-card">
                <?php if ($isImage): ?>
                <img src="../uploads/<?= htmlspecialchars($file['file_path']) ?>" 
                     class="card-img-top" alt="<?= htmlspecialchars($file['file_name']) ?>"
                     style="height: 150px; object-fit: contain; background: #f8f9fa;">
                <?php else: ?>
                <div class="card-body text-center" style="height: 150px; display: flex; flex-direction: column; justify-content: center;">
                    <i class="fas fa-file-alt fa-5x text-secondary mb-2"></i>
                    <h5 class="card-title"><?= htmlspecialchars($file['file_name']) ?></h5>
                </div>
                <?php endif; ?>
                
                <?php if ($isUsed): ?>
                <div class="file-used" title="Used in <?= $usageCounts[$file['id']] ?> post(s)">
                    <?= $usageCounts[$file['id']] ?>
                </div>
                <?php endif; ?>
                
                <div class="card-footer">
                    <small class="text-muted d-block">
                        <i class="fas fa-user me-1"></i><?= htmlspecialchars($file['uploaded_by_name']) ?>
                    </small>
                    <small class="text-muted d-block">
                        <i class="fas fa-calendar me-1"></i><?= date('M j, Y H:i', strtotime($file['uploaded_at'])) ?>
                    </small>
                    <div class="mt-2 d-flex justify-content-between">
                        <a href="../uploads/<?= htmlspecialchars($file['file_path']) ?>" 
                           download class="btn btn-sm btn-outline-primary" title="Download">
                            <i class="fas fa-download"></i>
                        </a>
                        
                        <form method="POST" class="d-inline">
                            <?= CSRF::tokenField() ?>
                            <input type="hidden" name="delete" value="1">
                            <input type="hidden" name="file_id" value="<?= $file['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                    title="Delete" <?= $isUsed ? 'disabled' : '' ?>
                                    onclick="return confirm('Are you sure you want to delete this file?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        
                        <?php if ($isImage): ?>
                        <button class="btn btn-sm btn-outline-secondary copy-link" 
                                title="Copy URL"
                                data-link="<?= htmlspecialchars('/uploads/' . $file['file_path']) ?>">
                            <i class="fas fa-copy"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <nav aria-label="Page navigation">
        <ul class="pagination mt-4">
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
            </li>
            
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
            <?php endfor; ?>
            
            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('fileInput');
    const browseBtn = document.getElementById('browseBtn');
    const uploadBtn = document.getElementById('uploadBtn');
    const clearBtn = document.getElementById('clearBtn');
    const fileList = document.getElementById('fileList');
    const uploadProgress = document.getElementById('uploadProgress');
    const progressBar = document.getElementById('progressBar');
    const progressPercent = document.getElementById('progressPercent');
    const form = document.getElementById('ajaxUploadForm');
    
    // State variables
    let filesToUpload = [];
    let isUploading = false;
    let uploadController = null;
    let lastFileSelectionTime = 0;

    // Handle browse button click
    browseBtn.addEventListener('click', function() {
        fileInput.value = ''; // Reset input to allow same file selection
        fileInput.click();
    });
    
    // Handle file selection with duplicate prevention
    fileInput.addEventListener('change', function(e) {
        const now = Date.now();
        
        // Prevent multiple rapid fire change events
        if (now - lastFileSelectionTime < 500) {
            return;
        }
        lastFileSelectionTime = now;
        
        if (this.files && this.files.length > 0) {
            // Create a new array to avoid reference issues
            const newFiles = Array.from(this.files);
            
            // Filter out duplicates by name, size and lastModified
            const uniqueFiles = newFiles.filter(newFile => 
                !filesToUpload.some(existingFile => 
                    existingFile.name === newFile.name && 
                    existingFile.size === newFile.size &&
                    existingFile.lastModified === newFile.lastModified
                )
            );
            
            if (uniqueFiles.length > 0) {
                filesToUpload = [...filesToUpload, ...uniqueFiles];
                renderFileList();
                updateButtonStates();
            } else {
                showAlert('warning', 'No new files to add (duplicates filtered out)');
            }
        }
    });
    
    // Drag and drop handlers (unchanged as it works properly)
    dropzone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('active');
    });
    
    dropzone.addEventListener('dragleave', function() {
        this.classList.remove('active');
    });
    
    dropzone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('active');
        
        if (e.dataTransfer.files.length > 0) {
            const newFiles = Array.from(e.dataTransfer.files);
            filesToUpload = [...filesToUpload, ...newFiles];
            renderFileList();
            updateButtonStates();
        }
    });
    
    // Clear selection
    clearBtn.addEventListener('click', function() {
        if (isUploading) {
            abortUpload();
        }
        resetFileSelection();
    });
    
    // Form submission with enhanced duplicate prevention
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (isUploading || filesToUpload.length === 0) return;
        
        startUpload();
        
        try {
            // Create a clean copy of files to upload
            const filesCopy = [...filesToUpload];
            
            // Upload the files
            const response = await uploadFiles(filesCopy);
            
            // Handle the response
            handleUploadResponse(response);
            
            // Only reset if upload was successful
            if (response.success) {
                resetFileSelection();
            }
        } catch (error) {
            handleUploadError(error);
        } finally {
            finishUpload();
        }
    });
    
    // =====================
    // Core Upload Functions
    // =====================
    
    function uploadFiles(files) {
        return new Promise((resolve, reject) => {
            const formData = new FormData(form);
            
            // Add files to FormData with unique identifiers
            files.forEach((file, index) => {
                formData.append(`files[${index}]`, file, file.name);
            });
            
            uploadController = new AbortController();
            
            const xhr = new XMLHttpRequest();
            xhr.open('POST', form.action, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    updateProgress(percent);
                }
            });
            
            xhr.onload = function() {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (xhr.status >= 200 && xhr.status < 300) {
                        resolve(response);
                    } else {
                        reject(response);
                    }
                } catch (e) {
                    reject({ message: 'Error parsing server response' });
                }
            };
            
            xhr.onerror = function() {
                reject({ message: 'Network error occurred' });
            };
            
            xhr.onabort = function() {
                reject({ message: 'Upload cancelled' });
            };
            
            xhr.send(formData);
        });
    }
    
    // =====================
    // UI Helper Functions
    // =====================
    
    function renderFileList() {
        fileList.innerHTML = '';
        
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
        const maxSize = 5 * 1024 * 1024; // 5MB
        
        filesToUpload.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item d-flex justify-content-between align-items-center';
            
            // File info
            const fileInfo = document.createElement('span');
            fileInfo.innerHTML = `<i class="far fa-file-image text-primary me-2"></i>${file.name}`;
            
            // File actions
            const fileActions = document.createElement('div');
            fileActions.className = 'd-flex align-items-center';
            
            // File status
            const fileStatus = document.createElement('span');
            fileStatus.className = 'text-muted me-2';
            fileStatus.textContent = formatFileSize(file.size);
            
            // Remove button
            const removeBtn = document.createElement('button');
            removeBtn.className = 'btn btn-sm btn-outline-danger';
            removeBtn.innerHTML = '<i class="fas fa-times"></i>';
            removeBtn.addEventListener('click', (e) => {
                e.preventDefault();
                removeFile(index);
            });
            
            // Validation
            if (!allowedTypes.includes(file.type) && !file.name.match(/\.(jpe?g|png|gif|webp|svg)$/i)) {
                fileItem.className += ' error';
                fileInfo.innerHTML = `<i class="fas fa-times-circle text-danger me-2"></i>${file.name}`;
                fileStatus.className = 'text-danger';
                fileStatus.textContent = 'Invalid type';
            } else if (file.size > maxSize) {
                fileItem.className += ' error';
                fileInfo.innerHTML = `<i class="fas fa-times-circle text-danger me-2"></i>${file.name}`;
                fileStatus.className = 'text-danger';
                fileStatus.textContent = 'Too large';
            }
            
            fileActions.append(fileStatus, removeBtn);
            fileItem.append(fileInfo, fileActions);
            fileList.appendChild(fileItem);
        });
    }
    
    function removeFile(index) {
        filesToUpload.splice(index, 1);
        renderFileList();
        updateButtonStates();
    }
    
    function startUpload() {
        isUploading = true;
        uploadProgress.style.display = 'block';
        progressBar.style.width = '0%';
        progressPercent.textContent = '0%';
        uploadBtn.disabled = true;
        clearBtn.disabled = true;
        browseBtn.disabled = true;
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Uploading...';
    }
    
    function finishUpload() {
        isUploading = false;
        uploadProgress.style.display = 'none';
        uploadBtn.innerHTML = '<i class="fas fa-upload me-2"></i>Upload';
        browseBtn.disabled = false;
        updateButtonStates();
    }
    
    function abortUpload() {
        if (uploadController) {
            uploadController.abort();
        }
        showAlert('info', 'Upload cancelled');
        finishUpload();
    }
    
    function resetFileSelection() {
        filesToUpload = [];
        fileInput.value = '';
        fileList.innerHTML = '';
        updateButtonStates();
    }
    
    function updateButtonStates() {
        const hasValidFiles = filesToUpload.some(file => 
            ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'].includes(file.type) || 
            file.name.match(/\.(jpe?g|png|gif|webp|svg)$/i)
        );
        
        uploadBtn.disabled = !hasValidFiles || isUploading;
        clearBtn.disabled = filesToUpload.length === 0 || isUploading;
    }
    
    function updateProgress(percent) {
        progressBar.style.width = percent + '%';
        progressPercent.textContent = percent + '%';
    }
    
    function handleUploadResponse(response) {
        if (response.success) {
            showAlert('success', response.message || 'Files uploaded successfully!');
            
            // Refresh after delay if files were uploaded
            if (response.files?.length > 0) {
                setTimeout(() => window.location.reload(), 1500);
            }
        } else {
            if (response.errors?.length > 0) {
                response.errors.forEach(error => {
                    showAlert('danger', error);
                });
            } else {
                showAlert('danger', response.message || 'Error uploading files');
            }
        }
    }
    
    function handleUploadError(error) {
        console.error('Upload error:', error);
        showAlert('danger', error.message || 'An error occurred during upload');
    }
    
    function showAlert(type, message) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} mt-3`;
        alert.textContent = message;
        
        // Remove any existing alerts first
        const existingAlerts = fileList.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());
        
        fileList.appendChild(alert);
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});
</script>
</div>

<?php
require_once 'footer.php';
?>