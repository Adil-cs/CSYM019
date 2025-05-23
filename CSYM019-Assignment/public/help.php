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
    <!-- Main navigation menu for site-wide access -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="index.php" class="navbar-brand">
                <i class="fas fa-calendar-alt"></i> Community Events
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

    <!-- Main content wrapper -->
    <div class="container">
        <header>
            <h1>Help Center</h1>
            <p>Find answers to your questions and get support</p>
        </header>

        <!-- Interactive help content area -->
        <div class="help-content animate__animated animate__fadeIn">
            <!-- Search functionality for help topics -->
            <div class="search-box">
                <input type="text" placeholder="Search for help..." class="search-input">
                <button class="search-button">
                    <i class="fas fa-search"></i>
                </button>
            </div>

            <!-- Organized help sections -->
            <div class="help-sections">
                <!-- User onboarding section -->
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

                <!-- Event management guidance -->
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

                <!-- Account management section -->
                <section class="help-section">
                    <h2><i class="fas fa-cog"></i> Account Settings</h2>
                    <div class="faq-grid">
                        <div class="faq-item">
                            <h3>How do I update my profile? (Coming Soon)</h3>
                            <p>Go to your account settings to update your profile information, change your password, or manage your notification preferences.</p>
                        </div>
                        <div class="faq-item">
                            <h3>How do I reset my password? (Coming Soon)</h3>
                            <p>Click on "Forgot Password" on the login page. Enter your email address and follow the instructions sent to your inbox.</p>
                        </div>
                        <div class="faq-item">
                            <h3>How do I delete my account? (Coming Soon)</h3>
                            <p>Go to account settings and click on "Delete Account". Note that this action cannot be undone.</p>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Support contact options -->
            <div class="contact-support">
                <h2>Still Need Help?</h2>
                <p>Our support team is here to help you 24/7</p>
                <div class="contact-methods">
                    <a href="mailto:support@communityevents.com" class="contact-button">
                        <i class="fas fa-envelope"></i> Email Support (Coming Soon)
                    </a>
                    <a href="tel:+1234567890" class="contact-button">
                        <i class="fas fa-phone"></i> Call Support (Coming Soon)
                    </a>
                    <a href="#" class="contact-button">
                        <i class="fas fa-comments"></i> Live Chat(Coming soon)
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 