<?php
// sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="bg-dark border-right" id="sidebar-wrapper">
    <div class="sidebar-heading text-white text-center py-3">CMS Admin</div>
    <div class="list-group list-group-flush">
        <a href="index.php" class="list-group-item list-group-item-action text-white bg-dark <?php echo $current_page === 'index.php' ? 'active' : '' ?>">
            <i class="fas fa-chart-line me-2"></i> Dashboard
        </a>
        <?php if (in_array('manage_posts', $_SESSION['permissions'])): ?>
        <a href="posts.php" class="list-group-item list-group-item-action text-white bg-dark <?php echo $current_page === 'posts.php' ? 'active' : '' ?>">
            <i class="fas fa-file me-2"></i> Manage Posts
        </a>
        <?php endif; ?>
        
        <?php if (in_array('manage_media', $_SESSION['permissions'])): ?>
        <a href="media.php" class="list-group-item list-group-item-action text-white bg-dark <?php echo $current_page === 'media.php' ? 'active' : '' ?>">
            <i class="fas fa-photo-video me-2"></i> Media Library
        </a>
        <?php endif; ?>
        
        <?php if (in_array('manage_users', $_SESSION['permissions'])): ?>
        <a href="users.php" class="list-group-item list-group-item-action text-white bg-dark <?php echo $current_page === 'users.php' ? 'active' : '' ?>">
            <i class="fas fa-users me-2"></i> Manage Users
        </a>
        <?php endif; ?>

        <?php if (in_array('manage_projects', $_SESSION['permissions'])): ?>
        <a href="project.php" class="list-group-item list-group-item-action text-white bg-dark <?php echo $current_page === 'project.php' ? 'active' : '' ?>">
            <i class="fas fa-diagram-project me-2"></i> Manage Projects
        </a>
        <?php endif; ?>
        
        <?php if (in_array('manage_clients', $_SESSION['permissions'])): ?>
        <a href="clients.php" class="list-group-item list-group-item-action text-white bg-dark <?php echo $current_page === 'clients.php' ? 'active' : '' ?>">
            <i class="fas fa-building me-2"></i> Manage Clients
        </a>
        <?php endif; ?>
        
        <a href="../auth/logout.php" class="list-group-item list-group-item-action text-white bg-dark">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
        </a>
    </div>
</div>