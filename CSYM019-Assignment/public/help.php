<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Center - Community Events</title>
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
                <a href="about.php"><i class="fas fa-info-circle"></i> About Us</a>
                <a href="help.php" class="active"><i class="fas fa-question-circle"></i> Help</a>
                <a href="../Admin/login.php"><i class="fas fa-user-shield"></i> Admin</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <header>
            <h1>Help Center</h1>
            <p>Find answers to your questions and get support</p>
        </header>

        <div class="help-content animate__animated animate__fadeIn">
            <div class="search-box">
                <input type="text" placeholder="Search for help..." class="search-input">
                <button class="search-button">
                    <i class="fas fa-search"></i>
                </button>
            </div>

            <div class="help-sections">
                <section class="help-section">
                    <h2><i class="fas fa-user-plus"></i> Getting Started</h2>
                    <div class="faq-grid">
                        <div class="faq-item">
                            <h3>How do I create an account?</h3>
                            <p>To create an account, click on the "Sign Up" button in the top right corner. Fill in your details and verify your email address to get started.</p>
                        </div>
                        <div class="faq-item">
                            <h3>How do I find events?</h3>
                            <p>You can browse events on the home page or use the search bar to find specific events. Filter by category, date, or location to narrow down your search.</p>
                        </div>
                        <div class="faq-item">
                            <h3>How do I register for an event?</h3>
                            <p>Click on any event to view its details, then click the "Register" button. Fill in your information and confirm your registration.</p>
                        </div>
                    </div>
                </section>

                <section class="help-section">
                    <h2><i class="fas fa-calendar-check"></i> Event Management</h2>
                    <div class="faq-grid">
                        <div class="faq-item">
                            <h3>How do I create an event?</h3>
                            <p>Log in to your account, go to the admin dashboard, and click "Create Event". Fill in the event details, upload images, and set the date and location.</p>
                        </div>
                        <div class="faq-item">
                            <h3>Can I edit my event after creating it?</h3>
                            <p>Yes, you can edit your event details at any time through the admin dashboard. Changes will be reflected immediately on the event page.</p>
                        </div>
                        <div class="faq-item">
                            <h3>How do I manage event registrations?</h3>
                            <p>In the admin dashboard, you can view all registrations, export attendee lists, and send notifications to registered users.</p>
                        </div>
                    </div>
                </section>

                <section class="help-section">
                    <h2><i class="fas fa-cog"></i> Account Settings</h2>
                    <div class="faq-grid">
                        <div class="faq-item">
                            <h3>How do I update my profile?</h3>
                            <p>Go to your account settings to update your profile information, change your password, or manage your notification preferences.</p>
                        </div>
                        <div class="faq-item">
                            <h3>How do I reset my password?</h3>
                            <p>Click on "Forgot Password" on the login page. Enter your email address and follow the instructions sent to your inbox.</p>
                        </div>
                        <div class="faq-item">
                            <h3>How do I delete my account?</h3>
                            <p>Go to account settings and click on "Delete Account". Note that this action cannot be undone.</p>
                        </div>
                    </div>
                </section>
            </div>

            <div class="contact-support">
                <h2>Still Need Help?</h2>
                <p>Our support team is here to help you 24/7</p>
                <div class="contact-methods">
                    <a href="mailto:support@communityevents.com" class="contact-button">
                        <i class="fas fa-envelope"></i> Email Support
                    </a>
                    <a href="tel:+1234567890" class="contact-button">
                        <i class="fas fa-phone"></i> Call Support
                    </a>
                    <a href="#" class="contact-button">
                        <i class="fas fa-comments"></i> Live Chat(Coming soon)
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .help-content {
            margin-top: 2rem;
        }

        .search-box {
            display: flex;
            max-width: 600px;
            margin: 0 auto 3rem;
        }

        .search-input {
            flex: 1;
            padding: 1rem;
            border: 2px solid var(--primary-light);
            border-radius: var(--border-radius) 0 0 var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .search-button {
            background: var(--primary-color);
            color: var(--white);
            border: none;
            padding: 0 1.5rem;
            border-radius: 0 var(--border-radius) var(--border-radius) 0;
            cursor: pointer;
            transition: var(--transition);
        }

        .search-button:hover {
            background: var(--primary-dark);
        }

        .help-sections {
            display: grid;
            gap: 3rem;
        }

        .help-section {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
        }

        .help-section h2 {
            color: var(--text-dark);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .help-section h2 i {
            color: var(--primary-color);
        }

        .faq-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .faq-item {
            background: rgba(255, 255, 255, 0.8);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            border: 1px solid var(--primary-light);
        }

        .faq-item h3 {
            color: var(--text-dark);
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .faq-item p {
            color: var(--text-light);
            line-height: 1.6;
        }

        .contact-support {
            text-align: center;
            margin-top: 4rem;
            padding: 3rem;
            background: rgba(255, 255, 255, 0.95);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
        }

        .contact-support h2 {
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        .contact-support p {
            color: var(--text-light);
            margin-bottom: 2rem;
        }

        .contact-methods {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .contact-button {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            background: var(--primary-color);
            color: var(--white);
            border-radius: var(--border-radius);
            text-decoration: none;
            transition: var(--transition);
        }

        .contact-button:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .search-box {
                margin-bottom: 2rem;
            }

            .help-section {
                padding: 1.5rem;
            }

            .faq-grid {
                grid-template-columns: 1fr;
            }

            .contact-methods {
                flex-direction: column;
            }

            .contact-button {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</body>
</html> 