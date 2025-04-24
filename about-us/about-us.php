<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - R.C RAMOS CONSTRUCTION CORPORATION</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto+Slab:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Global Styles */
        :root {
            --primary: #1a3e72;
            --secondary: #f8b739;
            --accent: #e74c3c;
            --light: #f9f9f9;
            --dark: #0d2a4a;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --dark-gray: #495057;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            line-height: 1.7;
            color: #333;
            background-color: var(--light);
            overflow-x: hidden;
            font-family: 'Poppins', sans-serif;
        }
        
        h1, h2, h3, h4 {
            font-family: 'Roboto Slab', serif;
            font-weight: 600;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: var(--secondary);
            color: var(--dark);
            border: none;
            border-radius: 4px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 14px;
        }
        
        .btn:hover {
            background-color: #e0a328;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #0f2c5a;
        }
        
        .section-padding {
            padding: 100px 0;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 60px;
            position: relative;
        }
        
        .section-title h2 {
            font-size: 42px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .section-title p {
            font-size: 18px;
            color: var(--gray);
            max-width: 700px;
            margin: 0 auto;
        }
        
        .section-title:after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: var(--secondary);
            margin: 20px auto;
        }
        
        /* Header Styles */
        header {
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 999;
            background-color: rgba(26, 62, 114, 0.95);
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        header.scrolled {
            padding: 10px 0;
            background-color: rgba(26, 62, 114, 0.98);
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
        
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 28px;
            font-weight: 700;
            font-family: 'Roboto Slab', serif;
        }
        
        .logo span {
            color: var(--secondary);
        }
        
        nav ul {
            display: flex;
            list-style: none;
        }
        
        nav ul li {
            margin-left: 30px;
            position: relative;
        }
        
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            position: relative;
            font-size: 16px;
        }
        
        nav ul li a:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background: var(--secondary);
            bottom: -5px;
            left: 0;
            transition: width 0.3s;
        }
        
        nav ul li a:hover:after {
            width: 100%;
        }
        
        nav ul li a:hover {
            color: var(--secondary);
        }
        
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1605152276897-4f618f831968?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
            text-align: center;
            padding: 220px 20px 180px;
            position: relative;
        }
        
        .hero h1 {
            font-size: 60px;
            margin-bottom: 20px;
            font-weight: 700;
            text-transform: uppercase;
            animation: fadeInDown 1s ease;
            letter-spacing: 2px;
            line-height: 1.2;
        }
        
        .hero p {
            font-size: 20px;
            max-width: 800px;
            margin: 0 auto 30px;
            animation: fadeInUp 1s ease;
            opacity: 0.9;
        }
        
        .hero-btns {
            display: flex;
            justify-content: center;
            gap: 20px;
            animation: fadeIn 1.5s ease;
        }
        
        /* Breadcrumb */
        .breadcrumb {
            background-color: var(--light-gray);
            padding: 15px 0;
            margin-top: -1px;
        }
        
        .breadcrumb-container {
            display: flex;
            align-items: center;
        }
        
        .breadcrumb a {
            color: var(--primary);
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .breadcrumb a:hover {
            color: var(--secondary);
        }
        
        .breadcrumb-separator {
            margin: 0 10px;
            color: var(--gray);
        }
        
        .breadcrumb-current {
            color: var(--dark-gray);
            font-weight: 500;
        }
        
        /* About Content */
        .about-content {
            position: relative;
            overflow: hidden;
        }
        
        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
        }
        
        .about-image {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 20px 30px rgba(0,0,0,0.1);
            height: 500px;
        }
        
        .about-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .about-image:hover img {
            transform: scale(1.05);
        }
        
        .about-image:before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(to top, rgba(26, 62, 114, 0.7), transparent);
            z-index: 1;
            bottom: 0;
        }
        
        .about-text h3 {
            font-size: 36px;
            margin-bottom: 20px;
            color: var(--primary);
            font-weight: 700;
        }
        
        .about-text p {
            margin-bottom: 20px;
            font-size: 16px;
            color: #555;
            line-height: 1.8;
        }
        
        .about-features {
            margin-top: 30px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            background: var(--light-gray);
            padding: 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .feature-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .feature-icon {
            color: var(--secondary);
            font-size: 24px;
            margin-top: 5px;
            min-width: 30px;
        }
        
        .feature-text h4 {
            font-size: 18px;
            margin-bottom: 5px;
            color: var(--primary);
        }
        
        .feature-text p {
            font-size: 14px;
            color: var(--gray);
            margin-bottom: 0;
        }
        
        /* Company Overview */
        .company-overview {
            background-color: var(--light-gray);
            padding: 80px 0;
        }
        
        .overview-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
        }
        
        .overview-image {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 20px 30px rgba(0,0,0,0.1);
            height: 400px;
        }
        
        .overview-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .overview-text h3 {
            font-size: 32px;
            margin-bottom: 20px;
            color: var(--primary);
        }
        
        .overview-text p {
            margin-bottom: 20px;
            color: #555;
        }
        
        .overview-list {
            margin-top: 20px;
        }
        
        .overview-list li {
            margin-bottom: 10px;
            position: relative;
            padding-left: 25px;
            list-style: none;
        }
        
        .overview-list li:before {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            left: 0;
            top: 2px;
            color: var(--secondary);
        }
        
        /* Milestones Section */
        .milestones {
            background-color: var(--primary);
            color: white;
            position: relative;
        }
        
        .milestones .section-title h2 {
            color: white;
        }
        
        .milestones .section-title:after {
            background: var(--secondary);
        }
        
        .milestones-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
            text-align: center;
        }
        
        .milestone-item {
            padding: 30px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .milestone-item:hover {
            transform: translateY(-10px);
            background: rgba(255,255,255,0.15);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .milestone-icon {
            font-size: 40px;
            color: var(--secondary);
            margin-bottom: 20px;
        }
        
        .milestone-number {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 10px;
            color: white;
            font-family: 'Roboto Slab', serif;
        }
        
        .milestone-text {
            font-size: 18px;
            font-weight: 500;
        }
        
        /* Values Section */
        .values {
            background-color: #f1f5f9;
        }
        
        .values-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }
        
        .value-card {
            background: white;
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .value-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }
        
        .value-card:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 0;
            background: var(--secondary);
            transition: height 0.3s ease;
        }
        
        .value-card:hover:before {
            height: 100%;
        }
        
        .value-icon {
            font-size: 50px;
            color: var(--secondary);
            margin-bottom: 20px;
        }
        
        .value-card h3 {
            font-size: 22px;
            margin-bottom: 15px;
            color: var(--primary);
        }
        
        .value-card p {
            color: var(--gray);
        }
        
        /* Timeline Section */
        .timeline {
            position: relative;
            padding: 50px 0;
            background: url('https://images.unsplash.com/photo-1605106702734-205df224ecce?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80') no-repeat center center;
            background-size: cover;
            background-attachment: fixed;
            color: white;
        }
        
        .timeline:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(26, 62, 114, 0.9);
        }
        
        .timeline .section-title h2 {
            color: white;
            position: relative;
        }
        
        .timeline-container {
            position: relative;
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .timeline-item {
            position: relative;
            padding-left: 80px;
            margin-bottom: 50px;
        }
        
        .timeline-item:last-child {
            margin-bottom: 0;
        }
        
        .timeline-date {
            position: absolute;
            left: 0;
            top: 0;
            width: 60px;
            height: 60px;
            background: var(--secondary);
            color: var(--dark);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
            box-shadow: 0 0 0 5px rgba(248, 183, 57, 0.3);
        }
        
        .timeline-content {
            background: rgba(255,255,255,0.1);
            padding: 30px;
            border-radius: 10px;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255,255,255,0.1);
            transition: all 0.3s ease;
        }
        
        .timeline-content:hover {
            background: rgba(255,255,255,0.15);
        }
        
        .timeline-content h3 {
            font-size: 24px;
            margin-bottom: 10px;
            color: var(--secondary);
        }
        
        .timeline-content p {
            color: #ddd;
        }
        
        /* Team Section */
        .team {
            background-color: white;
        }
        
        .team-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }
        
        .team-member {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .team-member:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }
        
        .team-image {
            height: 350px;
            overflow: hidden;
            position: relative;
        }
        
        .team-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .team-member:hover .team-image img {
            transform: scale(1.1);
        }
        
        .team-info {
            padding: 25px;
            text-align: center;
        }
        
        .team-info h3 {
            font-size: 22px;
            margin-bottom: 5px;
            color: var(--primary);
        }
        
        .team-info p.position {
            color: var(--secondary);
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .team-info p.bio {
            color: var(--gray);
            font-size: 14px;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .team-social {
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        
        .team-social a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        
        .team-social a:hover {
            background: var(--secondary);
            color: var(--dark);
            transform: translateY(-3px);
        }
        
        /* Testimonials Section */
        .testimonials {
            background-color: var(--light-gray);
        }
        
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }
        
        .testimonial-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            position: relative;
        }
        
        .testimonial-card:before {
            content: '\201C';
            font-family: Georgia, serif;
            font-size: 60px;
            color: var(--secondary);
            opacity: 0.3;
            position: absolute;
            top: 10px;
            left: 10px;
        }
        
        .testimonial-content {
            margin-bottom: 20px;
            font-style: italic;
            color: var(--dark-gray);
            position: relative;
            z-index: 1;
        }
        
        .testimonial-author {
            display: flex;
            align-items: center;
        }
        
        .testimonial-author img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
        }
        
        .author-info h4 {
            font-size: 18px;
            margin-bottom: 5px;
            color: var(--primary);
        }
        
        .author-info p {
            font-size: 14px;
            color: var(--gray);
        }
        
        /* Clients Section */
        .clients {
            background-color: white;
        }
        
        .clients-slider {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 40px;
        }
        
        .client-logo {
            flex: 1;
            min-width: 150px;
            max-width: 200px;
            text-align: center;
            opacity: 0.7;
            transition: all 0.3s ease;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .client-logo:hover {
            opacity: 1;
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .client-logo img {
            max-width: 100%;
            height: auto;
            filter: grayscale(100%);
            transition: filter 0.3s ease;
        }
        
        .client-logo:hover img {
            filter: grayscale(0);
        }
        
        /* CTA Section */
        .cta {
            background: linear-gradient(rgba(26, 62, 114, 0.9), rgba(26, 62, 114, 0.9)), url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 100px 20px;
            position: relative;
        }
        
        .cta h2 {
            font-size: 42px;
            margin-bottom: 20px;
            position: relative;
        }
        
        .cta p {
            font-size: 18px;
            max-width: 700px;
            margin: 0 auto 30px;
            opacity: 0.9;
        }
        
        /* Footer */
        footer {
            background-color: var(--dark);
            color: white;
            padding: 80px 0 20px;
        }
        
        .footer-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 40px;
            margin-bottom: 50px;
        }
        
        .footer-col h3 {
            font-size: 20px;
            margin-bottom: 25px;
            color: var(--secondary);
            position: relative;
            padding-bottom: 10px;
        }
        
        .footer-col h3:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 2px;
            background: var(--secondary);
        }
        
        .footer-col p {
            color: #bbb;
            margin-bottom: 20px;
            line-height: 1.8;
        }
        
        .footer-col ul {
            list-style: none;
        }
        
        .footer-col ul li {
            margin-bottom: 12px;
        }
        
        .footer-col ul li a {
            color: #bbb;
            text-decoration: none;
            transition: all 0.3s ease;
            display: block;
        }
        
        .footer-col ul li a:hover {
            color: var(--secondary);
            padding-left: 5px;
        }
        
        .footer-contact li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .footer-contact i {
            color: var(--secondary);
            margin-top: 5px;
        }
        
        .footer-social {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .footer-social a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            color: white;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        
        .footer-social a:hover {
            background: var(--secondary);
            color: var(--dark);
            transform: translateY(-3px);
        }
        
        .copyright {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: #999;
            font-size: 14px;
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive Design */
        @media (max-width: 1200px) {
            .section-title h2 {
                font-size: 36px;
            }
            
            .hero h1 {
                font-size: 48px;
            }
        }
        
        @media (max-width: 992px) {
            .about-grid, .overview-container {
                grid-template-columns: 1fr;
            }
            
            .values-grid, .milestones-grid, .testimonials-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .team-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .footer-container {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .section-padding {
                padding: 80px 0;
            }
        }
        
        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }
            
            nav {
                position: fixed;
                top: 80px;
                left: -100%;
                width: 80%;
                height: calc(100vh - 80px);
                background: var(--primary);
                transition: all 0.5s ease;
                z-index: 998;
                box-shadow: 5px 0 15px rgba(0,0,0,0.2);
            }
            
            nav.active {
                left: 0;
            }
            
            nav ul {
                flex-direction: column;
                padding: 30px;
            }
            
            nav ul li {
                margin: 15px 0;
            }
            
            .hero {
                padding: 180px 20px 140px;
            }
            
            .hero h1 {
                font-size: 36px;
            }
            
            .hero p {
                font-size: 18px;
            }
            
            .hero-btns {
                flex-direction: column;
                gap: 15px;
            }
            
            .btn {
                width: 100%;
            }
            
            .section-title h2 {
                font-size: 32px;
            }
            
            .about-features {
                grid-template-columns: 1fr;
            }
            
            .team-grid, .values-grid, .milestones-grid, .testimonials-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 576px) {
            .footer-container {
                grid-template-columns: 1fr;
            }
            
            .section-padding {
                padding: 60px 0;
            }
            
            .cta h2 {
                font-size: 32px;
            }
            
            .milestone-item {
                padding: 20px;
            }
            
            .milestone-number {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header id="header">
        <div class="container header-container">
            <div class="logo">R.C <span>RAMOS</span> CONSTRUCTION</div>
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
            </button>
            <nav id="mainNav">
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="about.html" class="active">About Us</a></li>
                    <li><a href="services.html">Services</a></li>
                    <li><a href="projects.html">Projects</a></li>
                    <li><a href="contact.html">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Building the Future with Excellence</h1>
            <p>For over three decades, R.C Ramos Construction Corporation has been shaping skylines and communities with integrity, innovation, and unparalleled craftsmanship.</p>
            <div class="hero-btns">
                <a href="#contact" class="btn btn-primary">Get a Quote</a>
                <a href="projects.html" class="btn">View Our Work</a>
            </div>
        </div>
    </section>
    
    <!-- Breadcrumb -->
    <section class="breadcrumb">
        <div class="container breadcrumb-container">
            <a href="index.html">Home</a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">About Us</span>
        </div>
    </section>
    
    <!-- About Content -->
    <section class="about-content section-padding">
        <div class="container">
            <div class="about-grid">
                <div class="about-image">
                    <img src="https://images.unsplash.com/photo-1605152276897-4f618f831968?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="R.C Ramos Construction Team">
                </div>
                <div class="about-text">
                    <h3>Our Legacy of Building Excellence</h3>
                    <p>Founded in 1985 by Ramon C. Ramos, R.C Ramos Construction Corporation began as a small family business with a single backhoe and a big dream. Today, we stand as one of the most respected construction firms in the region, with a portfolio spanning commercial, residential, and infrastructure projects.</p>
                    <p>What sets us apart is our unwavering commitment to quality, safety, and client satisfaction. We approach every project—whether a modest renovation or a multi-story development—with the same level of dedication and attention to detail.</p>
                    <p>Under the leadership of second-generation CEO Maria Ramos-Santos, we continue to innovate while maintaining the core values that have guided us since the beginning: integrity, craftsmanship, and community.</p>
                    
                    <div class="about-features">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-award"></i>
                            </div>
                            <div class="feature-text">
                                <h4>Licensed & Certified</h4>
                                <p>Fully licensed general contractors with all required certifications</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="feature-text">
                                <h4>Safety First</h4>
                                <p>OSHA-compliant with an exemplary safety record</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-leaf"></i>
                            </div>
                            <div class="feature-text">
                                <h4>Sustainable Practices</h4>
                                <p>LEED-certified professionals committed to green building</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <div class="feature-text">
                                <h4>Client Focused</h4>
                                <p>98% client satisfaction rate across all projects</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Company Overview -->
    <section class="company-overview">
        <div class="container">
            <div class="overview-container">
                <div class="overview-text">
                    <h3>Comprehensive Construction Solutions</h3>
                    <p>R.C Ramos Construction Corporation offers a full spectrum of construction services tailored to meet the diverse needs of our clients. Our integrated approach ensures seamless project delivery from conception to completion.</p>
                    
                    <div class="overview-list">
                        <li>General contracting for commercial and residential projects</li>
                        <li>Construction management services</li>
                        <li>Design-build capabilities</li>
                        <li>Renovation and restoration expertise</li>
                        <li>Sustainable and green building solutions</li>
                        <li>Infrastructure and civil works</li>
                    </div>
                    
                    <a href="services.html" class="btn btn-primary">Explore Our Services</a>
                </div>
                <div class="overview-image">
                    <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Construction Site">
                </div>
            </div>
        </div>
    </section>
    
    <!-- Milestones Section -->
    <section class="milestones section-padding">
        <div class="container">
            <div class="section-title">
                <h2>Our Milestones</h2>
                <p>Key achievements that mark our journey of growth and excellence in the construction industry</p>
            </div>
            
            <div class="milestones-grid">
                <div class="milestone-item">
                    <div class="milestone-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="milestone-number">1985</div>
                    <div class="milestone-text">Company Founded</div>
                </div>
                <div class="milestone-item">
                    <div class="milestone-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="milestone-number">500+</div>
                    <div class="milestone-text">Projects Completed</div>
                </div>
                <div class="milestone-item">
                    <div class="milestone-icon">
                        <i class="fas fa-hard-hat"></i>
                    </div>
                    <div class="milestone-number">300+</div>
                    <div class="milestone-text">Skilled Professionals</div>
                </div>
                <div class="milestone-item">
                    <div class="milestone-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="milestone-number">25+</div>
                    <div class="milestone-text">Industry Awards</div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Values Section -->
    <section class="values section-padding">
        <div class="container">
            <div class="section-title">
                <h2>Our Core Values</h2>
                <p>The principles that guide every decision we make and every project we undertake</p>
            </div>
            
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-gem"></i>
                    </div>
                    <h3>Integrity</h3>
                    <p>We build trust through transparency, honesty, and ethical business practices in all our relationships.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3>Quality</h3>
                    <p>From materials to workmanship, we never compromise on delivering the highest standards of quality.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h3>Innovation</h3>
                    <p>We embrace cutting-edge technologies and methods to improve efficiency and project outcomes.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Teamwork</h3>
                    <p>Our collaborative approach brings together diverse expertise to achieve exceptional results.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h3>Sustainability</h3>
                    <p>We're committed to environmentally responsible building practices for a greener future.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3>Community</h3>
                    <p>We give back to the communities we serve through local hiring and charitable initiatives.</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Timeline Section -->
    <section class="timeline section-padding">
        <div class="container">
            <div class="section-title">
                <h2>Our Journey</h2>
                <p>Key moments in our company's history that have shaped who we are today</p>
            </div>
            
            <div class="timeline-container">
                <div class="timeline-item">
                    <div class="timeline-date">1985</div>
                    <div class="timeline-content">
                        <h3>Company Founded</h3>
                        <p>Ramon C. Ramos establishes the company with a small team and a vision to build quality structures that stand the test of time.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-date">1992</div>
                    <div class="timeline-content">
                        <h3>First Major Contract</h3>
                        <p>Awarded our first multi-million dollar project - the Metro City Civic Center, establishing our reputation for quality commercial construction.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-date">2005</div>
                    <div class="timeline-content">
                        <h3>Leadership Transition</h3>
                        <p>Maria Ramos-Santos takes over as CEO, bringing fresh vision while maintaining the company's core values and commitment to quality.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-date">2012</div>
                    <div class="timeline-content">
                        <h3>Sustainability Initiative</h3>
                        <p>Launched our green building division, becoming leaders in sustainable construction practices in our region.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-date">2020</div>
                    <div class="timeline-content">
                        <h3>35th Anniversary</h3>
                        <p>Celebrated 35 years of building excellence with over 500 completed projects and numerous industry awards.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Team Section -->
    <section class="team section-padding">
        <div class="container">
            <div class="section-title">
                <h2>Meet Our Leadership</h2>
                <p>The experienced professionals who guide our company's vision and operations</p>
            </div>
            
            <div class="team-grid">
                <div class="team-member">
                    <div class="team-image">
                        <img src="https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?ixlib=rb-1.2.1&auto=format&fit=crop&w=634&q=80" alt="Maria Ramos-Santos">
                    </div>
                    <div class="team-info">
                        <h3>Maria Ramos-Santos</h3>
                        <p class="position">Chief Executive Officer</p>
                        <p class="bio">With 20+ years in construction management, Maria leads with a vision for sustainable growth while honoring the company's legacy. She holds an MBA from Harvard and serves on several industry boards.</p>
                        <div class="team-social">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                </div>
                <div class="team-member">
                    <div class="team-image">
                        <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-1.2.1&auto=format&fit=crop&w=634&q=80" alt="Carlos Mendoza">
                    </div>
                    <div class="team-info">
                        <h3>Carlos Mendoza</h3>
                        <p class="position">Chief Operations Officer</p>
                        <p class="bio">A construction veteran with 30+ years experience ensuring projects meet our exacting standards for quality and safety. Carlos oversees all field operations and workforce development.</p>
                        <div class="team-social">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                </div>
                <div class="team-member">
                    <div class="team-image">
                        <img src="https://images.unsplash.com/photo-1573497620053-ea5300f94f21?ixlib=rb-1.2.1&auto=format&fit=crop&w=634&q=80" alt="Lisa Chen">
                    </div>
                    <div class="team-info">
                        <h3>Lisa Chen</h3>
                        <p class="position">Director of Engineering</p>
                        <p class="bio">Licensed engineer and LEED AP with expertise in sustainable design and innovative construction technologies. Lisa leads our engineering team and ensures all projects meet technical specifications.</p>
                        <div class="team-social">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Testimonials Section -->
    <section class="testimonials section-padding">
        <div class="container">
            <div class="section-title">
                <h2>What Our Clients Say</h2>
                <p>Hear from some of the many satisfied clients we've worked with over the years</p>
            </div>
            
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        "R.C Ramos Construction delivered our corporate headquarters on time and under budget. Their attention to detail and proactive communication made the entire process seamless."
                    </div>
                    <div class="testimonial-author">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="James Wilson">
                        <div class="author-info">
                            <h4>James Wilson</h4>
                            <p>CEO, Wilson Enterprises</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        "Working with R.C Ramos was a pleasure from start to finish. They understood our vision for a sustainable office complex and brought it to life beyond our expectations."
                    </div>
                    <div class="testimonial-author">
                        <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Sarah Johnson">
                        <div class="author-info">
                            <h4>Sarah Johnson</h4>
                            <p>Director, GreenTech Solutions</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        "The quality of construction and professionalism of the R.C Ramos team is unmatched. Our hospital expansion project was completed with minimal disruption to operations."
                    </div>
                    <div class="testimonial-author">
                        <img src="https://randomuser.me/api/portraits/men/75.jpg" alt="Dr. Michael Tan">
                        <div class="author-info">
                            <h4>Dr. Michael Tan</h4>
                            <p>Medical Director, City General</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Clients Section -->
    <section class="clients section-padding">
        <div class="container">
            <div class="section-title">
                <h2>Trusted By</h2>
                <p>We're proud to have worked with these respected organizations</p>
            </div>
            
            <div class="clients-slider">
                <div class="client-logo">
                    <img src="https://via.placeholder.com/150x80?text=City+Government" alt="City Government">
                </div>
                <div class="client-logo">
                    <img src="https://via.placeholder.com/150x80?text=State+University" alt="State University">
                </div>
                <div class="client-logo">
                    <img src="https://via.placeholder.com/150x80?text=National+Bank" alt="National Bank">
                </div>
                <div class="client-logo">
                    <img src="https://via.placeholder.com/150x80?text=Healthcare+System" alt="Healthcare System">
                </div>
                <div class="client-logo">
                    <img src="https://via.placeholder.com/150x80?text=Hotel+Chain" alt="Hotel Chain">
                </div>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="cta section-padding" id="contact">
        <div class="container">
            <h2>Ready to Build Your Vision?</h2>
            <p>Whether you're planning a commercial development, residential project, or infrastructure improvement, our team is ready to bring your vision to life with expertise and care.</p>
            <div class="hero-btns">
                <a href="contact.html" class="btn btn-primary">Get a Free Consultation</a>
                <a href="tel:+15551234567" class="btn">Call Us Now</a>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-container">
                <div class="footer-col">
                    <h3>About R.C Ramos</h3>
                    <p>For over 35 years, R.C Ramos Construction Corporation has been delivering exceptional construction services with integrity, quality, and innovation.</p>
                    <div class="footer-social">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="index.html">Home</a></li>
                        <li><a href="about.html">About Us</a></li>
                        <li><a href="services.html">Services</a></li>
                        <li><a href="projects.html">Projects</a></li>
                        <li><a href="contact.html">Contact</a></li>
                        <li><a href="#">Careers</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Services</h3>
                    <ul>
                        <li><a href="#">General Contracting</a></li>
                        <li><a href="#">Construction Management</a></li>
                        <li><a href="#">Design-Build</a></li>
                        <li><a href="#">Renovations</a></li>
                        <li><a href="#">Sustainable Building</a></li>
                        <li><a href="#">Infrastructure</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Contact Us</h3>
                    <ul class="footer-contact">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>123 Construction Avenue, Metro City, PH 1000</span>
                        </li>
                        <li>
                            <i class="fas fa-phone-alt"></i>
                            <span>(555) 123-4567</span>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <span>info@rcramosconstruction.com</span>
                        </li>
                        <li>
                            <i class="fas fa-clock"></i>
                            <span>Mon-Fri: 8:00 AM - 5:00 PM</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2023 R.C Ramos Construction Corporation. All Rights Reserved. | <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
            </div>
        </div>
    </footer>
    
    <script>
        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mainNav = document.getElementById('mainNav');
        
        mobileMenuBtn.addEventListener('click', () => {
            mainNav.classList.toggle('active');
            mobileMenuBtn.innerHTML = mainNav.classList.contains('active') ? 
                '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
        });
        
        // Header Scroll Effect
        const header = document.getElementById('header');
        
        window.addEventListener('scroll', () => {
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
        
        // Smooth Scrolling for Anchor Links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (this.getAttribute('href') === '#') return;
                
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    window.scrollTo({
                        top: target.offsetTop - 80,
                        behavior: 'smooth'
                    });
                    
                    // Close mobile menu if open
                    if (mainNav.classList.contains('active')) {
                        mainNav.classList.remove('active');
                        mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
                    }
                }
            });
        });
    </script>
</body>
</html>