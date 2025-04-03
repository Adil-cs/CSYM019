<?php
require_once "config/database.php";

// Fetch all events
$sql = "SELECT * FROM events ORDER BY event_date ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Events</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Community Events</h1>
            <div class="search-filter">
                <input type="text" id="searchInput" placeholder="Search events...">
                <select id="categoryFilter">
                    <option value="">All Categories</option>
                    <option value="Workshop">Workshop</option>
                    <option value="Conference">Conference</option>
                    <option value="Seminar">Seminar</option>
                    <option value="Social">Social</option>
                </select>
            </div>
        </header>

        <div class="events-grid" id="eventsContainer">
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <div class="event-card" data-category="<?php echo htmlspecialchars($row['category']); ?>">
                    <?php if($row['image_url']): ?>
                        <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                    <?php endif; ?>
                    <div class="event-details">
                        <h2><?php echo htmlspecialchars($row['title']); ?></h2>
                        <p class="event-date"><?php echo date('F j, Y', strtotime($row['event_date'])); ?> at <?php echo date('g:i A', strtotime($row['event_time'])); ?></p>
                        <p class="event-location"><?php echo htmlspecialchars($row['location']); ?></p>
                        <p class="event-category"><?php echo htmlspecialchars($row['category']); ?></p>
                        <p class="event-description"><?php echo htmlspecialchars($row['description']); ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const categoryFilter = document.getElementById('categoryFilter');
            const eventCards = document.querySelectorAll('.event-card');

            function filterEvents() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedCategory = categoryFilter.value;

                eventCards.forEach(card => {
                    const title = card.querySelector('h2').textContent.toLowerCase();
                    const description = card.querySelector('.event-description').textContent.toLowerCase();
                    const category = card.dataset.category;

                    const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
                    const matchesCategory = !selectedCategory || category === selectedCategory;

                    if (matchesSearch && matchesCategory) {
                        card.style.display = 'block';
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