<?php
// Start session with strict mode
session_start([
    'use_strict_mode' => true,
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
require_once ROOT_PATH . '/helpers/SlugGenerator.php';

// Verify database connection
if (!isset($conn) || !$conn instanceof PDO) {
    error_log("Database connection failed");
    die("Database connection error. Please try again later.");
}

// Check permissions
Auth::requirePermission('manage_posts');

// Handle post actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        CSRF::verifyToken($_POST['csrf_token']);
        
        if (isset($_POST['create_post'])) {
            // Validate and sanitize inputs
            $title = trim(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING));
            $content = trim(filter_input(INPUT_POST, 'content', FILTER_UNSAFE_RAW));
            $status = in_array($_POST['status'], ['draft', 'published', 'archived']) ? $_POST['status'] : 'draft';
            $categories = isset($_POST['categories']) ? array_filter($_POST['categories'], 'is_numeric') : [];
            $featured_image = isset($_POST['featured_image']) && is_numeric($_POST['featured_image']) ? (int)$_POST['featured_image'] : null;
            
            // Validate required fields
            if (empty($title) || mb_strlen($title) > 255) {
                throw new Exception("Title must be between 1-255 characters");
            }
            
            if (empty($content)) {
                throw new Exception("Content is required");
            }
            
            // Generate unique slug
            $slug = SlugGenerator::generate($title, 'posts', 'slug');
            $excerpt = substr(strip_tags($content), 0, 200);
            
            $conn->beginTransaction();
            
            // Insert post
            $stmt = $conn->prepare("INSERT INTO posts 
                                   (title, slug, content, excerpt, status, user_id, featured_image) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $title, 
                $slug, 
                $content, 
                $excerpt, 
                $status, 
                $_SESSION['user_id'],
                $featured_image
            ]);
            $postId = $conn->lastInsertId();
            
            // Add categories if any
            if (!empty($categories)) {
                $categoryStmt = $conn->prepare("INSERT INTO post_categories (post_id, category_id) VALUES (?, ?)");
                foreach ($categories as $categoryId) {
                    $categoryStmt->execute([$postId, $categoryId]);
                }
            }
            
            $conn->commit();
            
            // Clear form data from session
            unset($_SESSION['form_data']);
            
            // Set success message and redirect
            $_SESSION['success_message'] = "Post created successfully!";
            header("Location: edit_post.php?id=" . $postId);
            exit();
        }
    } catch (Exception $e) {
        if (isset($conn) && $conn->inTransaction()) {
            $conn->rollBack();
        }
        
        // Store form data in session for repopulation
        $_SESSION['form_data'] = $_POST;
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
        
        // Log the error
        error_log("Post creation error: " . $e->getMessage());
        
        // Redirect back to form
        header("Location: posts.php");
        exit();
    }
}

// Get all posts with pagination and optional filters
$currentPage = max(1, isset($_GET['page']) ? (int)$_GET['page'] : 1);
$perPage = 10;
$offset = ($currentPage - 1) * $perPage;

// Build base query
$baseQuery = "SELECT p.*, u.name as author_name, 
             (SELECT GROUP_CONCAT(c.name SEPARATOR ', ') 
              FROM post_categories pc 
              JOIN categories c ON pc.category_id = c.id 
              WHERE pc.post_id = p.id) as category_names
             FROM posts p 
             JOIN users u ON p.user_id = u.id";

// Initialize
$where = [];
$params = [];
$types = [];

// Status filter
if (!empty($_GET['status']) && in_array($_GET['status'], ['draft', 'published', 'archived'])) {
    $where[] = "p.status = ?";
    $params[] = $_GET['status'];
    $types[] = PDO::PARAM_STR;
}

// Search filter
if (!empty($_GET['search'])) {
    $where[] = "(p.title LIKE ? OR p.content LIKE ?)";
    $params[] = '%' . $_GET['search'] . '%';
    $params[] = '%' . $_GET['search'] . '%';
    $types[] = PDO::PARAM_STR;
    $types[] = PDO::PARAM_STR;
}

// Build final query
$query = $baseQuery;
$countQuery = "SELECT COUNT(*) FROM posts p";

if (!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
    $countQuery .= " WHERE " . implode(" AND ", $where);
}

$query .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;
$types[] = PDO::PARAM_INT;
$types[] = PDO::PARAM_INT;

try {
    // Get total count
    $countStmt = $conn->prepare($countQuery);
    foreach ($params as $i => $param) {
        if ($i < (count($params) - 2)) { // Skip LIMIT/OFFSET for count
            $countStmt->bindValue($i+1, $param, $types[$i]);
        }
    }
    $countStmt->execute();
    $totalPosts = $countStmt->fetchColumn();
    
    // Get posts
    $stmt = $conn->prepare($query);
    foreach ($params as $i => $param) {
        $stmt->bindValue($i+1, $param, $types[$i]);
    }
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $totalPages = max(1, ceil($totalPosts / $perPage));
    
} catch (PDOException $e) {
    error_log("Post Query Error: " . $e->getMessage());
    $_SESSION['error_message'] = "Database error occurred. Please try again.";
    $posts = [];
    $totalPages = 1;
}

// Get all categories for dropdown
$categories = [];
try {
    $categories = $conn->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();
} catch (PDOException $e) {
    error_log("Categories load error: " . $e->getMessage());
    $_SESSION['error_message'] = "Error loading categories.";
}

// Get media for featured image selection
$media = [];
try {
    $media = $conn->query("SELECT id, file_path, file_name FROM media WHERE file_type LIKE 'image/%' ORDER BY uploaded_at DESC")->fetchAll();
} catch (PDOException $e) {
    error_log("Media load error: " . $e->getMessage());
    $_SESSION['error_message'] = "Error loading media files.";
}
?>
<?php
    $allParams = $_GET;
    $allParams['status'] = 'all';
    unset($allParams['page']);

    $publishedParams = $_GET;
    $publishedParams['status'] = 'published';
    unset($publishedParams['page']);

    $draftParams = $_GET;
    $draftParams['status'] = 'draft';
    unset($draftParams['page']);

    $archivedParams = $_GET;
    $archivedParams['status'] = 'archived';
    unset($archivedParams['page']);
?>
<?php
// posts.php
$page_title = "Manage Postss";
require_once 'header.php';


// Your page-specific content here
?>

<div class="container-fluid mt-4">
    <title>Manage Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .table-responsive {
            overflow-x: auto;
        }
        .status-badge {
            font-size: 0.85rem;
        }
        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .category-checkboxes {
            max-height: 200px;
            overflow-y: auto;
        }
        .post-title {
            font-weight: 500;
        }
        .search-box {
            max-width: 300px;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <!-- Display messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['success_message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['error_message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <h2 class="mb-0">Manage Posts</h2>
            
            <div class="d-flex flex-column flex-md-row gap-2">
                <!-- Search Form -->
                <form method="GET" class="d-flex search-box">
                    <input type="text" name="search" class="form-control" placeholder="Search posts..." 
                           value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                    <button type="submit" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                
                <!-- Create Post Button -->
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPostModal">
                    <i class="fas fa-plus me-2"></i>Create Post
                </button>
            </div>
        </div>

        <!-- Status Filter Tabs -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
            <a class="nav-link <?= !isset($_GET['status']) || $_GET['status'] === 'all' ? 'active' : '' ?>" 
            href="?<?= http_build_query($allParams) ?>">All</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= isset($_GET['status']) && $_GET['status'] === 'published' ? 'active' : '' ?>" 
                href="?<?= http_build_query($publishedParams) ?>">Published</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= isset($_GET['status']) && $_GET['status'] === 'draft' ? 'active' : '' ?>" 
                href="?<?= http_build_query($draftParams) ?>">Drafts</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= isset($_GET['status']) && $_GET['status'] === 'archived' ? 'active' : '' ?>" 
                href="?<?= http_build_query($archivedParams) ?>">Archived</a>
            </li>
        </ul>

        <!-- Posts Table -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Categories</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($posts)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">No posts found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td>
                                        <a href="edit_post.php?id=<?= $post['id'] ?>" class="post-title text-decoration-none">
                                            <?= htmlspecialchars($post['title']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($post['author_name']) ?></td>
                                    <td>
                                        <?php if (!empty($post['category_names'])): ?>
                                            <?= htmlspecialchars($post['category_names']) ?>
                                        <?php else: ?>
                                            <span class="text-muted">None</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge status-badge bg-<?= 
                                            $post['status'] === 'published' ? 'success' : 
                                            ($post['status'] === 'draft' ? 'warning' : 'secondary') 
                                        ?>">
                                            <?= ucfirst($post['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('M j, Y', strtotime($post['created_at'])) ?></td>
                                    <td>
                                        <div class="d-flex action-buttons gap-2">
                                            <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="delete_post.php" class="d-inline">
                                                <?= CSRF::tokenField(); ?>
                                                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this post?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            <?php if ($post['status'] === 'published'): ?>
                                                <a href="../post/<?= $post['slug'] ?>" class="btn btn-sm btn-outline-success" title="View" target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage - 1])) ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == 1 || $i == $totalPages || ($i >= $currentPage - 2 && $i <= $currentPage + 2)): ?>
                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php elseif ($i == $currentPage - 3 || $i == $currentPage + 3): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage + 1])) ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>

        <!-- Create Post Modal -->
        <div class="modal fade" id="createPostModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" id="postForm">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Create New Post</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <?= CSRF::tokenField(); ?>
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title *</label>
                                        <input type="text" class="form-control" id="title" name="title" required
                                               value="<?= isset($_SESSION['form_data']['title']) ? htmlspecialchars($_SESSION['form_data']['title']) : '' ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="content" class="form-label">Content *</label>
                                        <textarea class="form-control" id="content" name="content" rows="15" required><?= 
                                            isset($_SESSION['form_data']['content']) ? htmlspecialchars($_SESSION['form_data']['content']) : '' 
                                        ?></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="sticky-top" style="top: 20px;">
                                        <div class="card mb-3">
                                            <div class="card-header">
                                                <h6 class="mb-0">Publish</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label for="status" class="form-label">Status *</label>
                                                    <select class="form-select" id="status" name="status" required>
                                                        <option value="draft" <?= isset($_SESSION['form_data']['status']) && $_SESSION['form_data']['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                                                        <option value="published" <?= isset($_SESSION['form_data']['status']) && $_SESSION['form_data']['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                                                        <option value="archived" <?= isset($_SESSION['form_data']['status']) && $_SESSION['form_data']['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
                                                    </select>
                                                </div>
                                                <button type="submit" name="create_post" class="btn btn-primary w-100">
                                                    <i class="fas fa-save me-2"></i>Save Post
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="card mb-3">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">Featured Image</h6>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#mediaLibraryModal">
                                                    <i class="fas fa-images"></i>
                                                </button>
                                            </div>
                                            <div class="card-body">
                                                <select class="form-select" name="featured_image" id="featured_image">
                                                    <option value="">None</option>
                                                    <?php foreach ($media as $item): ?>
                                                    <option value="<?= $item['id'] ?>" <?= isset($_SESSION['form_data']['featured_image']) && $_SESSION['form_data']['featured_image'] == $item['id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($item['file_path']) ?>
                                                    </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <div id="featuredImagePreview" class="mt-3 text-center">
                                                    <?php if (isset($_SESSION['form_data']['featured_image'])): ?>
                                                        <?php 
                                                        $selectedImage = array_filter($media, function($img) {
                                                            return $img['id'] == $_SESSION['form_data']['featured_image'];
                                                        });
                                                        if (!empty($selectedImage)): 
                                                            $selectedImage = reset($selectedImage);
                                                        ?>
                                                            <img src="../uploads/<?= htmlspecialchars($selectedImage['file_npath']) ?>" 
                                                                 class="img-fluid rounded" style="max-height: 150px;">
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="mb-0">Categories</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="category-checkboxes">
                                                    <?php if (empty($categories)): ?>
                                                        <div class="alert alert-info mb-0">No categories found</div>
                                                    <?php else: ?>
                                                        <?php foreach ($categories as $category): ?>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="categories[]" value="<?= $category['id'] ?>" 
                                                                   id="cat-<?= $category['id'] ?>"
                                                                   <?= isset($_SESSION['form_data']['categories']) && in_array($category['id'], $_SESSION['form_data']['categories']) ? 'checked' : '' ?>>
                                                            <label class="form-check-label" for="cat-<?= $category['id'] ?>">
                                                                <?= htmlspecialchars($category['name']) ?>
                                                            </label>
                                                        </div>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Media Library Modal -->
        <div class="modal fade" id="mediaLibraryModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Media Library</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row" id="mediaLibraryGrid">
                            <?php foreach ($media as $item): ?>
                            <div class="col-6 col-md-4 col-lg-3 mb-3 media-item">
                                <div class="card h-100">
                                    <img src="../uploads/<?= htmlspecialchars($item['file_path']) ?>" 
                                         class="card-img-top" alt="<?= htmlspecialchars($item['file_path']) ?>"
                                         style="height: 120px; object-fit: cover;">
                                    <div class="card-body p-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-truncate"><?= htmlspecialchars($item['file_name']) ?></small>
                                            <button type="button" class="btn btn-sm btn-outline-primary select-media" 
                                                    data-id="<?= $item['id'] ?>" data-filename="<?= htmlspecialchars($item['file_path']) ?>">
                                                Select
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.tiny.cloud/1/86lxaz73pf34yrep69198baeuf8v967eq7t3xi46rc5cslzx/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize TinyMCE
        tinymce.init({
            selector: '#content',
            plugins: 'link image table code media lists autolink charmap preview anchor',
            toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | link image media | code',
            height: 600,
            content_style: 'body { font-family: Arial, sans-serif; font-size: 16px; }',
            images_upload_url: 'upload_image.php',
            automatic_uploads: true,
            image_caption: true,
            image_advtab: true,
            file_picker_types: 'image media',
            relative_urls: false,
            convert_urls: false
            setup: function(editor) {
            // Disable HTML5 validation for the editor
            editor.on('init', function() {
                this.getContainer().querySelector('textarea').removeAttribute('required');
            });
        }
        });

        // Media library selection
        document.querySelectorAll('.select-media').forEach(button => {
            button.addEventListener('click', function() {
                const mediaId = this.getAttribute('data-id');
                const filename = this.getAttribute('data-filename');
                
                // Update featured image select
                document.getElementById('featured_image').value = mediaId;
                
                // Update preview
                const previewDiv = document.getElementById('featuredImagePreview');
                previewDiv.innerHTML = `<img src="../uploads/${filename}" class="img-fluid rounded" style="max-height: 150px;">`;
                
                // Close media modal
                const mediaModal = bootstrap.Modal.getInstance(document.getElementById('mediaLibraryModal'));
                mediaModal.hide();
            });
        });

        // Live preview of featured image
        document.getElementById('featured_image').addEventListener('change', function() {
            const previewDiv = document.getElementById('featuredImagePreview');
            const selectedOption = this.options[this.selectedIndex];
            
            if (this.value === '') {
                previewDiv.innerHTML = '';
            } else {
                const filename = selectedOption.text;
                previewDiv.innerHTML = `<img src="../uploads/${filename}" class="img-fluid rounded" style="max-height: 150px;">`;
            }
        });

        // Form validation
        document.getElementById('postForm').addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const content = tinyMCE.get('content').getContent().trim();
            
            if (title === '' || content === '') {
                e.preventDefault();
                alert('Title and content are required');
            }
        });
    });
    </script>

<?php
require_once 'footer.php';
?>