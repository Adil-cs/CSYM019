<?php
require_once "config/database.php";

// Check if the events table exists
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'events'");
if(mysqli_num_rows($table_check) == 0) {
    // Create the events table if it doesn't exist
    $create_table = "CREATE TABLE IF NOT EXISTS events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        event_date DATE NOT NULL,
        location VARCHAR(255) NOT NULL,
        category VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if(!mysqli_query($conn, $create_table)) {
        die("Error creating table: " . mysqli_error($conn));
    }
}

// Fetch all events
$sql = "SELECT e.*, COUNT(r.id) as registered_count 
        FROM events e 
        LEFT JOIN event_registrations r ON e.id = r.event_id 
        GROUP BY e.id 
        ORDER BY e.event_date ASC";
$result = mysqli_query($conn, $sql);

// Check if the query was successful
if($result === false) {
    die("Error fetching events: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Events</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        .event-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 2rem;
            width: 100%;
            max-width: 400px;
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .event-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            transition: transform 0.5s ease;
            background-color: #f8f9fa;
        }

        .event-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .event-card:hover .event-image {
            transform: scale(1.05);
        }

        .event-content {
            padding: 1.5rem;
        }

        .event-title {
            font-size: 1.5rem;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            transition: color 0.3s ease;
        }

        .event-card:hover .event-title {
            color: #3498db;
        }

        .event-meta {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            color: #7f8c8d;
        }

        .event-meta i {
            margin-right: 0.5rem;
        }

        .event-description {
            color: #34495e;
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .event-category {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: #f1f1f1;
            border-radius: 20px;
            font-size: 0.875rem;
            color: #7f8c8d;
        }

        .image-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            color: #6c757d;
        }

        .image-placeholder i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .image-error {
            color: #dc3545;
            font-size: 0.875rem;
            text-align: center;
            padding: 0.5rem;
        }

        .animate__animated {
            animation-duration: 1s;
        }

        @media (max-width: 768px) {
            .event-image {
                height: 150px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="index.php" class="navbar-brand">
                <i class="fas fa-calendar-alt"></i> Community Events
            </a>
            <div class="nav-links">
                <a href="index.php" class="active"><i class="fas fa-home"></i> All Events</a>
                <a href="upcoming.php"><i class="fas fa-list"></i> Upcoming Events</a>
                <a href="about.php"><i class="fas fa-info-circle"></i> About Us</a>
                <a href="help.php"><i class="fas fa-question-circle"></i> Help</a>
                <a href="admin/login.php"><i class="fas fa-user-shield"></i> Admin</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <header>
            <h1>Discover Community Events</h1>
            <p>Find and join exciting events in your community</p>
            <div class="search-filter">
                <input type="text" id="searchInput" placeholder="Search events...">
                <select id="categoryFilter">
                    <option value="">All Categories</option>
                    <option value="Sports">Sports</option>
                    <option value="Music">Music</option>
                    <option value="Art">Art</option>
                    <option value="Food">Food</option>
                    <option value="Technology">Technology</option>
                    <option value="Business">Business</option>
                    <option value="Education">Education</option>
                    <option value="Other">Other</option>
                </select>
            </div>
        </header>

        <div class="events-grid" id="eventsContainer">
            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <div class="event-card animate__animated animate__fadeInUp">
                        <div class="event-image">
                            <?php if(!empty($row['image_path'])): ?>
                                <?php
                                $image_path = $row['image_path'];
                                $full_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $image_path;
                                if(file_exists($full_path)) {
                                    echo '<img src="' . htmlspecialchars($image_path) . '" alt="' . htmlspecialchars($row['title']) . '">';
                                } else {
                                    echo '<div class="image-placeholder">';
                                    echo '<i class="fas fa-calendar-alt"></i>';
                                    echo '<div class="image-error">Image not found at: ' . htmlspecialchars($full_path) . '</div>';
                                    echo '</div>';
                                    error_log("Image not found at: " . $full_path);
                                }
                                ?>
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
                            <a href="event_details.php?id=<?php echo $row['id']; ?>" class="btn btn-outline">View Details</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-events animate__animated animate__fadeIn">
                    <i class="fas fa-calendar-times"></i>
                    <p>No events found. Check back later for upcoming events!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Events Section -->
    <!-- <section class="events-section">
        <div class="container">
            <h2 class="section-title animate__animated animate__fadeIn">Upcoming Events</h2>
            <div class="events-grid">
                <?php
                require_once "config/database.php";
                
                // Modified query to only show upcoming events
                $sql = "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC";
                $result = mysqli_query($conn, $sql);
                
                if(mysqli_num_rows($result) > 0){
                    while($row = mysqli_fetch_assoc($result)){
                        echo '<div class="event-card animate__animated animate__fadeInUp">';
                        
                        // Debug image path
                        $image_path = !empty($row['image_path']) ? $row['image_path'] : null;
                        
                        if($image_path && file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $image_path)) {
                            echo '<img src="' . htmlspecialchars($image_path) . '" alt="' . htmlspecialchars($row['title']) . '" class="event-image">';
                        } else {
                            echo '<div class="event-image no-image">';
                            echo '<i class="fas fa-image"></i>';
                            if($image_path) {
                                echo '<div class="image-error">Image not found: ' . htmlspecialchars($image_path) . '</div>';
                            }
                            echo '</div>';
                        }
                        
                        echo '<div class="event-content">';
                        echo '<h3 class="event-title">' . htmlspecialchars($row['title']) . '</h3>';
                        echo '<div class="event-meta">';
                        echo '<span><i class="fas fa-calendar"></i>' . date('F j, Y', strtotime($row['event_date'])) . '</span>';
                        echo '<span><i class="fas fa-map-marker-alt"></i>' . htmlspecialchars($row['location']) . '</span>';
                        echo '</div>';
                        echo '<p class="event-description">' . htmlspecialchars($row['description']) . '</p>';
                        echo '<span class="event-category">' . htmlspecialchars($row['category']) . '</span>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="no-events animate__animated animate__fadeIn">';
                    echo '<i class="fas fa-calendar-times"></i>';
                    echo '<p>No upcoming events found. Check back later for new events!</p>';
                    echo '</div>';
                }
                
                mysqli_close($conn);
                ?>
            </div>
        </div>
    </section> -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const categoryFilter = document.getElementById('categoryFilter');
            const eventCards = document.querySelectorAll('.event-card');

            function filterEvents() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedCategory = categoryFilter.value;

                eventCards.forEach(card => {
                    const title = card.querySelector('.event-title').textContent.toLowerCase();
                    const description = card.querySelector('.event-description').textContent.toLowerCase();
                    const category = card.querySelector('.event-category').textContent.toLowerCase();

                    const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
                    const matchesCategory = !selectedCategory || category === selectedCategory.toLowerCase();

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