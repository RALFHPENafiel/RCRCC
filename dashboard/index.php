<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../index.php");
    exit();
}

// Database connection
require_once '../config/db.php';

// Get statistics
$stats = [];
try {
    // Total posts
    $stmt = $conn->query("SELECT COUNT(*) as total_posts FROM posts WHERE status = 'published'");
    $stats['posts'] = $stmt->fetch()['total_posts'];
    
    // Total users
    $stmt = $conn->query("SELECT COUNT(*) as total_users FROM users WHERE is_active = 1");
    $stats['users'] = $stmt->fetch()['total_users'];
    
    // Total media
    $stmt = $conn->query("SELECT COUNT(*) as total_media FROM media");
    $stats['media'] = $stmt->fetch()['total_media'];
    
    // Total projects
    $stmt = $conn->query("SELECT COUNT(*) as total_projects FROM projects");
    $stats['projects'] = $stmt->fetch()['total_projects'];
    
    // Project status breakdown
    $stmt = $conn->query("SELECT status, COUNT(*) as count FROM projects GROUP BY status");
    $projectStatus = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Recent posts
    $stmt = $conn->query("SELECT id, title, created_at FROM posts ORDER BY created_at DESC LIMIT 5");
    $recentPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Recent projects
    $stmt = $conn->query("SELECT p.id, p.name, p.status, c.name as client_name 
                         FROM projects p 
                         JOIN clients c ON p.client_id = c.id 
                         ORDER BY p.created_at DESC LIMIT 5");
    $recentProjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Recent activity (combine posts and projects)
    $stmt = $conn->query("(SELECT 'post' as type, id, title as name, created_at FROM posts ORDER BY created_at DESC LIMIT 3)
                          UNION
                          (SELECT 'project' as type, id, name, created_at FROM projects ORDER BY created_at DESC LIMIT 3)
                          ORDER BY created_at DESC LIMIT 5");
    $recentActivity = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    // Set default values if query fails
    $stats = ['posts' => 0, 'users' => 0, 'media' => 0, 'projects' => 0];
    $projectStatus = [];
    $recentPosts = [];
    $recentProjects = [];
    $recentActivity = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css">
    <style>
 :root {
    --sidebar-width: 250px;
}

* {
    box-sizing: border-box;
}

/* Base styles */
body {
    margin: 0;
    padding: 0;
    overflow-x: hidden;
}

/* Wrapper and layout */
#wrapper {
    display: flex;
    min-height: 100vh;
}

/* Sidebar styles */
#sidebar-wrapper {
    width: var(--sidebar-width);
    min-height: 100vh;
    position: fixed;
    z-index: 1000;
    background-color: #343a40;
    transition: transform 0.3s ease-in-out;
    transform: translateX(0);
}

#sidebar-wrapper .sidebar-heading {
    padding: 0.875rem 1.25rem;
    font-size: 1.2rem;
    background-color: rgba(0, 0, 0, 0.1);
}

#sidebar-wrapper .list-group {
    width: var(--sidebar-width);
}

/* Content area */
#page-content-wrapper {
    flex: 1;
    min-height: 100vh;
    margin-left: var(--sidebar-width);
    transition: margin 0.3s ease-in-out;
}

/* Toggled states */
#wrapper.toggled #sidebar-wrapper {
    transform: translateX(-100%);
}

#wrapper.toggled #page-content-wrapper {
    margin-left: 0;
}

/* Navbar and toggle button */
.navbar {
    position: relative;
    z-index: 1100;
}

#menu-toggle {
    transition: all 0.3s ease;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

#menu-toggle:hover {
    transform: scale(1.05);
}

/* Component styles */
.list-group-item-action {
    transition: all 0.3s;
}

.list-group-item-action:hover {
    background-color: #495057 !important;
}

.card-stat {
    transition: transform 0.3s;
    border: none;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.card-stat:hover {
    transform: translateY(-5px);
}

.card-stat i {
    font-size: 2rem;
    opacity: 0.7;
}

.recent-item {
    border-left: 3px solid transparent;
    transition: all 0.3s;
}

.recent-item:hover {
    border-left-color: #0d6efd;
    background-color: rgba(13, 110, 253, 0.05);
}

.status-badge {
    padding: 0.35em 0.65em;
    font-size: 0.75em;
}

/* Responsive adjustments */
@media (max-width: 767.98px) {
    #sidebar-wrapper {
        transform: translateX(-100%);
    }
    
    #page-content-wrapper {
        margin-left: 0;
    }
    
    #wrapper.toggled #sidebar-wrapper {
        transform: translateX(0);
    }
    
    #wrapper.toggled #page-content-wrapper {
        margin-left: 0;
    }
}
    </style>
</head>
<body>
<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <div class="bg-dark border-right" id="sidebar-wrapper">
        <div class="sidebar-heading text-white text-center py-3">CMS Admin</div>
        <div class="list-group list-group-flush">
            <a href="index.php" class="list-group-item list-group-item-action text-white bg-dark active">
                <i class="fas fa-chart-line me-2"></i> Dashboard
            </a>
            <?php if (in_array('manage_posts', $_SESSION['permissions'])): ?>
            <a href="posts.php" class="list-group-item list-group-item-action text-white bg-dark">
                <i class="fas fa-file me-2"></i> Manage Posts
            </a>
            <?php endif; ?>
            
            <?php if (in_array('manage_media', $_SESSION['permissions'])): ?>
            <a href="media.php" class="list-group-item list-group-item-action text-white bg-dark">
                <i class="fas fa-photo-video me-2"></i> Media Library
            </a>
            <?php endif; ?>
            
            <?php if (in_array('manage_users', $_SESSION['permissions'])): ?>
            <a href="users.php" class="list-group-item list-group-item-action text-white bg-dark">
                <i class="fas fa-users me-2"></i> Manage Users
            </a>
            <?php endif; ?>

            <?php if (in_array('manage_projects', $_SESSION['permissions'])): ?>
            <a href="project.php" class="list-group-item list-group-item-action text-white bg-dark">
                <i class="fas fa-diagram-project me-2"></i> Manage Projects
            </a>
            <?php endif; ?>
            
            <?php if (in_array('manage_clients', $_SESSION['permissions'])): ?>
            <a href="clients.php" class="list-group-item list-group-item-action text-white bg-dark">
                <i class="fas fa-building me-2"></i> Manage Clients
            </a>
            <?php endif; ?>
            
            <a href="../auth/logout.php" class="list-group-item list-group-item-action text-white bg-dark">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
        </div>
    </div>

    <!-- Page Content -->
    <div id="page-content-wrapper" class="w-100">
        <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
            <div class="container-fluid">
            <button class="btn btn-dark" id="menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
                <h5 class="ms-3 mb-0">Dashboard Overview</h5>
                <div class="ms-auto d-flex align-items-center">
                    <span class="me-3">Welcome, <strong><?php echo htmlspecialchars($_SESSION["user_name"]); ?></strong></span>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container-fluid mt-4">
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card card-stat bg-primary text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-uppercase">Total Posts</h6>
                                    <h2 class="mb-0"><?php echo $stats['posts']; ?></h2>
                                </div>
                                <i class="fas fa-file"></i>
                            </div>
                            <div class="mt-3">
                                <a href="posts.php" class="text-white small">View All <i class="fas fa-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card card-stat bg-success text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-uppercase">Active Users</h6>
                                    <h2 class="mb-0"><?php echo $stats['users']; ?></h2>
                                </div>
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="mt-3">
                                <a href="users.php" class="text-white small">Manage Users <i class="fas fa-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card card-stat bg-warning text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-uppercase">Media Files</h6>
                                    <h2 class="mb-0"><?php echo $stats['media']; ?></h2>
                                </div>
                                <i class="fas fa-photo-video"></i>
                            </div>
                            <div class="mt-3">
                                <a href="media.php" class="text-white small">Browse Media <i class="fas fa-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card card-stat bg-info text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-uppercase">Active Projects</h6>
                                    <h2 class="mb-0"><?php echo $stats['projects']; ?></h2>
                                </div>
                                <i class="fas fa-diagram-project"></i>
                            </div>
                            <div class="mt-3">
                                <a href="project.php" class="text-white small">View Projects <i class="fas fa-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Project Status Chart -->
                <div class="col-lg-8 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Project Status Overview</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="projectStatusChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Recent Activity</h5>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($recentActivity)): ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($recentActivity as $activity): ?>
                                        <a href="<?php echo $activity['type'] == 'post' ? 'posts.php?action=edit&id='.$activity['id'] : 'project.php?action=view&id='.$activity['id']; ?>" 
                                           class="list-group-item list-group-item-action recent-item py-3">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($activity['name']); ?></h6>
                                                <small class="text-muted"><?php echo time_elapsed_string($activity['created_at']); ?></small>
                                            </div>
                                            <small class="text-<?php echo $activity['type'] == 'post' ? 'info' : 'success'; ?>">
                                                <i class="fas fa-<?php echo $activity['type'] == 'post' ? 'file' : 'project-diagram'; ?> me-1"></i>
                                                <?php echo ucfirst($activity['type']); ?>
                                            </small>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-info-circle fa-2x mb-2"></i>
                                    <p>No recent activity</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Recent Posts -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Posts</h5>
                            <a href="posts.php" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($recentPosts)): ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($recentPosts as $post): ?>
                                        <a href="posts.php?action=edit&id=<?php echo $post['id']; ?>" 
                                           class="list-group-item list-group-item-action recent-item py-3">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($post['title']); ?></h6>
                                                <small class="text-muted"><?php echo date('M j, Y', strtotime($post['created_at'])); ?></small>
                                            </div>
                                            <small class="text-muted">ID: <?php echo $post['id']; ?></small>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-file-alt fa-2x mb-2"></i>
                                    <p>No recent posts</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Projects -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Projects</h5>
                            <a href="project.php" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($recentProjects)): ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($recentProjects as $project): ?>
                                        <a href="project.php?action=view&id=<?php echo $project['id']; ?>" 
                                           class="list-group-item list-group-item-action recent-item py-3">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($project['name']); ?></h6>
                                                <span class="badge status-badge bg-<?php 
                                                    echo $project['status'] == 'completed' ? 'success' : 
                                                         ($project['status'] == 'in_progress' ? 'primary' : 
                                                         ($project['status'] == 'on_hold' ? 'warning' : 
                                                         ($project['status'] == 'cancelled' ? 'danger' : 'secondary'))); 
                                                ?>">
                                                    <?php echo str_replace('_', ' ', ucfirst($project['status'])); ?>
                                                </span>
                                            </div>
                                            <small class="text-muted">Client: <?php echo htmlspecialchars($project['client_name']); ?></small>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-project-diagram fa-2x mb-2"></i>
                                    <p>No recent projects</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<!-- Custom Scripts -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const wrapper = document.getElementById('wrapper');
    
    menuToggle.addEventListener('click', function(e) {
        e.preventDefault();
        wrapper.classList.toggle('toggled');
        // Update icon if you have one
        const icon = this.querySelector('i');
        if (icon) {
            icon.className = wrapper.classList.contains('toggled') 
                ? 'fas fa-bars' 
                : 'fas fa-times';
        }
    });
    
    // Handle responsive behavior
    function handleResponsive() {
        if (window.innerWidth >= 768) {
            // Desktop - start with sidebar visible
            wrapper.classList.remove('toggled');
        } else {
            // Mobile - start with sidebar hidden
            wrapper.classList.add('toggled');
        }
    }
    
    // Initial call
    handleResponsive();
    
    // Call on resize
    window.addEventListener('resize', handleResponsive);
});
</script>
<script>

    
    // Project Status Chart
    const projectStatusCtx = document.getElementById('projectStatusChart').getContext('2d');
    const projectStatusChart = new Chart(projectStatusCtx, {
        type: 'doughnut',
        data: {
            labels: [
                <?php 
                $statusLabels = [];
                $statusData = [];
                $statusColors = [];
                foreach ($projectStatus as $status) {
                    $statusLabels[] = "'" . str_replace('_', ' ', ucfirst($status['status'])) . "'";
                    $statusData[] = $status['count'];
                    
                    // Set colors based on status
                    switch($status['status']) {
                        case 'planning': $statusColors[] = "'#6c757d'"; break;
                        case 'in_progress': $statusColors[] = "'#0d6efd'"; break;
                        case 'on_hold': $statusColors[] = "'#ffc107'"; break;
                        case 'completed': $statusColors[] = "'#198754'"; break;
                        case 'cancelled': $statusColors[] = "'#dc3545'"; break;
                        default: $statusColors[] = "'#adb5bd'";
                    }
                }
                echo implode(', ', $statusLabels);
                ?>
            ],
            datasets: [{
                data: [<?php echo implode(', ', $statusData); ?>],
                backgroundColor: [<?php echo implode(', ', $statusColors); ?>],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            let value = context.raw || 0;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            },
            cutout: '70%',
        }
    });
    
    // Make cards clickable
    document.querySelectorAll('.card-stat').forEach(card => {
        const link = card.querySelector('a');
        if (link) {
            card.style.cursor = 'pointer';
            card.addEventListener('click', () => {
                window.location.href = link.href;
            });
        }
    });
</script>

<?php
// Helper function to display time elapsed
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    
    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;
    
    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }
    
    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
?>
</body>
</html>