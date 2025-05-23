<?php
require_once "../config/utils.php";
require_once "../config/database.php";

// starting my admin session
userSessionInit();

// making sure only logged in admins can see this page
if(!isset($_SESSION["isLoggedIn"]) || $_SESSION["isLoggedIn"] !== true){
    header("location: login.php");
    exit;
}

// handling event deletion when requested
if(isset($_POST['remove_event'])) {
    $eventToRemove = $_POST['event_id'];
    $removeEventQuery = "DELETE FROM events WHERE id = ?";
    if($deleteStatement = mysqli_prepare($myDatabaseLink, $removeEventQuery)) {
        mysqli_stmt_bind_param($deleteStatement, "i", $eventToRemove);
        mysqli_stmt_execute($deleteStatement);
    }
}

// fetching all events for display
$getAllEventsQuery = "SELECT * FROM events ORDER BY event_date DESC";
$eventList = mysqli_query($myDatabaseLink, $getAllEventsQuery);
if ($eventList === false) {
    die("Oops! Something went wrong while fetching events: " . mysqli_error($myDatabaseLink));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Admin Control Panel</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- My Navigation Bar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="../public/index.php" class="navbar-brand">
                <i class="fas fa-calendar-alt"></i> Community Events
            </a>
            <div class="nav-links">
                <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> My Dashboard</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-tachometer-alt"></i> My Control Panel</h1>
            <div class="admin-actions">
                <a href="add_event.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New Event
                </a>
            </div>
        </div>

        <div class="form-container">
            <?php if(mysqli_num_rows($eventList) > 0): ?>
                <table class="events-table">
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>When</th>
                            <th>Where</th>
                            <th>Type</th>
                            <th>What to Do</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($eventData = mysqli_fetch_assoc($eventList)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($eventData['title']); ?></td>
                                <td><?php echo date('F j, Y', strtotime($eventData['event_date'])); ?></td>
                                <td><?php echo htmlspecialchars($eventData['location']); ?></td>
                                <td><?php echo htmlspecialchars($eventData['category']); ?></td>
                                <td class="action-buttons">
                                    <a href="edit_event.php?id=<?php echo $eventData['id']; ?>" class="action-btn edit-btn">
                                        <i class="fas fa-edit"></i> Change
                                    </a>
                                    <form method="POST" action="dashboard.php" class="delete-form">
                                        <input type="hidden" name="event_id" value="<?php echo $eventData['id']; ?>">
                                        <button type="submit" name="remove_event" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to remove this event?')">
                                            <i class="fas fa-trash"></i> Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-events">
                    <i class="fas fa-calendar-times"></i>
                    <h3>No Events Yet</h3>
                    <p>Time to create your first event!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 