<?php
require_once "../config/database.php";

// Fetch future events from the database
$upcoming_query = "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC";
$upcoming_result = mysqli_query($myDatabaseLink, $upcoming_query);
if($upcoming_result === false) {
    die("Query error: " . mysqli_error($myDatabaseLink));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upcoming Events - Community Events</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<body>
    <!-- Site-wide navigation menu -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="index.php" class="navbar-brand">
                <img src="/public/assets/assets_task_01jvj0svqff7gsd86t80gqd2hj_1747582816_img_0.webp" alt="App Logo" style="height:3.5rem;width:auto;vertical-align:middle;margin-right:0.5rem;display:inline-block;"> Community Events
            </a>
            <div class="nav-links">
                <a href="index.php"><i class="fas fa-home"></i> All Events</a>
                <a href="upcoming.php" class="active"><i class="fas fa-list"></i> Upcoming Events</a>
                <a href="about.php"><i class="fas fa-info-circle"></i> About Us</a>
                <a href="help.php"><i class="fas fa-question-circle"></i> Help</a>
                <a href="../Admin/login.php"><i class="fas fa-user-shield"></i> Admin</a>
            </div>
        </div>
    </nav>

    <!-- Main content wrapper -->
    <div class="container">
        <!-- Page header with title -->
        <header>
            <h1>Upcoming Events</h1>
            <p>Discover and join upcoming events in your community</p>
        </header>

        <!-- Dynamic events display grid -->
        <div class="events-grid" id="eventsContainer">
            <?php if(mysqli_num_rows($upcoming_result) > 0): ?>
                <?php while($event = mysqli_fetch_assoc($upcoming_result)): ?>
                    <div class="event-card animate__animated animate__fadeInUp">
                        <?php if(!empty($event['image_path'])): ?>
                            <img src="/<?php echo htmlspecialchars($event['image_path']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" class="event-image">
                        <?php else: ?>
                            <div class="event-image no-image">
                                <i class="fas fa-image"></i>
                            </div>
                        <?php endif; ?>
                        <div class="event-content">
                            <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                            <div class="event-meta">
                                <span><i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($event['event_date'])); ?></span>
                                <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['location']); ?></span>
                            </div>
                            <p class="event-description"><?php echo htmlspecialchars($event['description']); ?></p>
                            <span class="event-category"><?php echo htmlspecialchars($event['category']); ?></span>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-events animate__animated animate__fadeIn">
                    <i class="fas fa-calendar-times"></i>
                    <p>No upcoming events found. Check back later for new events!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Intersection Observer for scroll animations
        document.addEventListener('DOMContentLoaded', function() {
            const eventCards = document.querySelectorAll('.event-card');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate__fadeInUp');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1
            });
            eventCards.forEach(card => {
                observer.observe(card);
            });
        });
    </script>
</body>
</html> 