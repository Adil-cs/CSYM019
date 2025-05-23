<?php
require_once "../config/database.php";
// checking if my events table exists, if not create it
$checkTable = mysqli_query($myDatabaseLink, "SHOW TABLES LIKE 'events'");
if(mysqli_num_rows($checkTable) == 0) {
    $createTableQuery = "CREATE TABLE IF NOT EXISTS events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        event_date DATE NOT NULL,
        location VARCHAR(255) NOT NULL,
        category VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    if(!mysqli_query($myDatabaseLink, $createTableQuery)) {
        die("Table creation failed: " . mysqli_error($myDatabaseLink));
    }
}

// getting all events with their registration counts
$fetchEventsQuery = "SELECT e.*, COUNT(r.id) as registered_count FROM events e LEFT JOIN event_registrations r ON e.id = r.event_id GROUP BY e.id ORDER BY e.event_date ASC";
$eventResults = mysqli_query($myDatabaseLink, $fetchEventsQuery);
if($eventResults === false) {
    die("Query failed: " . mysqli_error($myDatabaseLink));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Events</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* Custom styles here if needed */
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="/public/index.php" class="navbar-brand">
                <img src="/public/assets/assets_task_01jvj0svqff7gsd86t80gqd2hj_1747582816_img_0.webp" alt="App Logo" style="height:3.5rem;width:auto;vertical-align:middle;margin-right:0.5rem;display:inline-block;"> Community Events
            </a>
            <div class="nav-links">
                <a href="/public/index.php" class="active"><i class="fas fa-home"></i> All Events</a>
                <a href="/public/upcoming.php"><i class="fas fa-list"></i> Upcoming Events</a>
                <a href="/public/about.php"><i class="fas fa-info-circle"></i> About Us</a>
                <a href="/public/help.php"><i class="fas fa-question-circle"></i> Help</a>
                <a href="/Admin/login.php"><i class="fas fa-user-shield"></i> Admin</a>
            </div>
        </div>
    </nav>
    <div class="container">
        <header>
            <h1>Discover Community Events</h1>
            <p>Find and join exciting events in your community</p>
        </header>
        <div class="hero-image-container">
            <div class="hero-image-wrapper">
                <img src="../uploads/events/event.jpg" alt="Community Events" class="hero-image">
                <button class="hero-button" onclick="scrollToEvents()">Join an Event</button>
                <div class="hero-overlay"></div>
            </div>
        </div>
            <div class="search-filter">
                <input type="text" id="searchInput" placeholder="Search events...">
                <select id="categoryFilter">
                    <option value="">All Categories</option>
                    <option value="sports">Sports</option>
                    <option value="music">Music</option>
                    <option value="art">Art</option>
                    <option value="food">Food</option>
                    <option value="technology">Technology</option>
                    <option value="business">Business</option>
                    <option value="education">Education</option>
                    <option value="other">Other</option>
                </select>
            </div>
        <div class="events-grid" id="eventsContainer">
            <?php if(mysqli_num_rows($eventResults) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($eventResults)): ?>
                    <a href="event_details.php?id=<?php echo $row['id']; ?>" class="event-card animate__animated animate__fadeInUp" style="text-decoration:none; color:inherit;">
                        <div class="event-image">
                            <?php if(!empty($row['image_path'])): ?>
                                <img src="/<?php echo htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="event-image">
                            <?php else: ?>
                                <div class="image-placeholder">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="event-content">
                            <h3 class="event-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                            <div class="event-meta">
                                <span><i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($row['event_date'])); ?></span>
                                <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($row['location']); ?></span>
                                <span><i class="fas fa-users"></i> <?php echo $row['registered_count']; ?> Registered</span>
                            </div>
                            <p class="event-description"><?php echo substr(htmlspecialchars($row['description']), 0, 150) . '...'; ?></p>
                            <span class="event-category"><?php echo htmlspecialchars($row['category']); ?></span>
                        </div>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-events animate__animated animate__fadeIn">
                    <i class="fas fa-calendar-times"></i>
                    <p>No events found. Check back later for upcoming events!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script>
        // Simple search and filter for events
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const categoryFilter = document.getElementById('categoryFilter');
            const eventCards = document.querySelectorAll('.event-card');
            const heroWrapper = document.querySelector('.hero-image-wrapper');
            window.addEventListener('scroll', function() {
                if (window.scrollY > 100) {
                    heroWrapper.classList.add('scrolled');
                } else {
                    heroWrapper.classList.remove('scrolled');
                }
            });
            window.scrollToEvents = function() {
                const eventsSection = document.getElementById('eventsContainer');
                eventsSection.scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
            }
            function filterEvents() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedCategory = categoryFilter.value.toLowerCase();
                eventCards.forEach(card => {
                    const title = card.querySelector('.event-title').textContent.toLowerCase();
                    const description = card.querySelector('.event-description').textContent.toLowerCase();
                    const category = card.querySelector('.event-category').textContent.toLowerCase();
                    const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
                    const matchesCategory = !selectedCategory || category === selectedCategory;
                    if (matchesSearch && matchesCategory) {
                        card.style.display = 'block';
                        card.classList.add('animate__animated', 'animate__fadeInUp');
                    } else {
                        card.style.display = 'none';
                    }
                });
            }
            searchInput.addEventListener('input', filterEvents);
            categoryFilter.addEventListener('change', filterEvents);
        });
    </script>
</body>
</html> 
</html> 