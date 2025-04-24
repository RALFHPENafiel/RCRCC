    <?php
    require_once 'config/db.php'; // uses $conn from your db config

    $project = null;
    $projectImages = [];

    if (isset($_GET['project_id'])) {
        $project_id = $_GET['project_id'];

        // Fetch project + client info
        $stmt = $conn->prepare("
            SELECT 
                p.*, 
                c.contact_person AS client_name, 
                c.email AS client_email, 
                c.phone AS client_phone     
            FROM projects p 
            LEFT JOIN clients c ON p.client_id = c.id 
            WHERE p.id = :id
        ");
        $stmt->bindParam(':id', $project_id, PDO::PARAM_INT);
        $stmt->execute();

        $project = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch project images
        $imgStmt = $conn->prepare("SELECT file_path FROM project_images WHERE project_id = :project_id");
        $imgStmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
        $imgStmt->execute();

        $projectImages = $imgStmt->fetchAll(PDO::FETCH_ASSOC);
    }

    try {
        $stmt = $conn->prepare("
            SELECT p.id, p.name, pi.file_path
            FROM projects p
            LEFT JOIN project_images pi ON p.id = pi.project_id AND pi.is_thumbnail = 1
        ");
        $stmt->execute();
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
    ?>

    <!DOCTYPE html>
    <html>
    <head>
        <title><?= htmlspecialchars($project['name']) ?> - Project Details</title>
        <style>
            body {
                font-family: 'Segoe UI', sans-serif;
                background-color: #ffffff;
                color: #111111;
                margin: 0;
                padding: 0;
            }

            .container {
                padding: 40px;
                max-width: 1200px;
                margin: auto;
            }

            .top-section {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                gap: 40px;
            }

            .text-column {
                flex: 1;
            }

            .main-image {
                flex: 1;
                height: 300px;
                background-color: #f4f4f4;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .main-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                border: 2px solid #ff6600;
            }

            h1 {
                color: #ff6600;
                margin-bottom: 10px;
            }

            p {
                line-height: 1.6;
            }

            .info-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
                margin: 30px 0;
                border-top: 1px solid #000;
                padding-top: 20px;
            }

            .info-item {
                font-size: 14px;
                color: #333;
            }

            .info-item strong {
                color: #000;
                font-weight: 600;
            }

            .image-gallery {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin: 30px 0;
            }

            .gallery-image {
                width: calc(33.333% - 10px);
                height: 200px;
                object-fit: cover;
                border: 2px solid #e0e0e0;
                transition: border-color 0.3s;
            }

            .gallery-image:hover {
                border-color: #ff6600;
            }

            .stats {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                text-align: center;
                margin-top: 50px;
                border-top: 1px solid #ccc;
                padding-top: 20px;
            }

            .stat-box h2 {
                font-size: 32px;
                margin: 0;
                color: #000;
            }

            .stat-box p {
                font-size: 14px;
                color: #666;
            }

            a {
                color: #ff6600;
                text-decoration: none;
            }

            a:hover {
                text-decoration: underline;
            }

            @media (max-width: 768px) {
                .top-section, .info-grid, .stats {
                    grid-template-columns: 1fr !important;
                    flex-direction: column;
                }

                .gallery-image {
                    width: 100%;
                }
            }

            .grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        padding: 20px;
        }
        .tile {
        border: 1px solid #ddd;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transition: transform 0.2s ease;
        }
        .tile:hover {
        transform: scale(1.02);
        }
        .tile img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        }
        .tile h3 {
        margin: 0;
        padding: 12px;
        font-size: 1.1rem;
        text-align: center;
        }
        </style>
    </head>
    <body>
    <div class="container">
        <?php if (!$project): ?>
            <p>Project not found or no project ID specified.</p>
        <?php else: ?>
            <div class="top-section">
                <div class="text-column">
                    <h1><?= htmlspecialchars($project['name']) ?></h1>
                    <p><?= nl2br(htmlspecialchars($project['description'])) ?></p>
                </div>
                <div class="main-image">
                    <?php if (!empty($projectImages)): ?>
                        <img src="uploads/projects/<?= htmlspecialchars($projectImages[0]['file_path']) ?>" alt="Main Project Image">
                    <?php else: ?>
                        <p style="text-align:center; color: #999;">No image available</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-item"><strong>CLIENT</strong><br><?= htmlspecialchars($project['client_name']) ?></div>
                <div class="info-item"><strong>INDUSTRY</strong><br><?= htmlspecialchars($project['industry']) ?></div>
                <div class="info-item"><strong>SECTOR</strong><br><?= htmlspecialchars($project['structure']) ?></div>
                <div class="info-item"><strong>ARCHITECT</strong><br><?= htmlspecialchars($project['client_email']) ?></div>
            </div>

            <div class="image-gallery">
                <?php foreach ($projectImages as $img): ?>
                    <img src="uploads/projects/<?= htmlspecialchars($img['file_path']) ?>" class="gallery-image" alt="Project Image">
                <?php endforeach; ?>
            </div>

            <div class="stats">
                <div class="stat-box">
                    <h2><?= htmlspecialchars($project['status']) ?></h2>
                    <p>Project Status</p>
                </div>
                <div class="stat-box">
                    <h2><?= date('M j, Y', strtotime($project['start_date'])) ?> to <?= date('M j, Y', strtotime($project['end_date'])) ?></h2>
                    <p>Project Duration</p>
                </div>
                <div class="stat-box">
                    <h2><?= htmlspecialchars($project['location']) ?></h2>
                    <p>Location</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    
        <h1>All Projects</h1>
    <div class="grid">
    <?php if ($projects): ?>
        <?php foreach ($projects as $proj): ?>
        <div class="tile">
            <?php 
            $imagePath = !empty($proj['file_path']) ? "uploads/projects/" . htmlspecialchars($proj['file_path']) : 'placeholder.jpg';
            ?>
            <img src="<?= $imagePath ?>" alt="Project Image">
            <h3><?= htmlspecialchars($proj['name']) ?></h3>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align:center;">No projects found.</p>
    <?php endif; ?>
    </div>

    </body>
    </html>
