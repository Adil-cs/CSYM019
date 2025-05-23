<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Our Platform - Community Events</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Global navigation menu -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="index.php" class="navbar-brand">
                <i class="fas fa-calendar-alt"></i> Community Events
            </a>
            <div class="nav-links">
                <a href="index.php" class="nav-item"><i class="fas fa-home"></i> All Events</a>
                <a href="upcoming.php" class="nav-item"><i class="fas fa-list"></i> Upcoming Events</a>
                <a href="about.php" class="active nav-item"><i class="fas fa-info-circle"></i> About Us</a>
                <a href="help.php" class="nav-item"><i class="fas fa-question-circle"></i> Help</a>
                <a href="../Admin/login.php" class="nav-item"><i class="fas fa-user-shield"></i> Admin</a>
            </div>
        </div>
    </nav>

    <!-- Primary content container -->
    <div class="container">
        <header>
            <h1>Welcome to Community Events</h1>
            <p>Building Stronger Communities Through Shared Experiences</p>
        </header>

        <!-- Academic information section -->
        <section class="module-details">
            <h2>Academic Information</h2>
            <ul class="module-list">
                <li><strong>Academic Level:</strong> Level 7</li>
                <li><strong>Course Code:</strong> CSYM019 | Internet Programming</li>
                <li><strong>Academic Credits:</strong> 20</li>
                <li><strong>Course Instructor:</strong> Dr. Ahmed Basil | <a href="mailto:ahmed.basil2@northampton.ac.uk">ahmed.basil2@northampton.ac.uk</a></li>
                <li><strong>Assessment Code:</strong> AS1</li>
                <li><strong>Project Objective:</strong> Development of a Dynamic Web Application for Community Event Management</li>
                <li><strong>Assessment Weight:</strong> 100%</li>
                <li><strong>Submission Deadline:</strong> 23rd of May 2025 23:59 (British Time)</li>
            </ul>
            <div class="student-info">
                <strong>Developer:</strong> Adil Bin Arif<br>
                <strong>Student ID:</strong> 24831748
            </div>
        </section>

        <!-- Dynamic content area with animations -->
        <div class="about-content animate__animated animate__fadeIn">
            <!-- Vision statement section -->
            <section class="about-section">
                <div class="about-card">
                    <div class="about-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h2>Our Vision</h2>
                    <p>We strive to create a vibrant platform where communities can thrive through meaningful connections and shared experiences. Our goal is to make event discovery and participation seamless, fostering stronger community bonds and creating lasting memories.</p>
                </div>
            </section>

            <!-- Platform history section -->
            <section class="about-section">
                <div class="about-card">
                    <div class="about-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <h2>Platform Evolution</h2>
                    <p>Launched in 2024, our platform began as a solution to bridge the gap between event organizers and community members. Through continuous innovation and user feedback, we've evolved into a comprehensive platform that empowers communities to create, discover, and participate in meaningful events.</p>
                </div>
            </section>

            <!-- Team information section -->
            <section class="about-section">
                <div class="about-card">
                    <div class="about-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h2>Development Team</h2>
                    <div class="team-grid">
                        <div class="team-member">
                            <div class="member-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <h3>Adil Bin Arif</h3>
                            <p>Lead Developer</p>
                        </div>
                        <div class="team-member">
                            <div class="member-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <h3>Adil</h3>
                            <p>UI/UX Designer</p>
                        </div>
                        <div class="team-member">
                            <div class="member-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <h3>Adil</h3>
                            <p>Project Manager</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script>
        // Smooth scrolling implementation for navigation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html> 