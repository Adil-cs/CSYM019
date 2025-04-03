<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "../config/database.php";

// Handle event deletion
if(isset($_POST['delete_event'])) {
    $event_id = $_POST['event_id'];
    $sql = "DELETE FROM events WHERE id = ?";
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $event_id);
        mysqli_stmt_execute($stmt);
    }
}

// Fetch all events
$sql = "SELECT * FROM events ORDER BY event_date DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
            <h1>Admin Dashboard</h1>
            <div class="admin-actions">
                <a href="add_event.php" class="btn btn-primary">Add New Event</a>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </header>

        <div class="events-list">
            <h2>Manage Events</h2>
            <?php if(mysqli_num_rows($result) > 0): ?>
                <table class="events-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo date('F j, Y', strtotime($row['event_date'])); ?></td>
                                <td><?php echo htmlspecialchars($row['location']); ?></td>
                                <td><?php echo htmlspecialchars($row['category']); ?></td>
                                <td>
                                    <a href="edit_event.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">Edit</a>
                                    <form method="post" style="display: inline;">
                                        <input type="hidden" name="event_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="delete_event" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this event?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No events found. Add your first event!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 