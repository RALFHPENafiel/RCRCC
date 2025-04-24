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
    <?php include 'sidebar.php'; ?>
    <div id="page-content-wrapper" class="w-100">
        <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
            <div class="container-fluid">
                <button class="btn btn-dark" id="menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h5 class="ms-3 mb-0"><?php echo $page_title ?? 'Admin Panel'; ?></h5>
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