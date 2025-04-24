<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cms_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if project ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: portfolio.php");
    exit;
}

$project_id = intval($_GET['id']);

// Fetch project details including coordinates
$projectQuery = "SELECT p.*, 
                c.name as client_name, c.contact_person, c.email as client_email, c.phone as client_phone, c.address as client_address,
                e.name as engineer_name, e.email as engineer_email, e.phone as engineer_phone,
                u.name as created_by_name,
                p.latitude, p.longitude
                FROM projects p 
                LEFT JOIN clients c ON p.client_id = c.id 
                LEFT JOIN engineers e ON p.engineer_id = e.id
                LEFT JOIN users u ON p.created_by = u.id
                WHERE p.id = ?";

$stmt = $conn->prepare($projectQuery);
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: portfolio.php");
    exit;
}

$project = $result->fetch_assoc();

// Fetch all project images
$imagesQuery = "SELECT * FROM project_images WHERE project_id = ? ORDER BY is_thumbnail DESC";
$stmt = $conn->prepare($imagesQuery);
$stmt->bind_param("i", $project_id);
$stmt->execute();
$imagesResult = $stmt->get_result();
$images = [];
while ($row = $imagesResult->fetch_assoc()) {
    $images[] = $row;
}

// Get thumbnail image
$thumbnailImage = "../images/default-project.png";
foreach ($images as $image) {
    if ($image['is_thumbnail'] == 1) {
        $thumbnailImage = "../uploads/projects/" . $image['file_path'];
        break;
    }
}
if ($thumbnailImage == "../images/default-project.png" && count($images) > 0) {
    $thumbnailImage = "../uploads/projects/" . $images[0]['file_path'];
}

function formatStatus($status) {
    return ucwords(str_replace('_', ' ', $status));
}

$pageTitle = $project['name'] . " | Project Details";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="project-details.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.awesome-markers/2.0.2/leaflet.awesome-markers.css" />
    <style>
        /* Project Hero Section */
        .proj-detail-hero-section {
            width: 100%;
            position: relative;
            margin-bottom: 60px;
        }

        .proj-detail-hero-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            z-index: 2;
            width: 100%;
        }

        .proj-detail-hero-title {
            font-size: 48px;
            font-weight: 700;
            color: #fff;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            margin: 0;
        }

        .proj-detail-hero-image-wrapper {
            width: 100%;
            height: 400px;
            overflow: hidden;
        }

        .proj-detail-hero-img {
            width: 100%;
            height: 100%;   
            object-fit: cover;
            filter: brightness(0.7);
        }

        /* Main Container */
        .proj-detail-main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 20px;
        }

        /* Header Text Center */
        .proj-detail-header-text-center {
            text-align: center;
            margin-bottom: 60px;
        }

        .proj-detail-main-title {
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .proj-detail-page-tagline {
            font-size: 18px;
            color: #555;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* Project Overview Section */
        .proj-detail-overview-section {
            margin-bottom: 80px;
        }

        .proj-detail-overview-container {
            display: flex;
            gap: 40px;
            align-items: flex-start;
        }

        .proj-detail-text-content {
            flex: 1;
            min-width: 0;
        }

        .proj-detail-text-content::before {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background-color: #000;
            margin-bottom: 30px;
        }

        /* Image Carousel Styles */
        .proj-detail-image-content {
            flex: 1;
            position: relative;
            min-width: 0;
        }

        .proj-detail-carousel {
            position: relative;
            width: 100%;
            height: 500px;
            overflow: hidden;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .proj-detail-carousel-inner {
            display: flex;
            transition: transform 0.5s ease;
            height: 100%;
        }

        .proj-detail-carousel-item {
            min-width: 100%;
            height: 100%;
        }

        .proj-detail-carousel-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .proj-detail-carousel-controls {
            position: absolute;
            top: 50%;
            width: 100%;
            display: flex;
            justify-content: space-between;
            transform: translateY(-50%);
            z-index: 10;
        }

        .proj-detail-carousel-btn {
            background: rgba(255,255,255,0.7);
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            font-size: 20px;
            color: #333;
        }

        .proj-detail-carousel-indicators {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            z-index: 10;
        }

        .proj-detail-carousel-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255,255,255,0.5);
            border: 1px solid rgba(0,0,0,0.2);
            cursor: pointer;
        }

        .proj-detail-carousel-indicator.active {
            background: rgba(0,0,0,0.7);
        }

        .proj-detail-section-heading {
            font-size: 30px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #000;
            line-height: 1.3;
        }

        .proj-detail-description-block {
            font-size: 16px;
            line-height: 1.8;
            color: #333;
            margin-bottom: 30px;
        }

        .proj-detail-description-text {
            margin-bottom: 20px;
            position: relative;
            padding-left: 20px;
            border-left: 2px solid #eee;
        }

        /* Project Details - Integrated with description */
        .proj-detail-meta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px 40px;
            margin-top: 40px;
        }

        .proj-detail-meta-item {
            margin-bottom: 15px;
        }

        .proj-detail-meta-label {
            font-size: 12px;
            font-weight: 600;
            color: #777;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .proj-detail-meta-value {
            font-size: 16px;
            color: #000;
            font-weight: 500;
        }

        /* Map Section */
        .proj-detail-map-section {
            margin: 60px 0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
            border: 1px solid #e0e0e0;
        }

        .proj-detail-map-header {
            background: #2c3e50;
            color: white;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .proj-detail-map-header h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }

        .proj-detail-map-header i {
            margin-right: 8px;
        }

        .map-zoom-controls {
            display: flex;
            gap: 5px;
        }

        .map-zoom-controls button {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .map-zoom-controls button:hover {
            background: rgba(255,255,255,0.3);
        }

        .proj-detail-map-container {
            width: 100%;
            height: 500px;
        }

        .proj-detail-map-placeholder {
            height: 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            color: #7f8c8d;
        }

        .proj-detail-map-placeholder i {
            margin-bottom: 15px;
            opacity: 0.5;
        }

        /* Leaflet Map Customization */
        .leaflet-container {
            background: #f8f9fa !important;
            font-family: inherit;
            height: 500px !important;
        }

        .leaflet-popup-content {
            min-width: 200px;
            margin: 12px !important;
        }

        .leaflet-popup-content h3 {
            color: #2c3e50;
            font-size: 16px;
            margin: 0 0 8px 0;
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
        }

        .leaflet-popup-content p {
            margin: 0;
            font-size: 14px;
            color: #555;
        }

        /* Back to Projects Button */
        .proj-detail-nav-footer {
            margin-top: 40px;
            text-align: center;
        }

        .proj-detail-back-btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: #000;
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .proj-detail-back-btn:hover {
            background-color: #333;
        }

        /* Responsive Styles */
        @media screen and (max-width: 992px) {
            .proj-detail-overview-container {
                flex-direction: column;
                gap: 30px;
            }
            
            .proj-detail-carousel,
            .leaflet-container,
            .proj-detail-map-container {
                height: 400px;
            }
            
            .proj-detail-hero-title {
                font-size: 36px;
            }
            
            .proj-detail-main-title {
                font-size: 36px;
            }
            
            .proj-detail-hero-image-wrapper {
                height: 350px;
            }
        }

        @media screen and (max-width: 768px) {
            .proj-detail-meta-grid {
                grid-template-columns: 1fr;
            }
            
            .proj-detail-carousel,
            .leaflet-container,
            .proj-detail-map-container {
                height: 350px;
            }
            
            .proj-detail-main-title {
                font-size: 32px;
            }
        }

        @media screen and (max-width: 576px) {
            .proj-detail-section-heading {
                font-size: 24px;
            }
            
            .proj-detail-carousel,
            .leaflet-container,
            .proj-detail-map-container {
                height: 300px;
            }
            
            .proj-detail-hero-title {
                font-size: 28px;
            }
            
            .proj-detail-main-title {
                font-size: 28px;
            }
            
            .proj-detail-hero-image-wrapper {
                height: 300px;
            }
            
            .proj-detail-main-container {
                padding: 40px 15px;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <main>
        <!-- Hero Section with Project Title -->
        <section class="proj-detail-hero-section">
            <div class="proj-detail-hero-content">
            </div>
            <div class="proj-detail-hero-image-wrapper">
                <img class="proj-detail-hero-img" src="<?php echo $thumbnailImage; ?>" alt="<?php echo htmlspecialchars($project['name']); ?>">
            </div>
        </section>
        
        <div class="proj-detail-main-container">
            <div class="proj-detail-header-text-center">
                <h1 class="proj-detail-main-title">Our Project Showcase</h1>
                <p class="proj-detail-page-tagline">Discover our diverse portfolio that reflects our commitment to quality and client satisfaction.</p>
            </div>
            
            <div class="proj-detail-content-wrapper">
                <!-- Project Overview Section with Image Carousel on Right -->
                <section class="proj-detail-overview-section">
                    <div class="proj-detail-overview-container">
                        <div class="proj-detail-text-content">
                            <h2 class="proj-detail-section-heading"><?php echo htmlspecialchars($project['name']); ?> <br>in <?php echo htmlspecialchars($project['location']); ?></h2>
                            
                            <div class="proj-detail-description-block">
                                <?php if ($project['description']): ?>
                                    <p class="proj-detail-description-text"><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
                                <?php else: ?>
                                    <p class="proj-detail-description-text">No description available for this project.</p>
                                <?php endif; ?>
                            
                                <!-- Integrated Project Meta Information -->
                                <div class="proj-detail-meta-grid">
                                    <div class="proj-detail-meta-item">
                                        <div class="proj-detail-meta-label">CLIENT</div>
                                        <div class="proj-detail-meta-value"><?php echo htmlspecialchars($project['client_name']); ?></div>
                                    </div>
                                    
                                    <div class="proj-detail-meta-item">
                                        <div class="proj-detail-meta-label">INDUSTRY</div>
                                        <div class="proj-detail-meta-value"><?php echo htmlspecialchars($project['industry']); ?></div>
                                    </div>
                                    
                                    <div class="proj-detail-meta-item">
                                        <div class="proj-detail-meta-label">STATUS</div>
                                        <div class="proj-detail-meta-value"><?php echo formatStatus($project['status']); ?></div>
                                    </div>
                                    
                                    <div class="proj-detail-meta-item">
                                        <div class="proj-detail-meta-label">STRUCTURE</div>
                                        <div class="proj-detail-meta-value"><?php echo htmlspecialchars($project['structure'] ?? 'Not specified'); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Image Carousel -->
                        <div class="proj-detail-image-content">
                            <div class="proj-detail-carousel">
                                <div class="proj-detail-carousel-inner" id="carouselInner">
                                    <?php foreach ($images as $index => $image): ?>
                                        <div class="proj-detail-carousel-item">
                                            <img src="../uploads/projects/<?php echo htmlspecialchars($image['file_path']); ?>" alt="Project Image <?php echo $index + 1; ?>" class="proj-detail-carousel-img">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <?php if (count($images) > 1): ?>
                                <div class="proj-detail-carousel-controls">
                                    <button class="proj-detail-carousel-btn" id="carouselPrev"><i class="fas fa-chevron-left"></i></button>
                                    <button class="proj-detail-carousel-btn" id="carouselNext"><i class="fas fa-chevron-right"></i></button>
                                </div>
                                
                                <div class="proj-detail-carousel-indicators" id="carouselIndicators">
                                    <?php foreach ($images as $index => $image): ?>
                                        <div class="proj-detail-carousel-indicator <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>"></div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </section>
                
               <!-- Enhanced Map Section -->
                <section class="proj-detail-map-section">
                    <div class="proj-detail-map-header">
                        <h3><i class="fas fa-map-marked-alt"></i> Project Location</h3>
                        <div class="map-controls">
                            <button id="mapFullscreen" class="map-control-btn" title="Toggle Fullscreen">
                                <i class="fas fa-expand-alt"></i>
                            </button>
                            <div class="map-zoom-controls">
                                <button id="zoomIn" class="map-control-btn" title="Zoom In">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button id="zoomOut" class="map-control-btn" title="Zoom Out">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="map-container">
                        <div id="projectMap" class="proj-detail-map-container"></div>
                        <?php if (empty($project['latitude']) || empty($project['longitude'])): ?>
                            <div class="proj-detail-map-placeholder">
                                <i class="fas fa-map-signs fa-3x pulse-animation"></i>
                                <p>Location data not available</p>
                                <small>Project coordinates have not been specified</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
                
                <!-- Back to Projects Button -->
                <div class="proj-detail-nav-footer">
                    <a href="portfolio.php" class="proj-detail-back-btn">Back to Projects</a>
                </div>
            </div>
        </div>
    </main>
    
    <?php include '../includes/footer.php'; ?>

    <!-- Map Style and Script -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.awesome-markers/2.0.2/leaflet.awesome-markers.css" />

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.awesome-markers/2.0.2/leaflet.awesome-markers.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Image Carousel Functionality
            const carouselInner = document.getElementById('carouselInner');
            const carouselItems = document.querySelectorAll('.proj-detail-carousel-item');
            const prevBtn = document.getElementById('carouselPrev');
            const nextBtn = document.getElementById('carouselNext');
            const indicators = document.querySelectorAll('.proj-detail-carousel-indicator');
            let currentIndex = 0;
            const totalItems = carouselItems.length;
            let autoRotateInterval;

            function updateCarousel() {
                carouselInner.style.transform = `translateX(-${currentIndex * 100}%)`;
                
                // Update indicators
                indicators.forEach((indicator, index) => {
                    if (index === currentIndex) {
                        indicator.classList.add('active');
                    } else {
                        indicator.classList.remove('active');
                    }
                });
            }

            function startAutoRotate() {
                if (totalItems > 1) {
                    autoRotateInterval = setInterval(() => {
                        currentIndex = (currentIndex < totalItems - 1) ? currentIndex + 1 : 0;
                        updateCarousel();
                    }, 5000);
                }
            }

            if (prevBtn && nextBtn) {
                prevBtn.addEventListener('click', () => {
                    clearInterval(autoRotateInterval);
                    currentIndex = (currentIndex > 0) ? currentIndex - 1 : totalItems - 1;
                    updateCarousel();
                    startAutoRotate();
                });

                nextBtn.addEventListener('click', () => {
                    clearInterval(autoRotateInterval);
                    currentIndex = (currentIndex < totalItems - 1) ? currentIndex + 1 : 0;
                    updateCarousel();
                    startAutoRotate();
                });
            }

            // Indicator click events
            indicators.forEach(indicator => {
                indicator.addEventListener('click', () => {
                    clearInterval(autoRotateInterval);
                    currentIndex = parseInt(indicator.dataset.index);
                    updateCarousel();
                    startAutoRotate();
                });
            });

            // Start auto-rotation
            startAutoRotate();

            // Initialize map if coordinates exist
            <?php if (!empty($project['latitude']) && !empty($project['longitude'])): ?>
            // Initialize map with enhanced settings
            const mapContainer = document.getElementById('projectMap');
            const projectCoords = [<?php echo $project['latitude']; ?>, <?php echo $project['longitude']; ?>];
            
            const map = L.map('projectMap', {
                center: projectCoords,
                zoom: 15,
                zoomControl: false,
                scrollWheelZoom: false,
                touchZoom: true,
                doubleClickZoom: true
            });

            // Choose from several premium map styles
            const mapStyles = {
                default: 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png',
                light: 'https://cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}.png',
                dark: 'https://cartodb-basemaps-{s}.global.ssl.fastly.net/dark_all/{z}/{x}/{y}.png',
                outdoors: 'https://{s}.tile.thunderforest.com/outdoors/{z}/{x}/{y}.png?apikey=your-api-key'
            };

            // Add premium-looking tile layer
            L.tileLayer(mapStyles.default, {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
                subdomains: 'abcd',
                maxZoom: 19,
                minZoom: 10
            }).addTo(map);

            // Create custom marker icon with brand color
            const projectMarker = L.AwesomeMarkers.icon({
                icon: 'building',
                markerColor: 'red', // Can be changed to match your brand color
                prefix: 'fa',
                iconColor: 'white',
                extraClasses: 'fa-solid'
            });

            // Add marker with custom icon and bounce animation effect
            const marker = L.marker(
                projectCoords,
                { 
                    icon: projectMarker,
                    title: '<?php echo htmlspecialchars($project['name']); ?>'
                }
            ).addTo(map);

            // Add a subtle circle around the marker
            const circle = L.circle(projectCoords, {
                color: 'rgba(231, 76, 60, 0.3)',
                fillColor: 'rgba(231, 76, 60, 0.1)',
                fillOpacity: 0.5,
                radius: 150
            }).addTo(map);

            // Enhanced popup with project info and styling
            const popupContent = `
                <div class="map-popup-content">
                    <div class="popup-header">
                        <h3><?php echo htmlspecialchars($project['name']); ?></h3>
                    </div>
                    <div class="popup-body">
                        <div class="popup-info-item">
                            <i class="fas fa-map-marker-alt"></i> 
                            <span><?php echo htmlspecialchars($project['location']); ?></span>
                        </div>
                        <?php if(!empty($project['client_name'])): ?>
                        <div class="popup-info-item">
                            <i class="fas fa-user-tie"></i> 
                            <span><?php echo htmlspecialchars($project['client_name']); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if(!empty($project['completion_date'])): ?>
                        <div class="popup-info-item">
                            <i class="fas fa-calendar-check"></i> 
                            <span>Completed: <?php echo htmlspecialchars($project['completion_date']); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="popup-footer">
                        <a href="#project-details" class="popup-link">Project Details</a>
                        <?php if(!empty($project['directions_url'])): ?>
                        <a href="<?php echo htmlspecialchars($project['directions_url']); ?>" target="_blank" class="popup-link">
                            <i class="fas fa-directions"></i> Directions
                        </a>
                        <?php endif; ?>
                    </div>
                </div>`;

            marker.bindPopup(popupContent).openPopup();

            // Add custom zoom controls
            document.getElementById('zoomIn').addEventListener('click', () => map.zoomIn());
            document.getElementById('zoomOut').addEventListener('click', () => map.zoomOut());

            // Fullscreen functionality
            let isFullscreen = false;
            const mapSection = document.querySelector('.proj-detail-map-section');
            const fullscreenBtn = document.getElementById('mapFullscreen');

            fullscreenBtn.addEventListener('click', () => {
                if (isFullscreen) {
                    mapSection.classList.remove('fullscreen-map');
                    fullscreenBtn.innerHTML = '<i class="fas fa-expand-alt"></i>';
                    document.body.style.overflow = '';
                } else {
                    mapSection.classList.add('fullscreen-map');
                    fullscreenBtn.innerHTML = '<i class="fas fa-compress-alt"></i>';
                    document.body.style.overflow = 'hidden';
                }
                isFullscreen = !isFullscreen;
                setTimeout(() => {
                    map.invalidateSize();
                }, 400);
            });

            // Add scale control with metric and imperial units
            L.control.scale({ 
                position: 'bottomleft',
                imperial: true,
                metric: true
            }).addTo(map);

            // Responsive behavior - recalculate on window resize
            window.addEventListener('resize', () => {
                map.invalidateSize();
            });
        <?php endif; ?>
        });
    </script>
</body>
</html>