<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'cms_db');

// File upload configuration
define('UPLOAD_DIR', __DIR__ . '/uploads/quote_requests/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
$allowedFileTypes = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];

// Email configuration
define('ADMIN_EMAIL', 'hr@yourcompany.com');
define('FROM_EMAIL', 'noreply@yourcompany.com');
define('FROM_NAME', 'Construction Company');

// Initialize variables
$errors = [];
$success = false;
$formData = [];

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = 'Invalid CSRF token. Please try again.';
    } else {
        // Sanitize and validate input
        $formData = [
            'name' => filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING),
            'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
            'phone' => filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING),
            'project-type' => filter_input(INPUT_POST, 'project-type', FILTER_SANITIZE_STRING),
            'budget' => filter_input(INPUT_POST, 'budget', FILTER_SANITIZE_STRING),
            'timeline' => filter_input(INPUT_POST, 'timeline', FILTER_SANITIZE_STRING),
            'message' => filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING)
        ];

        // Validate inputs
        if (empty($formData['name'])) $errors[] = 'Full name is required';
        if (empty($formData['email']) || !filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email address is required';
        }
        if (empty($formData['phone'])) {
            $errors[] = 'Phone number is required';
        } else {
            // Enhanced phone number validation with country code
            if (!preg_match('/^\+?\d{8,15}$/', $formData['phone'])) {
                $errors[] = 'Please enter a valid phone number with country code (e.g., +639123456789)';
            }
        }
        if (empty($formData['project-type'])) $errors[] = 'Project type is required';
        if (empty($formData['budget'])) $errors[] = 'Budget range is required';
        if (empty($formData['timeline'])) $errors[] = 'Project timeline is required';
        if (empty($formData['message'])) $errors[] = 'Project details are required';

        // Process file uploads if no errors
        $uploadedFiles = [];
        if (empty($errors) && !empty($_FILES['files']['name'][0])) {
            if (!is_dir(UPLOAD_DIR)) {
                mkdir(UPLOAD_DIR, 0755, true);
            }

            foreach ($_FILES['files']['tmp_name'] as $key => $tmpName) {
                if ($_FILES['files']['error'][$key] === UPLOAD_ERR_OK) {
                    // Validate file
                    $fileSize = $_FILES['files']['size'][$key];
                    $fileName = basename($_FILES['files']['name'][$key]);
                    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                    if ($fileSize > MAX_FILE_SIZE) {
                        $errors[] = "File $fileName exceeds maximum size of 10MB";
                        continue;
                    }

                    if (!in_array($fileExt, $allowedFileTypes)) {
                        $errors[] = "File type $fileExt is not allowed for $fileName";
                        continue;
                    }

                    // Generate unique filename
                    $newFileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\.]/', '_', $fileName);
                    $filePath = UPLOAD_DIR . $newFileName;

                    if (move_uploaded_file($tmpName, $filePath)) {
                        $uploadedFiles[] = [
                            'name' => $fileName,
                            'path' => $filePath,
                            'type' => $_FILES['files']['type'][$key]
                        ];
                    } else {
                        $errors[] = "Failed to upload file: $fileName";
                    }
                }
            }
        }

        // If no errors, process the form
        if (empty($errors)) {
            try {
                // Connect to database
                $db = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Check if client exists
                $stmt = $db->prepare("SELECT id FROM clients WHERE email = ?");
                $stmt->execute([$formData['email']]);
                $clientId = $stmt->fetchColumn();

                // Insert quote request
                $stmt = $db->prepare("
                    INSERT INTO quote_requests 
                    (client_id, name, email, phone, project_type, budget, timeline, message)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $clientId ?: null,
                    $formData['name'],
                    $formData['email'],
                    $formData['phone'],
                    $formData['project-type'],
                    $formData['budget'],
                    $formData['timeline'],
                    $formData['message']
                ]);
                $quoteRequestId = $db->lastInsertId();

                // Save uploaded files
                foreach ($uploadedFiles as $file) {
                    $stmt = $db->prepare("
                        INSERT INTO quote_request_files 
                        (quote_request_id, file_name, file_path, file_type)
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $quoteRequestId,
                        $file['name'],
                        $file['path'],
                        $file['type']
                    ]);
                }

                // Send email notifications
                sendEmailNotification($formData, $quoteRequestId, $uploadedFiles);
                sendConfirmationEmail($formData);

                $success = true;
            } catch (PDOException $e) {
                $errors[] = "Database error: " . $e->getMessage();
            }
        }
    }
}

function sendEmailNotification($formData, $quoteRequestId, $uploadedFiles) {
    $subject = "New Project Quote Request #$quoteRequestId";
    
    $message = "
        <h2>New Project Quote Request</h2>
        <p><strong>Request ID:</strong> #$quoteRequestId</p>
        <p><strong>Name:</strong> {$formData['name']}</p>
        <p><strong>Email:</strong> {$formData['email']}</p>
        <p><strong>Phone:</strong> {$formData['phone']}</p>
        <p><strong>Project Type:</strong> {$formData['project-type']}</p>
        <p><strong>Budget:</strong> {$formData['budget']}</p>
        <p><strong>Timeline:</strong> {$formData['timeline']}</p>
        <p><strong>Project Details:</strong><br>" . nl2br($formData['message']) . "</p>
    ";
    
    if (!empty($uploadedFiles)) {
        $message .= "<p><strong>Attached Files:</strong><ul>";
        foreach ($uploadedFiles as $file) {
            $message .= "<li>{$file['name']}</li>";
        }
        $message .= "</ul></p>";
    }
    
    $message .= "<p>You can view this request in the admin panel.</p>";
    
    $headers = [
        'From: ' . FROM_NAME . ' <' . FROM_EMAIL . '>',
        'Content-Type: text/html; charset=UTF-8',
        'X-Mailer: PHP/' . phpversion()
    ];
    
    mail(ADMIN_EMAIL, $subject, $message, implode("\r\n", $headers));
}

function sendConfirmationEmail($formData) {
    $subject = "Thank you for your project quote request";
    
    $message = "
        <h2>Thank You, {$formData['name']}!</h2>
        <p>We've received your project quote request and our team will review it shortly.</p>
        <p>Here's a summary of your request:</p>
        <ul>
            <li><strong>Project Type:</strong> {$formData['project-type']}</li>
            <li><strong>Budget:</strong> {$formData['budget']}</li>
            <li><strong>Timeline:</strong> {$formData['timeline']}</li>
        </ul>
        <p>We'll contact you within 2 business days to discuss your project in more detail.</p>
        <p>If you have any immediate questions, please don't hesitate to contact us.</p>
        <p>Best regards,<br>Construction Company Team</p>
    ";
    
    $headers = [
        'From: ' . FROM_NAME . ' <' . FROM_EMAIL . '>',
        'Content-Type: text/html; charset=UTF-8',
        'X-Mailer: PHP/' . phpversion()
    ];
    
    mail($formData['email'], $subject, $message, implode("\r\n", $headers));
}
?>
<?php
include("../includes/header.php");
?>
    <link rel="stylesheet" href="../assets/css/contact-us.css">
    <!-- Hero Section -->
    <section class="contact-hero">
        <div class="container">
            <div class="contact-hero-content">
                <h1>Building Connections, Creating Excellence</h1>
                <p>Get in touch with our team to discuss your next construction project. We're ready to turn your vision into reality.</p>
                <div class="cta-buttons">
                    <a href="#contact-form" class="btn">Request a Consultation</a>
                    <a href="tel:+63281234567" class="btn btn-outline"><i class="fas fa-phone"></i> Call Us Now</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Project Type Selector -->
    <section class="project-types">
        <div class="container">
            <h2>What Type of Project Are You Planning?</h2>
            <p class="text-center">Select your project category to connect with the right specialists</p>
            
            <div class="project-types-grid">
                <div class="project-type-card" onclick="scrollToForm('residential')">
                    <i class="fas fa-home"></i>
                    <h3>Residential</h3>
                    <p>Homes, apartments, villas, and residential complexes</p>
                </div>
                
                <div class="project-type-card" onclick="scrollToForm('commercial')">
                    <i class="fas fa-building"></i>
                    <h3>Commercial</h3>
                    <p>Office buildings, retail spaces, and business facilities</p>
                </div>
                
                <div class="project-type-card" onclick="scrollToForm('industrial')">
                    <i class="fas fa-industry"></i>
                    <h3>Industrial</h3>
                    <p>Factories, warehouses, and manufacturing plants</p>
                </div>
                
                <div class="project-type-card" onclick="scrollToForm('infrastructure')">
                    <i class="fas fa-road"></i>
                    <h3>Infrastructure</h3>
                    <p>Roads, bridges, utilities, and public works</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Enhanced Contact Methods Section -->
    <section class="contact-methods">
        <div class="container">
            <h2>Contact RC RAMOS Construction</h2>
            <p class="text-center">Reach out to us through any of these convenient methods</p>
            
            <div class="contact-grid">
                <div class="contact-card">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3>Visit Our Office</h3>
                    <p><strong>Address:</strong> 194 Quezon Road, San Roque</p>
                    <p>Mexico, Pampanga</p>
                    <p>Philippines 2021</p>
                    <a href="#location">View on map <i class="fas fa-arrow-right"></i></a>
                </div>
                
                <div class="contact-card">
                    <i class="fas fa-phone-alt"></i>
                    <h3>Call Our Team</h3>
                    <p><strong>Main Office:</strong> +63 2 8123 4567</p>
                    <p><strong>Sales Inquiries:</strong> +63 912 345 6789</p>
                    <p><strong>Support:</strong> +63 917 654 3210</p>
                    <a href="tel:+63281234567">Call now <i class="fas fa-arrow-right"></i></a>
                </div>
                
                <div class="contact-card emergency">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3>Emergency Contact</h3>
                    <p>24/7 construction emergencies</p>
                    <p>Site safety issues</p>
                    <p>Urgent project concerns</p>
                    <a href="tel:+639171234567">Emergency hotline <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section id="contact-form" class="contact-form-section">
        <div class="container">
            <h2>Request a Project Quote</h2>
            <p class="text-center">Complete this form and our team will provide a customized estimate for your construction project</p>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <h4>Please fix the following errors:</h4>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <h4>Thank you for your request!</h4>
                    <p>We've received your project quote request and will contact you shortly. A confirmation has been sent to your email address.</p>
                </div>
            <?php else: ?>
                <div class="form-container">
                    <form id="contactForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Full Name*</label>
                                <input type="text" id="name" name="name" required 
                                    value="<?php echo isset($formData['name']) ? htmlspecialchars($formData['name']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address*</label>
                                <input type="email" id="email" name="email" required 
                                    value="<?php echo isset($formData['email']) ? htmlspecialchars($formData['email']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">Phone Number*</label>
                                <div class="phone-input-group">
                                    <select id="country-code" name="country_code" style="width: 100px;">
                                        <option value="+63">PH (+63)</option>
                                        <option value="+1">US (+1)</option>
                                        <!-- Add more country codes as needed -->
                                    </select>
                                    <input type="tel" id="phone" name="phone" required 
                                        value="<?php echo isset($formData['phone']) ? htmlspecialchars($formData['phone']) : ''; ?>">
                                </div>
                                <small>Include area code (e.g., 9123456789)</small>
                            </div>
                            <div class="form-group">
                                <label for="project-type">Project Type*</label>
                                <select id="project-type" name="project-type" required>
                                    <option value="">Select Project Type</option>
                                    <option value="residential" <?php echo (isset($formData['project-type']) && $formData['project-type'] === 'residential' ? 'selected' : ''); ?>>Residential Construction</option>
                                    <option value="commercial" <?php echo (isset($formData['project-type']) && $formData['project-type'] === 'commercial' ? 'selected' : ''); ?>>Commercial Construction</option>
                                    <option value="industrial" <?php echo (isset($formData['project-type']) && $formData['project-type'] === 'industrial' ? 'selected' : ''); ?>>Industrial Facility</option>
                                    <option value="infrastructure" <?php echo (isset($formData['project-type']) && $formData['project-type'] === 'infrastructure' ? 'selected' : ''); ?>>Infrastructure</option>
                                    <option value="renovation" <?php echo (isset($formData['project-type']) && $formData['project-type'] === 'renovation' ? 'selected' : ''); ?>>Renovation/Remodeling</option>
                                    <option value="other" <?php echo (isset($formData['project-type']) && $formData['project-type'] === 'other' ? 'selected' : ''); ?>>Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="budget">Estimated Budget*</label>
                                <select id="budget" name="budget" required>
                                    <option value="">Select Budget Range</option>
                                    <option value="under-1m" <?php echo (isset($formData['budget']) && $formData['budget'] === 'under-1m' ? 'selected' : ''); ?>>Under ₱1 Million</option>
                                    <option value="1m-5m" <?php echo (isset($formData['budget']) && $formData['budget'] === '1m-5m' ? 'selected' : ''); ?>>₱1M - ₱5 Million</option>
                                    <option value="5m-10m" <?php echo (isset($formData['budget']) && $formData['budget'] === '5m-10m' ? 'selected' : ''); ?>>₱5M - ₱10 Million</option>
                                    <option value="10m-50m" <?php echo (isset($formData['budget']) && $formData['budget'] === '10m-50m' ? 'selected' : ''); ?>>₱10M - ₱50 Million</option>
                                    <option value="over-50m" <?php echo (isset($formData['budget']) && $formData['budget'] === 'over-50m' ? 'selected' : ''); ?>>Over ₱50 Million</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="timeline">Project Timeline*</label>
                                <select id="timeline" name="timeline" required>
                                    <option value="">Select Timeline</option>
                                    <option value="asap" <?php echo (isset($formData['timeline']) && $formData['timeline'] === 'asap' ? 'selected' : ''); ?>>ASAP</option>
                                    <option value="1-3m" <?php echo (isset($formData['timeline']) && $formData['timeline'] === '1-3m' ? 'selected' : ''); ?>>1-3 Months</option>
                                    <option value="3-6m" <?php echo (isset($formData['timeline']) && $formData['timeline'] === '3-6m' ? 'selected' : ''); ?>>3-6 Months</option>
                                    <option value="6-12m" <?php echo (isset($formData['timeline']) && $formData['timeline'] === '6-12m' ? 'selected' : ''); ?>>6-12 Months</option>
                                    <option value="over-1y" <?php echo (isset($formData['timeline']) && $formData['timeline'] === 'over-1y' ? 'selected' : ''); ?>>Over 1 Year</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Project Details*</label>
                            <textarea id="message" name="message" required placeholder="Please describe your project including size, location, timeline, and any special requirements"><?php echo isset($formData['message']) ? htmlspecialchars($formData['message']) : ''; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="files">Upload Files (Optional)</label>
                            <input type="file" id="files" name="files[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <small>You can upload plans, sketches, or reference images (Max 10MB, PDF, JPG, PNG, DOC allowed)</small>
                        </div>
                        
                        <!-- Simple CAPTCHA -->
                        <div class="form-group captcha-group">
                            <label for="captcha">What is 3 + 5? (Anti-spam)*</label>
                            <input type="text" id="captcha" name="captcha" required>
                        </div>
                        
                        <div class="form-submit">
                            <button type="submit" class="btn">
                                <i class="fas fa-paper-plane"></i> Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Departments Section -->
    <section class="departments">
        <div class="container">
            <h2>Contact the Right Department</h2>
            <p class="text-center">We have specialized teams ready to assist with your specific needs</p>
            
            <div class="departments-grid">
                <div class="department-card">
                    <h3>Sales & New Projects</h3>
                    <p>Discuss new construction opportunities and get customized project proposals.</p>
                    <div class="department-contacts">
                        <div class="department-contact">
                            <i class="fas fa-phone"></i>
                            <span>+63 2 8123 4567</span>
                        </div>
                        <div class="department-contact">
                            <i class="fas fa-envelope"></i>
                            <span>sales@rcramos.com</span>
                        </div>
                        <div class="department-contact">
                            <i class="fas fa-clock"></i>
                            <span>Mon-Fri: 8:00 AM - 6:00 PM</span>
                        </div>
                    </div>
                </div>
                
                <div class="department-card">
                    <h3>Client Support</h3>
                    <p>Existing project inquiries, change orders, and ongoing construction support.</p>
                    <div class="department-contacts">
                        <div class="department-contact">
                            <i class="fas fa-phone"></i>
                            <span>+63 2 8123 4568</span>
                        </div>
                        <div class="department-contact">
                            <i class="fas fa-envelope"></i>
                            <span>support@rcramos.com</span>
                        </div>
                        <div class="department-contact">
                            <i class="fas fa-clock"></i>
                            <span>Mon-Sat: 7:00 AM - 7:00 PM</span>
                        </div>
                    </div>
                </div>
                
                <div class="department-card">
                    <h3>Human Resources</h3>
                    <p>Career opportunities, subcontractor inquiries, and employment verification.</p>
                    <div class="department-contacts">
                        <div class="department-contact">
                            <i class="fas fa-phone"></i>
                            <span>+63 2 8123 4569</span>
                        </div>
                        <div class="department-contact">
                            <i class="fas fa-envelope"></i>
                            <span>hr@rcramos.com</span>
                        </div>
                        <div class="department-contact">
                            <i class="fas fa-clock"></i>
                            <span>Mon-Fri: 9:00 AM - 5:00 PM</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
        <div class="container">
            <h2>Meet Our Leadership Team</h2>
            <p class="text-center">Connect directly with our key executives for your construction needs</p>
            
            <div class="team-grid">
                <div class="team-card">
                    <div class="team-image">
                        <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" alt="Juan Dela Cruz">
                    </div>
                    <div class="team-info">
                        <h3>Juan Dela Cruz</h3>
                        <p class="position">Chief Executive Officer</p>
                        <p>20+ years in construction management and business development.</p>
                        <div class="team-contact">
                            <a href="tel:+639123456789" title="Call"><i class="fas fa-phone"></i></a>
                            <a href="mailto:juan@rcramos.com" title="Email"><i class="fas fa-envelope"></i></a>
                            <a href="#" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="team-card">
                    <div class="team-image">
                        <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" alt="Maria Santos">
                    </div>
                    <div class="team-info">
                        <h3>Maria Santos</h3>
                        <p class="position">VP of Operations</p>
                        <p>Specializes in large-scale commercial and industrial projects.</p>
                        <div class="team-contact">
                            <a href="tel:+639123456788" title="Call"><i class="fas fa-phone"></i></a>
                            <a href="mailto:maria@rcramos.com" title="Email"><i class="fas fa-envelope"></i></a>
                            <a href="#" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="team-card">
                    <div class="team-image">
                        <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" alt="Carlos Reyes">
                    </div>
                    <div class="team-info">
                        <h3>Carlos Reyes</h3>
                        <p class="position">Director of Residential Projects</p>
                        <p>Expert in custom home builds and residential developments.</p>
                        <div class="team-contact">
                            <a href="tel:+639123456787" title="Call"><i class="fas fa-phone"></i></a>
                            <a href="mailto:carlos@rcramos.com" title="Email"><i class="fas fa-envelope"></i></a>
                            <a href="#" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="team-card">
                    <div class="team-image">
                        <img src="https://images.unsplash.com/photo-1566492031773-4f4e44671857?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" alt="Roberto Ramos">
                    </div>
                    <div class="team-info">
                        <h3>Roberto Ramos</h3>
                        <p class="position">Founder & Chairman</p>
                        <p>Over 40 years of construction experience in the Philippines.</p>
                        <div class="team-contact">
                            <a href="tel:+639123456786" title="Call"><i class="fas fa-phone"></i></a>
                            <a href="mailto:roberto@rcramos.com" title="Email"><i class="fas fa-envelope"></i></a>
                            <a href="#" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Location Section -->
    <section id="location" class="location-section">
        <div class="container">
            <h2>Our Headquarters</h2>
            <p class="text-center">Visit our corporate office to discuss your construction needs in person</p>
            
            <div class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1185.1426694468673!2d120.75326296961981!3d15.067227494092014!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3396fa2db39f1f71%3A0x14dda8ec8b7c5dca!2sRC%20Ramos%20Construction%20Corporation!5e1!3m2!1sen!2sph!4v1743473072158!5m2!1sen!2sph" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                <div class="map-overlay">
                    <h3>RC Ramos Construction Corporation</h3>
                    <p>194 Quezon Road, San Roque, Mexico, Pampanga Philippines 2021</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <h2>Frequently Asked Questions</h2>
            <p class="text-center">Find quick answers to common questions about working with us</p>
            
            <div class="faq-container">
                <div class="faq-item">
                    <div class="faq-question">What information should I include in my project inquiry?</div>
                    <div class="faq-answer">
                        <p>For the most accurate response, please include: project type (residential, commercial, etc.), approximate size (square meters), location, desired timeline, budget range (if applicable), and any special requirements. The more details you provide, the better we can tailor our response to your needs.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">How soon can I expect a response to my inquiry?</div>
                    <div class="faq-answer">
                        <p>We typically respond to all inquiries within 1 business day. For complex projects requiring detailed estimates, we may need 2-3 business days to prepare a comprehensive response. Urgent inquiries can be expedited by calling our main office directly.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">Do you provide free consultations?</div>
                    <div class="faq-answer">
                        <p>Yes, we offer complimentary initial consultations for all new projects. This includes a preliminary discussion of your requirements, site evaluation (for local projects), and a rough estimate of costs and timelines. Detailed design and engineering consultations may involve fees depending on project scope.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">What areas do you serve?</div>
                    <div class="faq-answer">
                        <p>We operate throughout the Philippines with headquarters in Pampanga. Our primary service areas include Central Luzon, Metro Manila, and surrounding regions. For projects outside these areas, please contact us to discuss logistics and feasibility.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">How do you ensure quality control during construction?</div>
                    <div class="faq-answer">
                        <p>We implement a rigorous quality control process that includes: regular site inspections by project managers, third-party quality assurance checks, material testing, and strict adherence to building codes and standards. Clients receive periodic quality reports and are welcome to schedule site visits.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">What safety measures do you have in place?</div>
                    <div class="faq-answer">
                        <p>Safety is our top priority. We comply with all OSHA and local safety regulations, conduct regular safety training for all workers, perform daily safety inspections, and maintain comprehensive insurance coverage. Our safety record is among the best in the industry.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // FAQ Toggle Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const faqQuestions = document.querySelectorAll('.faq-question');
            
            faqQuestions.forEach(question => {
                question.addEventListener('click', () => {
                    const answer = question.nextElementSibling;
                    const isActive = question.classList.contains('active');
                    
                    // Close all other FAQs
                    document.querySelectorAll('.faq-question').forEach(q => {
                        if (q !== question) {
                            q.classList.remove('active');
                            q.nextElementSibling.classList.remove('show');
                        }
                    });
                    
                    // Toggle current FAQ
                    question.classList.toggle('active');
                    
                    if (isActive) {
                        answer.classList.remove('show');
                    } else {
                        answer.classList.add('show');
                    }
                });
            });
            
            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const targetId = this.getAttribute('href');
                    if (targetId === '#') return;
                    
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 100,
                            behavior: 'smooth'
                        });
                    }
                });
            });
            
            // Header scroll effect
            window.addEventListener('scroll', function() {
                const header = document.getElementById('main-header');
                if (window.scrollY > 50) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            });
        });

        // Function to scroll to form and select project type
        function scrollToForm(projectType) {
            const form = document.getElementById('contact-form');
            const select = document.getElementById('project-type');
            
            if (form && select) {
                window.scrollTo({
                    top: form.offsetTop - 100,
                    behavior: 'smooth'
                });
                
                // Set the project type in the form
                setTimeout(() => {
                    select.value = projectType;
                }, 500);
            }
        }
    </script>

<?php
    include("../includes/footer.php");
?>