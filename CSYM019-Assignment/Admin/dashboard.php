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

// Check if query was successful
if ($result === false) {
    die("Error executing query: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Community Events</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .admin-header h1 {
            color: #2c3e50;
            margin: 0;
        }
        .events-list {
            margin-top: 2rem;
        }
        .events-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        .events-table th,
        .events-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .events-table th {
            background: gray;
            font-weight: 600;
        }
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-primary {
            background: #3498db;
            color: white;
        }
        .btn-primary:hover {
            background: #2980b9;
        }
        .btn-edit {
            background: #2ecc71;
            color: white;
        }
        .btn-edit:hover {
            background: #27ae60;
        }
        .btn-delete {
            background: #e74c3c;
            color: white;
        }
        .btn-delete:hover {
            background: #c0392b;
        }
        .no-events {
            text-align: center;
            padding: 2rem;
            color: #7f8c8d;
        }
        .no-events i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="../index.php" class="navbar-brand">
                <i class="fas fa-calendar-alt"></i> Community Events
            </a>
            <div class="nav-links">
                <a href="../index.php"><i class="fas fa-home"></i> Home</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <header class="admin-header">
            <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
            <div class="admin-actions">
                <a href="add_event.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Event
                </a>
            </div>
        </header>

        <div class="events-list">
            <h2><i class="fas fa-list"></i> Manage Events</h2>
            <?php if($result && mysqli_num_rows($result) > 0): ?>
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
                                    <a href="edit_event.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form method="post" style="display: inline;">
                                        <input type="hidden" name="event_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="delete_event" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this event?')">
                                            <i class="fas fa-trash"></i> Delete
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
                    <p>No events found. Add your first event!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 