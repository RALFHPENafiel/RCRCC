<?php
include("../includes/header.php");
?>

    <!-- Hero Section with Video -->
    <section class="hero">
      <!-- Replace the placeholder with your actual video -->
      <video class="hero-video" autoplay loop muted playsinline>
        <source src="../videos/hrscn.MP4" type="video/mp4" />
        Your browser does not support the video tag.
      </video>
    </section>

    <!-- Mini-Player -->
    <div class="mini-player" id="miniPlayer">
      <video class="mini-video" id="miniVideo" muted></video>
      <div class="mini-controls">
        <button id="playPauseMini"><i class="fas fa-play"></i></button>
        <button id="expandMini"><i class="fas fa-eject"></i></button>
        <button id="closeMini"><i class="fas fa-times"></i></button>
      </div>
      <div class="mini-progress-container">
        <div class="mini-progress-bar" id="miniProgressBar"></div>
      </div>
    </div>

    <section class="about-projects-wrapper">
      <div class="about-projects-container">
        <div class="about-projects-header">
          <span class="about-projects-badge">INNOVATION IN CONSTRUCTION</span>
          <h2 class="about-projects-title">
            Building Tomorrow's <br />Infrastructure Today
          </h2>
        </div>

        <div class="about-projects-description">
          <p class="about-projects-text">
            RC RAMOS CONSTRUCTION CORPORATION leverages cutting-edge
            technologies and innovative methodologies to transform architectural
            visions into robust, sustainable, and forward-thinking
            infrastructural solutions.
          </p>
        </div>

        <div class="about-projects-highlights">
          <div class="highlight-item">
            <div class="highlight-icon">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="64"
                height="64"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path
                  d="M12 3a6.364 6.364 0 0 1 0 12 6.364 6.364 0 0 1 0-12z"
                />
                <path d="M12 15v6" />
                <path d="M12 3v3" />
              </svg>
            </div>
            <div class="highlight-content">
              <h3>Strategic Planning</h3>
              <p>
                Comprehensive project analysis and strategic approach ensuring
                optimal resource allocation and efficiency.
              </p>
            </div>
          </div>

          <div class="highlight-item">
            <div class="highlight-icon">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="64"
                height="64"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <rect x="3" y="3" width="18" height="18" rx="2" />
                <path d="M7 8v8" />
                <path d="M11 12h4" />
                <path d="M17 8v8" />
              </svg>
            </div>
            <div class="highlight-content">
              <h3>Advanced Technology</h3>
              <p>
                Integrating state-of-the-art digital tools and advanced
                construction technologies for precision and quality.
              </p>
            </div>
          </div>

          <div class="highlight-item">
            <div class="highlight-icon">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="64"
                height="64"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path
                  d="M2 16V6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-4l-4 4-4-4H4a2 2 0 0 1-2-2Z"
                />
                <path d="M10 9h4" />
                <path d="M12 16V7" />
              </svg>
            </div>
            <div class="highlight-content">
              <h3>Sustainable Solutions</h3>
              <p>
                Committed to environmentally responsible practices and
                sustainable construction methodologies.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <?php
// Database connection
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

// Get all industries for the filter buttons
$industriesQuery = "SELECT DISTINCT industry FROM projects WHERE industry IS NOT NULL";
$industriesResult = $conn->query($industriesQuery);
$industries = [];

if ($industriesResult->num_rows > 0) {
    while($row = $industriesResult->fetch_assoc()) {
        if (!empty($row['industry'])) {
            $industries[] = $row['industry'];
        }
    }
}

// Select all projects
$sql = "SELECT p.*, c.name as client_name 
        FROM projects p
        LEFT JOIN clients c ON p.client_id = c.id
        ORDER BY p.created_at DESC";
        
$result = $conn->query($sql);

// Store results in array
$projects = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
}

?>

<section class="portfolio-wrapper">
      <div class="portfolio-container">
        <div class="section-header">
          <span class="portfolio-badge">PORTFOLIO</span>
          <h2 class="portfolio-title">Our Projects</h2>
          <p class="portfolio-subtitle">
            Explore our featured work and successful implementations that
            showcase our expertise and commitment to excellence in architecture
            and construction.
          </p>
        </div>

        <!-- Project Filter -->
        <div class="portfolio-filter">
          <button class="portfolio-filter-btn active" data-filter="all">
            All Projects
          </button>
          <?php foreach ($industries as $industry): ?>
          <button class="portfolio-filter-btn" data-filter="<?php echo strtolower(str_replace(' ', '-', $industry)); ?>">
            <?php echo htmlspecialchars($industry); ?>
          </button>
          <?php endforeach; ?>
        </div>

        <div class="portfolio-grid">
          <?php foreach ($projects as $project): 
                // Convert industry to lowercase with hyphens for data-category
                $categoryClass = !empty($project['industry']) ? 
                    strtolower(str_replace(' ', '-', $project['industry'])) : 'other';
                
                // Get the first project image as thumbnail
                $imageQuery = "SELECT file_path FROM project_images WHERE project_id = " . $project['id'] . " AND is_thumbnail = 1 LIMIT 1";
                $imageResult = $conn->query($imageQuery);
                $imagePath = "../images/default-project.png"; // Default image
                
                if ($imageResult && $imageResult->num_rows > 0) {
                    $imageRow = $imageResult->fetch_assoc();
                    $imagePath = "../uploads/projects/" . htmlspecialchars($imageRow['file_path']);
                }
          ?>
          <!-- Project <?php echo $project['id']; ?> -->
          <div class="portfolio-item" data-category="<?php echo $categoryClass; ?>">
            <div class="portfolio-item-img">
              <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($project['name']); ?>" />
              <div class="portfolio-category"><?php echo htmlspecialchars($project['industry'] ?? 'General'); ?></div>
            </div>
            <div class="portfolio-item-content">
              <h3 class="portfolio-item-title"><?php echo htmlspecialchars($project['name']); ?></h3>
              <p class="portfolio-item-desc">
                <?php 
                // Display a short description - first 100 characters
                $desc = !empty($project['description']) ? 
                    htmlspecialchars(substr($project['description'], 0, 100)) . (strlen($project['description']) > 100 ? '...' : '') : 
                    'No description available.';
                echo $desc;
                ?>
              </p>
              <a href="project.php?action=view&id=<?php echo $project['id']; ?>" class="portfolio-view-btn">View Project</a>
            </div>
          </div>
          <?php endforeach; ?>
          
          <?php if (empty($projects)): ?>
          <div class="no-projects-message">
            <p>No projects found. Check back soon for our latest work!</p>
          </div>
          <?php endif; ?>
        </div>

        <!-- Pagination -->
        <div class="portfolio-pagination">
          <button class="portfolio-page-btn active">1</button>
          <button class="portfolio-page-btn">2</button>
          <button class="portfolio-page-btn">3</button>
          <button class="portfolio-page-btn">Next</button>
        </div>
      </div>
    </section>


    <div class="ramos-wrapper">
      <!-- Signature Projects Section -->
      <section class="ramos-projects-section ramos-signature-projects">
        <h2 class="ramos-section-title">Signature Projects</h2>
        <div class="ramos-carousel-container ramos-signature-projects-carousel">
          <div class="ramos-carousel">
            <div class="ramos-carousel__nav">
              <span
                class="ramos-signature-moveLeft ramos-carousel__arrow"
                role="button"
                aria-label="Previous Signature Project"
              >
                <svg class="ramos-carousel__icon" viewBox="0 0 24 24">
                  <polyline points="15 18 9 12 15 6" />
                </svg>
              </span>
              <span
                class="ramos-signature-moveRight ramos-carousel__arrow"
                role="button"
                aria-label="Next Signature Project"
              >
                <svg class="ramos-carousel__icon" viewBox="0 0 24 24">
                  <polyline points="9 18 15 12 9 6" />
                </svg>
              </span>
            </div>

            <div class="ramos-carousel-item active">
              <div class="ramos-carousel-item__content">
                <div
                  class="ramos-carousel-item__image"
                  style="background-image: url('../images/4-img.png')"
                  aria-label="Metropolitan Tower Project"
                ></div>
                <div class="ramos-carousel-item__info">
                  <span class="ramos-project-tag">Signature Project</span>
                  <h3 class="ramos-carousel-item__title">Metropolitan Tower</h3>
                  <p class="ramos-carousel-item__description">
                    An iconic skyscraper that redefines urban architecture,
                    integrating sustainable design principles with cutting-edge
                    engineering to create a landmark in the city's skyline.
                  </p>
                  <div class="ramos-project-details">
                    <div class="ramos-project-detail">
                      <span class="ramos-project-detail-label">Location</span>
                      <span class="ramos-project-detail-value"
                        >Downtown Metro</span
                      >
                    </div>
                    <div class="ramos-project-detail">
                      <span class="ramos-project-detail-label">Completion</span>
                      <span class="ramos-project-detail-value">2022</span>
                    </div>
                    <div class="ramos-project-detail">
                      <span class="ramos-project-detail-label">Scale</span>
                      <span class="ramos-project-detail-value">45 Floors</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="ramos-carousel-item">
              <div class="ramos-carousel-item__content">
                <div
                  class="ramos-carousel-item__image"
                  style="background-image: url('../images/2_img.png')"
                ></div>
                <div class="ramos-carousel-item__info">
                  <span class="ramos-project-tag">Signature Project</span>
                  <h3 class="ramos-carousel-item__title">Riverside Complex</h3>
                  <p class="ramos-carousel-item__description">
                    A transformative mixed-use development that seamlessly
                    blends residential, commercial, and recreational spaces,
                    creating a vibrant urban ecosystem along the riverfront.
                  </p>
                  <div class="ramos-project-details">
                    <div class="ramos-project-detail">
                      <span class="ramos-project-detail-label">Location</span>
                      <span class="ramos-project-detail-value"
                        >Riverfront District</span
                      >
                    </div>
                    <div class="ramos-project-detail">
                      <span class="ramos-project-detail-label">Completion</span>
                      <span class="ramos-project-detail-value">2021</span>
                    </div>
                    <div class="ramos-project-detail">
                      <span class="ramos-project-detail-label">Scale</span>
                      <span class="ramos-project-detail-value"
                        >3 Buildings</span
                      >
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="ramos-carousel-item">
              <div class="ramos-carousel-item__content">
                <div
                  class="ramos-carousel-item__image"
                  style="background-image: url('../images/img-1.jpg')"
                ></div>
                <div class="ramos-carousel-item__info">
                  <span class="ramos-project-tag">Signature Project</span>
                  <h3 class="ramos-carousel-item__title">
                    Urban Innovation Center
                  </h3>
                  <p class="ramos-carousel-item__description">
                    A state-of-the-art facility designed to foster technological
                    innovation, combining sustainable architecture with advanced
                    infrastructure for modern businesses and research
                    institutions.
                  </p>
                  <div class="ramos-project-details">
                    <div class="ramos-project-detail">
                      <span class="ramos-project-detail-label">Location</span>
                      <span class="ramos-project-detail-value">Tech Hub</span>
                    </div>
                    <div class="ramos-project-detail">
                      <span class="ramos-project-detail-label">Completion</span>
                      <span class="ramos-project-detail-value">2023</span>
                    </div>
                    <div class="ramos-project-detail">
                      <span class="ramos-project-detail-label">Scale</span>
                      <span class="ramos-project-detail-value">25,000 m²</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="ramos-carousel-controls">
            <div class="ramos-carousel-control active" data-index="0"></div>
            <div class="ramos-carousel-control" data-index="1"></div>
            <div class="ramos-carousel-control" data-index="2"></div>
          </div>
        </div>
      </section>

      <!-- Ongoing Projects Section -->
      <section class="ramos-projects-section ramos-ongoing-projects">
        <h2 class="ramos-section-title">Ongoing Projects</h2>
        <div class="ramos-carousel-container ramos-ongoing-projects-carousel">
          <div class="ramos-carousel">
            <div class="ramos-carousel__nav">
              <span
                class="ramos-ongoing-moveLeft ramos-carousel__arrow"
                role="button"
                aria-label="Previous Ongoing Project"
              >
                <svg class="ramos-carousel__icon" viewBox="0 0 24 24">
                  <polyline points="15 18 9 12 15 6" />
                </svg>
              </span>
              <span
                class="ramos-ongoing-moveRight ramos-carousel__arrow"
                role="button"
                aria-label="Next Ongoing Project"
              >
                <svg class="ramos-carousel__icon" viewBox="0 0 24 24">
                  <polyline points="9 18 15 12 9 6" />
                </svg>
              </span>
            </div>

            <div class="ramos-carousel-item active">
              <div class="ramos-carousel-item__content">
                <div
                  class="ramos-carousel-item__image"
                  style="background-image: url('../images/on-going.jpg')"
                ></div>
                <div class="ramos-carousel-item__info">
                  <span class="ramos-project-tag">Ongoing Project</span>
                  <h3 class="ramos-carousel-item__title">
                    Green Campus Development
                  </h3>
                  <p class="ramos-carousel-item__description">
                    An innovative educational complex designed to set new
                    standards in sustainable campus architecture, integrating
                    renewable energy, green spaces, and advanced learning
                    environments.
                  </p>
                  <div class="ramos-project-details">
                    <div class="ramos-project-detail">
                      <span class="ramos-project-detail-label">Location</span>
                      <span class="ramos-project-detail-value"
                        >University District</span
                      >
                    </div>
                    <div class="ramos-project-detail">
                      <span class="ramos-project-detail-label"
                        >Expected Completion</span
                      >
                      <span class="ramos-project-detail-value">2025</span>
                    </div>
                    <div class="ramos-project-detail">
                      <span class="ramos-project-detail-label">Scale</span>
                      <span class="ramos-project-detail-value">40,000 m²</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="ramos-carousel-item">
              <div class="ramos-carousel-item__content">
                <div
                  class="ramos-carousel-item__image"
                  style="background-image: url('../images/ongoing.jpg')"
                ></div>
                <div class="ramos-carousel-item__info">
                  <span class="ramos-project-tag">Ongoing Project</span>
                  <h3 class="ramos-carousel-item__title">Urban Renewal Hub</h3>
                  <p class="ramos-carousel-item__description">
                    A comprehensive urban redevelopment initiative transforming
                    a historic district with modern infrastructure,
                    community-centric spaces, and architectural preservation.
                  </p>
                  <div class="ramos-project-details">
                    <div class="ramos-project-detail">
                      <span class="ramos-project-detail-label">Location</span>
                      <span class="ramos-project-detail-value"
                        >Historic District</span
                      >
                    </div>
                    <div class="ramos-project-detail">
                      <span class="ramos-project-detail-label"
                        >Expected Completion</span
                      >
                      <span class="ramos-project-detail-value">2024</span>
                    </div>
                    <div class="ramos-project-detail">
                      <span class="ramos-project-detail-label">Scale</span>
                      <span class="ramos-project-detail-value"
                        >5 City Blocks</span
                      >
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="ramos-carousel-controls">
            <div class="ramos-carousel-control active" data-index="0"></div>
            <div class="ramos-carousel-control" data-index="1"></div>
          </div>
        </div>
      </section>
    </div>

    <!-- Font Awesome for icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <!-- Load Latest jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Load jQuery Migrate to Fix Compatibility -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-migrate/3.3.2/jquery-migrate.min.js"></script>
    <!-- Load Slick Carousel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <!-- JavaScript for Enhanced Navigation -->
    <script>
      // Elements
      const header = document.getElementById("main-header");
      const mobileMenuBtn = document.querySelector(".mobile-menu-btn");
      const navMenu = document.getElementById("nav-menu");
      const navLinks = document.querySelectorAll(".nav-link");
      const body = document.body;

      // Mobile menu toggle
      mobileMenuBtn.addEventListener("click", () => {
        navMenu.classList.toggle("active");
        mobileMenuBtn.classList.toggle("open");
        mobileMenuBtn.setAttribute(
          "aria-expanded",
          mobileMenuBtn.getAttribute("aria-expanded") === "false"
            ? "true"
            : "false"
        );
        body.classList.toggle("menu-open");
      });

      // Close menu when clicking outside
      document.addEventListener("click", (e) => {
        if (
          navMenu.classList.contains("active") &&
          !navMenu.contains(e.target) &&
          !mobileMenuBtn.contains(e.target)
        ) {
          navMenu.classList.remove("active");
          mobileMenuBtn.classList.remove("open");
          mobileMenuBtn.setAttribute("aria-expanded", "false");
          body.classList.remove("menu-open");
        }
      });

      // Close menu when clicking on a nav link
      navLinks.forEach((link) => {
        link.addEventListener("click", () => {
          navMenu.classList.remove("active");
          mobileMenuBtn.classList.remove("open");
          mobileMenuBtn.setAttribute("aria-expanded", "false");
          body.classList.remove("menu-open");

          // Set active link
          navLinks.forEach((navLink) => navLink.classList.remove("active"));
          link.classList.add("active");
        });
      });

      // Header scroll effect
      window.addEventListener("scroll", () => {
        if (window.scrollY > 50) {
          header.classList.add("scrolled");
        } else {
          header.classList.remove("scrolled");
        }
      });

      // Keyboard navigation support
      navMenu.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
          navMenu.classList.remove("active");
          mobileMenuBtn.classList.remove("open");
          mobileMenuBtn.setAttribute("aria-expanded", "false");
          body.classList.remove("menu-open");
        }
      });
    </script>

    <!-- Add this to your existing script section or create a new script tag -->
    <script>
      function openProjectDetails(projectId) {
        // Placeholder for project details navigation
        alert(`Opening details for Project ${projectId}`);
        // In a real implementation, you might navigate to a specific project page
        // window.location.href = `project-details.html?id=${projectId}`;
      }
    </script>

    <script>
      // Project Filter JavaScript
      document.addEventListener("DOMContentLoaded", () => {
        const filterBtns = document.querySelectorAll(".portfolio-filter-btn");
        const portfolioItems = document.querySelectorAll(".portfolio-item");

        filterBtns.forEach((btn) => {
          btn.addEventListener("click", () => {
            // Remove active class from all buttons
            filterBtns.forEach((b) => b.classList.remove("active"));
            btn.classList.add("active");

            const filter = btn.getAttribute("data-filter");

            portfolioItems.forEach((item) => {
              const category = item.getAttribute("data-category");

              if (filter === "all" || category.toLowerCase().includes(filter)) {
                item.style.display = "block";
              } else {
                item.style.display = "none";
              }
            });
          });
        });
      });
    </script>

    <script>
      function initializeCarousel(
        carouselClass,
        moveLeftClass,
        moveRightClass,
        controlsClass
      ) {
        const carouselContainer = document.querySelector(carouselClass);
        const carousel = carouselContainer.querySelector(".ramos-carousel");
        const carouselItems = carousel.querySelectorAll(".ramos-carousel-item");
        const moveLeft = carouselContainer.querySelector(moveLeftClass);
        const moveRight = carouselContainer.querySelector(moveRightClass);
        const carouselControls = carouselContainer.querySelectorAll(
          ".ramos-carousel-control"
        );
        const total = carouselItems.length;
        let current = 0;

        function setSlide(prev, next) {
          carouselItems[prev].classList.remove("active");
          carouselControls[prev].classList.remove("active");

          if (next > total - 1) next = 0;
          if (next < 0) next = total - 1;

          carouselItems[next].classList.add("active");
          carouselControls[next].classList.add("active");
          current = next;
        }

        moveRight.addEventListener("click", function () {
          const prev = current;
          const next = current + 1;
          setSlide(prev, next);
        });

        moveLeft.addEventListener("click", function () {
          const prev = current;
          const next = current - 1;
          setSlide(prev, next);
        });

        carouselControls.forEach((control) => {
          control.addEventListener("click", function () {
            const index = parseInt(this.getAttribute("data-index"));
            setSlide(current, index);
          });
        });
      }

      document.addEventListener("DOMContentLoaded", function () {
        initializeCarousel(
          ".ramos-signature-projects-carousel",
          ".ramos-signature-moveLeft",
          ".ramos-signature-moveRight",
          ".ramos-signature-projects-carousel .ramos-carousel-control"
        );
        initializeCarousel(
          ".ramos-ongoing-projects-carousel",
          ".ramos-ongoing-moveLeft",
          ".ramos-ongoing-moveRight",
          ".ramos-ongoing-projects-carousel .ramos-carousel-control"
        );
      });
    </script>

    <?php
    include("../includes/footer.php");
    ?>
