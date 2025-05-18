<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Community Events</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="index.php" class="navbar-brand">
                <img src="/public/assets/assets_task_01jvj0svqff7gsd86t80gqd2hj_1747582816_img_0.webp" alt="App Logo" style="height:3.5rem;width:auto;vertical-align:middle;margin-right:0.5rem;display:inline-block;"> Community Events
            </a>
            <div class="nav-links">
                <a href="index.php"><i class="fas fa-home"></i> All Events</a>
                <a href="upcoming.php"><i class="fas fa-list"></i> Upcoming Events</a>
                <a href="about.php" class="active"><i class="fas fa-info-circle"></i> About Us</a>
                <a href="help.php"><i class="fas fa-question-circle"></i> Help</a>
                <a href="../Admin/login.php"><i class="fas fa-user-shield"></i> Admin</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <header>
            <h1>About Community Events</h1>
            <p>Connecting Communities Through Shared Experiences</p>
        </header>

        <section class="module-details" style="background:#f8f9fa;padding:1.5rem 2rem;margin-bottom:2rem;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.05);color:#222;">
            <h2 style="color:#0077B5;">Module & Assessment Details</h2>
            <ul style="list-style:none;padding:0;">
                <li><strong>Module Level:</strong> Level 7</li>
                <li><strong>Module Code + Name:</strong> CSYM019 | Internet Programming</li>
                <li><strong>Credit Value:</strong> 20</li>
                <li><strong>Module Leader:</strong> Dr. Ahmed Basil | <a href="mailto:ahmed.basil2@northampton.ac.uk">ahmed.basil2@northampton.ac.uk</a></li>
                <li><strong>Assessment Code + Type:</strong> AS1</li>
                <li><strong>Assessment Deliverable(s):</strong> The purpose of this assignment is to assess your ability to create a Dynamic Web Application Project.</li>
                <li><strong>Weighting (%):</strong> 100%</li>
                <li><strong>Submission Date:</strong> 23rd of May 2025 23:59 (British Time)</li>
            </ul>
            <div style="margin-top:1rem;">
                <strong>Student Name:</strong> Adil Bin Arif<br>
                <strong>Student ID:</strong> 24831748
            </div>
        </section>

        <div class="about-content animate__animated animate__fadeIn">
            <section class="about-section">
                <div class="about-card">
                    <div class="about-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h2>Our Mission</h2>
                    <p>We're dedicated to bringing communities together through meaningful events and shared experiences. Our platform makes it easy to discover, organize, and participate in local events that matter to you.</p>
                </div>
            </section>

            <section class="about-section">
                <div class="about-card">
                    <div class="about-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <h2>Our Story</h2>
                    <p>Founded in 2024, Community Events started as a small project to help local organizations connect with their communities. Today, we've grown into a platform that serves thousands of users across multiple cities, helping them discover and create memorable experiences.</p>
                </div>
            </section>

            <section class="about-section">
                <div class="about-card">
                    <div class="about-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h2>Our Team</h2>
                    <div class="team-grid">
                        <div class="team-member">
                            <div class="member-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <h3>Adil Bin Arif</h3>
                            <p>Founder & CEO</p>
                        </div>
                        <div class="team-member">
                            <div class="member-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <h3>Adil</h3>
                            <p>Event Coordinator</p>
                        </div>
                        <div class="team-member">
                            <div class="member-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <h3>Adil</h3>
                            <p>Community Manager</p>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>

    <style>
        .about-content {
            display: grid;
            gap: 2rem;
            margin-top: 2rem;
        }

        .about-section {
            width: 100%;
        }

        .about-card {
            background: #2c3e50;
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .about-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .about-icon {
            font-size: 2.5rem;
            color: white;
            margin-bottom: 1rem;
        }

        .about-card h2 {
            color: var(--text-dark);
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .about-card p {
            color: var(--text-light);
            line-height: 1.6;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-top: 1.5rem;
        }

        .team-member {
            text-align: center;
        }

        .member-avatar {
            width: 100px;
            height: 100px;
            background: var(--primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .member-avatar i {
            font-size: 2.5rem;
            color: var(--primary-color);
        }

        .team-member h3 {
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .team-member p {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 2rem;
            margin-top: 1.5rem;
        }

        .stat-item {
            text-align: center;
        }

        .stat-item h3 {
            color: var(--primary-color);
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .stat-item p {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .about-card {
                padding: 1.5rem;
            }

            .team-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</body>
</html> 