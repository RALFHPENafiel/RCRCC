<?php
include("../includes/header.php");
?>
    <link rel="stylesheet" href="../assets/css/homepage.css" />

    <!-- Hero Section with Video -->
    <section class="hero">
      <!-- Replace the placeholder with your actual video -->
      <video class="hero-video" autoplay loop muted playsinline>
        <source src="../videos/hrscn.MP4" type="video/mp4" />
        Your browser does not support the video tag.
      </video>
    </section>

    <section class="ramos-hero-section" id="hero">
      <div class="ramos-container">
        <div class="ramos-content-wrapper">
          <div class="ramos-text-column">
            <div class="ramos-header-wrapper">
              <span class="ramos-preheader">RC RAMOS CONSTRUCTION</span>
              <h1 class="ramos-main-title">Transforming Visions,<br>Building Legacies</h1>
            </div>
            
            <p class="ramos-description">
              With over two decades of engineering excellence, RC RAMOS Construction Corporation delivers comprehensive construction solutions that integrate cutting-edge technology, strategic planning, and unparalleled craftsmanship across commercial, industrial, and residential sectors.
            </p>
            
            <div class="ramos-feature-grid">
              <div class="ramos-feature-item">
                <div class="ramos-feature-icon">
                  <i class="fas fa-hard-hat"></i>
                </div>
                <span>Innovative Engineering</span>
              </div>
              <div class="ramos-feature-item">
                <div class="ramos-feature-icon">
                  <i class="fas fa-chart-line"></i>
                </div>
                <span>Strategic Execution</span>
              </div>
            </div>
            
            <div class="ramos-cta-group">
              <a href="#contact" class="ramos-cta-button primary">Start Your Project</a>
              <a href="#portfolio" class="ramos-cta-button secondary">View Portfolio</a>
            </div>
          </div>
        </div>
      </div>
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

?

  <div class="recruitment-container">
      <div class="recruitment-content">
          <h1 class="recruitment-title">Join the team, we're growing fast!</h1>
          <p class="recruitment-description">We're looking for incredible people to build on our strong momentum. Help us power the brands you know and love.</p>
          <a href="#" class="recruitment-cta">See All Open Positions</a>
          <p class="recruitment-positions">69 open positions across <a href="#">all offices</a> and <a href="#">all teams</a>.</p>
      </div>
      <div class="recruitment-images">
          <div class="recruitment-image"></div>
          <div class="recruitment-image"></div>
          <div class="recruitment-image"></div>
          <div class="recruitment-image"></div>
          <div class="recruitment-image"></div>
          <div class="recruitment-image"></div>
          <div class="recruitment-image"></div>
      </div>
  </div>

    <div class="video-section" id="videoSection">
      <div class="video-container">
        <div class="video-overlay" id="videoOverlay">
          <button id="playButton" class="overlay-play-button">
            <i class="fas fa-play"></i>
            <span class="button-text">Watch Now</span>
          </button>
        </div>
        <video class="main-video" id="mainVideo" controls>
          <source src="../videos/videoo.mp4" type="video/mp4" />
          Your browser does not support the video tag.
        </video>
      </div>
    </div>

    <!-- Process Section -->
    <section class="process-section">
      <div class="section-pattern"></div>
      <div class="container">
        <div class="section-header">
          <span class="portfolio-badge">Expertly Engineered</span>
          <h2 class="portfolio-title">Our Proven Process</h2>
          <p class="portfolio-subtitle">
            From initial consultation to final installation, our comprehensive
            approach ensures your precast concrete project is completed with
            precision, efficiency, and the highest quality standards in the
            industry.
          </p>
        </div>

        <div class="process-steps">
          <div class="step">
            <div class="step-header">
              <div class="step-number">01</div>
              <h3 class="step-title">Initial Consultation</h3>
            </div>
            <p class="step-description">
              We start by understanding your project requirements, timeline,
              budget constraints, and specific design needs to create a
              customized plan for your precast concrete elements.
            </p>
            <div class="step-icon">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
              </svg>
            </div>
          </div>

          <div class="step">
            <div class="step-header">
              <div class="step-number">02</div>
              <h3 class="step-title">Engineering & Design</h3>
            </div>
            <p class="step-description">
              Our expert engineers create detailed designs and structural
              specifications for your precast components, ensuring they meet all
              building codes and performance requirements.
            </p>
            <div class="step-icon">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                <polyline points="2 17 12 22 22 17"></polyline>
                <polyline points="2 12 12 17 22 12"></polyline>
              </svg>
            </div>
          </div>

          <div class="step">
            <div class="step-header">
              <div class="step-number">03</div>
              <h3 class="step-title">Production Planning</h3>
            </div>
            <p class="step-description">
              We develop a detailed production schedule, prepare molds, and
              source high-quality materials to ensure efficient manufacturing of
              your precast concrete components.
            </p>
            <div class="step-icon">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <polygon
                  points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"
                ></polygon>
                <line x1="8" y1="2" x2="8" y2="18"></line>
                <line x1="16" y1="6" x2="16" y2="22"></line>
              </svg>
            </div>
          </div>

          <div class="step">
            <div class="step-header">
              <div class="step-number">04</div>
              <h3 class="step-title">Manufacturing</h3>
            </div>
            <p class="step-description">
              Components are produced in our state-of-the-art facility under
              strict quality control measures, creating precise, durable precast
              elements that match your specifications.
            </p>
            <div class="step-icon">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
              </svg>
            </div>
          </div>

          <div class="step">
            <div class="step-header">
              <div class="step-number">05</div>
              <h3 class="step-title">Installation</h3>
            </div>
            <p class="step-description">
              Our experienced installation team efficiently places all precast
              components on-site, following strict safety protocols and
              installation best practices for optimal results.
            </p>
            <div class="step-icon">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path
                  d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                ></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
              </svg>
            </div>
          </div>

          <div class="step">
            <div class="step-header">
              <div class="step-number">06</div>
              <h3 class="step-title">Quality Assurance</h3>
            </div>
            <p class="step-description">
              Final inspections and quality checks ensure every aspect of your
              project meets our rigorous standards, providing you with durable,
              beautiful precast concrete elements.
            </p>
            <div class="step-icon">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
              </svg>
            </div>
          </div>
        </div>

        <div class="action-container">
          <a href="services.html" class="services-btn">Explore Our Services</a>
        </div>
      </div>
    </section>

    <section class="why-choose-section">
      <div class="bg-accent"></div>
      <div class="container">
        <div class="section-header">
          <span class="portfolio-badge">Industry Excellence</span>
          <h2 class="portfolio-title">Why RC RAMOS?</h2>
          <p class="portfolio-subtitle">
            For over two decades, RC RAMOS has set the standard in precast
            concrete solutions. Our dedication to quality, innovation, and
            customer satisfaction has made us the preferred partner for
            construction projects nationwide.
          </p>
        </div>

        <div class="reasons-container">
          <div class="reason-box">
            <div class="reason-icon">
              <i class="fas fa-industry"></i>
            </div>
            <div class="reason-content">
              <h3 class="reason-title">Industry-Leading Precast Solutions</h3>
              <p class="reason-description">
                Our innovative precast concrete systems deliver superior
                structural integrity, faster installation times, and reduced
                on-site labor costs. Each solution is engineered to meet your
                project's unique specifications and challenges.
              </p>
            </div>
          </div>

          <div class="reason-box">
            <div class="reason-icon">
              <i class="fas fa-users"></i>
            </div>
            <div class="reason-content">
              <h3 class="reason-title">Experienced and Trusted Team</h3>
              <p class="reason-description">
                With our team of certified engineers, skilled technicians, and
                project managers, we bring decades of specialized precast
                expertise to your project. Our professionals work
                collaboratively to ensure flawless execution from design to
                installation.
              </p>
            </div>
          </div>

          <div class="reason-box">
            <div class="reason-icon">
              <i class="fas fa-medal"></i>
            </div>
            <div class="reason-content">
              <h3 class="reason-title">High-Quality Workmanship & Materials</h3>
              <p class="reason-description">
                We adhere to rigorous quality control processes and use only
                premium-grade materials. Every precast element undergoes
                comprehensive testing to ensure it meets or exceeds industry
                standards for strength, durability, and performance.
              </p>
            </div>
          </div>

          <div class="reason-box">
            <div class="reason-icon">
              <i class="fas fa-clock"></i>
            </div>
            <div class="reason-content">
              <h3 class="reason-title">Commitment to Timely Completion</h3>
              <p class="reason-description">
                Our advanced manufacturing facilities and streamlined project
                management processes enable us to deliver on schedule, every
                time. We understand that time is money in construction, and
                we're committed to keeping your project on track.
              </p>
            </div>
          </div>
        </div>

        <div class="why-rc-cta-container">
          <a href="services.html" class="services-btn"
            >Get a Free Consultation</a
          >
        </div>
      </div>
    </section>

    <!-- <section>
      <div class="container">
        <div class="section-header">
          <span class="portfolio-badge">Relationships</span>
          <h2 class="portfolio-title">Clients</h2>
        </div>
        <section class="customer-logos slider">
          <div class="slide"><img src="../images/partner.png" alt="logo" /></div>
          <div class="slide">
            <img src="../images/partner-2.png" alt="logo" />
          </div>
          <div class="slide">
            <img src="../images/partner-3.png" alt="logo" />
          </div>
          <div class="slide">
            <img src="../images/partner-4.png" alt="logo" />
          </div>
          <div class="slide">
            <img src="../images/partner-5.png" alt="logo" />
          </div>
          <div class="slide">
            <img src="../images/partner-6.png" alt="logo" />
          </div>
          <div class="slide">
            <img src="../images/partner-2.png" alt="logo" />
          </div>
          <div class="slide">
            <img src="../images/partner-3.png" alt="logo" />
          </div>
        </section>
      </div>
    </section> -->

    <!-- <section class="principles-section" id="principles">
      <div class="section-header">
        <span class="portfolio-badge">Core Valuess</span>
        <h2 class="portfolio-title">Our Principles</h2>
        <p>
          At RC RAMOS CONSTRUCTION CORPORATION, our principles form the
          foundation of everything we do. They guide our decisions, shape our
          culture, and define our commitment to excellence in the construction
          industry.
        </p>
      </div>

      <div class="principles-container">
        <div class="principle-card">
          <div class="principle-icon">
            <i class="fas fa-trophy"></i>
          </div>
          <div class="principle-content">
            <h3>Quality & Excellence</h3>
            <p>
              We are relentlessly committed to delivering construction projects
              that exceed expectations. Our dedication to superior
              craftsmanship, meticulous attention to detail, and use of premium
              materials ensures lasting value in every structure we build.
            </p>
          </div>
        </div>

        <div class="principle-card">
          <div class="principle-icon">
            <i class="fas fa-handshake"></i>
          </div>
          <div class="principle-content">
            <h3>Integrity & Transparency</h3>
            <p>
              We conduct our business with unwavering honesty and complete
              transparency. Our clients receive clear communication, fair
              pricing, and trustworthy service throughout every phase of the
              construction process.
            </p>
          </div>
        </div>

        <div class="principle-card">
          <div class="principle-icon">
            <i class="fas fa-hard-hat"></i>
          </div>
          <div class="principle-content">
            <h3>Safety First</h3>
            <p>
              We prioritize the safety and wellbeing of our team members,
              clients, and the public above all else. By adhering to and
              exceeding industry safety standards, we create secure environments
              on every job site.
            </p>
          </div>
        </div>

        <div class="principle-card">
          <div class="principle-icon">
            <i class="fas fa-leaf"></i>
          </div>
          <div class="principle-content">
            <h3>Environmental Responsibility</h3>
            <p>
              We embrace sustainable construction practices and innovative green
              building techniques. By minimizing our environmental footprint, we
              create structures that are not only beautiful and functional but
              also environmentally sound.
            </p>
          </div>
        </div>

        <div class="principle-card">
          <div class="principle-icon">
            <i class="fas fa-users"></i>
          </div>
          <div class="principle-content">
            <h3>Client Partnership</h3>
            <p>
              We approach each project as a collaborative partnership with our
              clients. By valuing their input, understanding their vision, and
              maintaining open communication, we transform their dreams into
              reality.
            </p>
          </div>
        </div>

        <div class="principle-card">
          <div class="principle-icon">
            <i class="fas fa-clock"></i>
          </div>
          <div class="principle-content">
            <h3>Timely Delivery</h3>
            <p>
              We respect our clients' timelines and understand the importance of
              scheduling in construction. Through efficient management and
              proactive planning, we strive to complete every project on
              schedule without compromising on quality.
            </p>
          </div>
        </div>
      </div>

      <div class="stats-section">
        <div class="stat-item">
          <div class="stat-number" id="years-count">0</div>
          <div class="stat-label">Years of Experience</div>
        </div>
        <div class="stat-item">
          <div class="stat-number" id="projects-count">0</div>
          <div class="stat-label">Projects Completed</div>
        </div>
        <div class="stat-item">
          <div class="stat-number" id="clients-count">0</div>
          <div class="stat-label">Satisfied Clients</div>
        </div>
        <div class="stat-item">
          <div class="stat-number" id="team-count">0</div>
          <div class="stat-label">Team Members</div>
        </div>
      </div>

      <div class="quote-section">
        <div class="quote">
          Building is not just about structures but about creating spaces where
          people can thrive, communities can grow, and futures can be built. We
          don't just construct buildings; we create legacies that stand the test
          of time.
        </div>
        <div class="quote-author">RC Ramos, Founder</div>
      </div> -->

      <!-- <div class="timeline-section">
        <div class="section-header">
          <span class="portfolio-badge">Milestones</span>
          <h2 class="portfolio-title">Our Journey of Excellence</h2>
        </div>
        <div class="timeline">
          <div class="timeline-item">
            <div class="timeline-content">
              <h3>Company Foundation</h3>
              <p>
                RC RAMOS CONSTRUCTION CORPORATION was established with a vision
                to transform the construction industry with integrity and
                innovation.
              </p>
            </div>
            <div class="timeline-dates">2005</div>
            <div class="timeline-dot"></div>
          </div>

          <div class="timeline-item">
            <div class="timeline-content">
              <h3>First Major Project</h3>
              <p>
                Completed our first commercial building project, setting the
                standard for our commitment to quality and timely delivery.
              </p>
            </div>
            <div class="timeline-dates">2008</div>
            <div class="timeline-dot"></div>
          </div>

          <div class="timeline-item">
            <div class="timeline-content">
              <h3>Sustainability Initiative</h3>
              <p>
                Launched our green building program, implementing sustainable
                practices across all our construction projects.
              </p>
            </div>
            <div class="timeline-dates">2012</div>
            <div class="timeline-dot"></div>
          </div>

          <div class="timeline-item">
            <div class="timeline-content">
              <h3>Industry Recognition</h3>
              <p>
                Received our first industry award for excellence in construction
                quality and safety standards.
              </p>
            </div>
            <div class="timeline-dates">2016</div>
            <div class="timeline-dot"></div>
          </div>

          <div class="timeline-item">
            <div class="timeline-content">
              <h3>Present Day</h3>
              <p>
                Continuing to grow and evolve while staying true to our founding
                principles and commitment to excellence.
              </p>
            </div>
            <div class="timeline-dates">Today</div>
            <div class="timeline-dot"></div>
          </div>
        </div>
      </div> -->

      <!-- <div class="principle-cta-container">
        <h3 class="principle-cta-heading">Ready to Build Something Amazing?</h3>
        <p class="principle-cta-text">
          Let's discuss how RC RAMOS CONSTRUCTION CORPORATION can bring your
          vision to life with our commitment to quality, integrity, and
          excellence.
        </p>
        <a href="#" class="portfolio-all-btn">Start Your Project Today</a>
      </div>
    </section> -->

    <?php
    include("../includes/footer.php");
    ?>

  

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

    <script>
      jQuery(document).ready(function ($) {
        $(".customer-logos").slick({
          slidesToShow: 6,
          slidesToScroll: 1,
          autoplay: true,
          autoplaySpeed: 1500,
          arrows: false,
          dots: false,
          pauseOnHover: false,
          responsive: [
            { breakpoint: 768, settings: { slidesToShow: 4 } },
            { breakpoint: 520, settings: { slidesToShow: 3 } },
          ],
        });

        AOS.init({
          duration: 1200,
          once: false,
        });

        $(window).on("load", function () {
          AOS.refresh();
        });
      });
    </script>

    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const cards = document.querySelectorAll(".principle-card");
        cards.forEach((card) => {
          card.style.opacity = 0;
          card.style.transform = "translateY(30px)";
          card.style.transition = "opacity 0.5s ease, transform 0.5s ease";
        });

        const timelineItems = document.querySelectorAll(".timeline-item");
        timelineItems.forEach((item, index) => {
          item.style.opacity = 0;
          if (index % 2 === 0) {
            item.style.transform = "translateX(-50px)";
          } else {
            item.style.transform = "translateX(50px)";
          }
          item.style.transition = "opacity 0.5s ease, transform 0.5s ease";
        });

        function isInViewport(element) {
          const rect = element.getBoundingClientRect();
          return (
            rect.top <=
              (window.innerHeight || document.documentElement.clientHeight) *
                0.8 && rect.bottom >= 0
          );
        }

        function handleScrollAnimations() {
          cards.forEach((card) => {
            if (isInViewport(card) && card.style.opacity === "0") {
              setTimeout(() => {
                card.style.opacity = 1;
                card.style.transform = "translateY(0)";
              }, 100);
            }
          });

          timelineItems.forEach((item, index) => {
            if (isInViewport(item) && item.style.opacity === "0") {
              setTimeout(() => {
                item.style.opacity = 1;
                item.style.transform = "translateX(0)";
              }, 100 * index);
            }
          });

          const statsSection = document.querySelector(".stats-section");
          if (
            isInViewport(statsSection) &&
            !statsSection.classList.contains("animated")
          ) {
            statsSection.classList.add("animated");
            animateStats();
          }
        }

        function animateStats() {
          const stats = [
            { id: "years-count", target: 20 },
            { id: "projects-count", target: 500 },
            { id: "clients-count", target: 375 },
            { id: "team-count", target: 120 },
          ];

          stats.forEach((stat) => {
            const el = document.getElementById(stat.id);
            const duration = 2000;
            const frameDuration = 1000 / 60;
            const totalFrames = Math.round(duration / frameDuration);
            let frame = 0;

            const counter = setInterval(() => {
              frame++;
              const progress = frame / totalFrames;
              const currentCount = Math.round(stat.target * progress);

              if (frame === totalFrames) {
                clearInterval(counter);
                el.textContent = stat.target;
              } else {
                el.textContent = currentCount;
              }
            }, frameDuration);
          });
        }

        handleScrollAnimations();

        window.addEventListener("scroll", handleScrollAnimations);
        const ctaButton = document.querySelector(".cta-button");
        ctaButton.addEventListener("mouseenter", () => {
          ctaButton.style.transform = "translateY(-5px)";
          ctaButton.style.boxShadow = "0 10px 25px rgba(230, 180, 0, 0.5)";
        });

        ctaButton.addEventListener("mouseleave", () => {
          ctaButton.style.transform = "";
          ctaButton.style.boxShadow = "";
        });
      });
    </script>
