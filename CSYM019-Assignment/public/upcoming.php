<?php
require_once "../config/database.php";

// Get upcoming events (today or later)
$q = "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC";
$res = mysqli_query($conn, $q);
if($res === false) {
    die("Query error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upcoming Events - Community Events</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        .event-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 2rem;
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .event-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.5s ease;
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

        .no-image {
            background: #f1f1f1;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #7f8c8d;
            font-size: 2rem;
            position: relative;
        }

        .image-error {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 0, 0, 0.1);
            color: #ff0000;
            font-size: 0.75rem;
            padding: 0.25rem;
            text-align: center;
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
    <!-- Navbar -->
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

    <div class="container">
        <header>
            <h1>Upcoming Events</h1>
            <p>Discover and join upcoming events in your community</p>
        </header>

        <div class="events-grid" id="eventsContainer">
            <?php if(mysqli_num_rows($res) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($res)): ?>
                    <div class="event-card animate__animated animate__fadeInUp">
                        <?php if(!empty($row['image_path'])): ?>
                            <img src="/<?php echo htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="event-image">
                        <?php else: ?>
                            <div class="event-image no-image">
                                <i class="fas fa-image"></i>
                            </div>
                        <?php endif; ?>
                        <div class="event-content">
                            <h3 class="event-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                            <div class="event-meta">
                                <span><i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($row['event_date'])); ?></span>
                                <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($row['location']); ?></span>
                            </div>
                            <p class="event-description"><?php echo htmlspecialchars($row['description']); ?></p>
                            <span class="event-category"><?php echo htmlspecialchars($row['category']); ?></span>
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
        // Animate event cards when they appear
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