    <style>
        /* ===== CTA Section ===== */
        .contact-cta {
        background: linear-gradient(rgba(26, 26, 26, 0.9), rgba(26, 26, 26, 0.9)),
            url("https://images.unsplash.com/photo-1600585152220-90363fe7e115?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80")
            no-repeat center center/cover;
        color: var(--light);
        text-align: center;
        padding: 100px 0;
        }

        .contact-cta h2 {
        color: var(--light);
        }

        .contact-cta h2:after {
        background: linear-gradient(90deg, var(--accent), var(--primary));
        }

        .contact-cta p {
        max-width: 700px;
        margin: 0 auto 40px;
        font-size: 1.2rem;
        color: rgba(255, 255, 255, 0.9);
        }

        .cta-buttons {
        display: flex;
        justify-content: center;
        gap: 20px;
        flex-wrap: wrap;
        }

        /* ===== Footer ===== */
        footer {
        background: var(--dark);
        color: var(--light);
        padding: 70px 0 30px;
        }

        .footer-content {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 40px;
        margin-bottom: 50px;
        }

        .footer-section {
        margin-bottom: 30px;
        }

        .footer-section h3 {
        color: var(--light);
        margin-bottom: 25px;
        position: relative;
        padding-bottom: 15px;
        font-size: 1.3rem;
        }

        .footer-section h3:after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 0;
        width: 40px;
        height: 3px;
        background: var(--primary);
        }

        .footer-section p {
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 20px;
        }

        .footer-section a {
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 12px;
        display: block;
        text-decoration: none;
        transition: var(--transition);
        }

        .footer-section a:hover {
        color: var(--accent);
        }

        .footer-section .contact-info {
        margin-bottom: 15px;
        display: flex;
        align-items: flex-start;
        }

        .footer-section .contact-info i {
        color: var(--primary);
        margin-right: 15px;
        margin-top: 5px;
        }

        .social-icons {
        display: flex;
        gap: 15px;
        margin-top: 25px;
        }

        .social-icons a {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        color: var(--light);
        transition: var(--transition);
        }

        .social-icons a:hover {
        background: var(--primary);
        }

        .copyright {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: 30px;
        text-align: center;
        color: rgba(255, 255, 255, 0.5);
        font-size: 0.9rem;
        }
    </style>
    <!-- CTA Section -->
    <section class="contact-cta">
        <div class="container">
            <h2>Ready to Start Your Construction Project?</h2>
            <p>Our team of experts is standing by to help bring your vision to life with quality craftsmanship and professional service.</p>
            <div class="cta-buttons">
                <a href="#contact-form" class="btn">Get a Free Quote</a>
                <a href="tel:+63281234567" class="btn btn-outline"><i class="fas fa-phone"></i> Call Us Now</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>RC Ramos Construction</h3>
                    <p>Building the future with excellence and integrity since 1985. Your trusted partner for quality construction across the Philippines.</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <a href="index.html">Home</a>
                    <a href="about.html">About Us</a>
                    <a href="services.html">Services</a>
                    <a href="projects.html">Projects</a>
                    <a href="contact.html">Contact Us</a>
                    <a href="#">Careers</a>
                </div>
                
                <div class="footer-section">
                    <h3>Services</h3>
                    <a href="#">Precast Construction</a>
                    <a href="#">Structural Engineering</a>
                    <a href="#">General Contracting</a>
                    <a href="#">Infrastructure</a>
                    <a href="#">Renovations</a>
                </div>
                
                <div class="footer-section">
                    <h3>Contact Us</h3>
                    <div class="contact-info">
                        <i class="fas fa-map-marker-alt"></i>
                        <p>194 Quezon Road, San Roque, Mexico, Pampanga Philippines 2021</p>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-phone"></i>
                        <p>+63 2 8123 4567<br>+63 912 345 6789</p>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-envelope"></i>
                        <p>info@rcramos.com<br>sales@rcramos.com</p>
                    </div>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; 2023 RC Ramos Construction Corporation. All Rights Reserved. | <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
            </div>
        </div>
    </footer>

    <!-- Floating Contact Button -->
    <div class="floating-contact">
        <a href="#contact-form" class="floating-btn">
            <i class="fas fa-envelope"></i>
        </a>
    </div>


</body>
</html>