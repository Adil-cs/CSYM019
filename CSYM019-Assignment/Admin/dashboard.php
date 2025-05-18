<?php
session_start();
// Only logged in users can see dashboard
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
require_once "../config/database.php";
// Delete event if requested
if(isset($_POST['delete_event'])) {
    $eid = $_POST['event_id'];
    $q = "DELETE FROM events WHERE id = ?";
    if($stmt = mysqli_prepare($conn, $q)) {
        mysqli_stmt_bind_param($stmt, "i", $eid);
        mysqli_stmt_execute($stmt);
    }
}
// Get all events
$q = "SELECT * FROM events ORDER BY event_date DESC";
$res = mysqli_query($conn, $q);
if ($res === false) {
    die("Query error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #000;
        }
        .admin-container {
            max-width: 1400px;
            min-width: 900px;
            min-height: 700px;
            margin: auto;
            padding: 3rem 2.5rem;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 0 30px rgba(0,0,0,0.15);
            display: flex;
            flex-direction: column;
            justify-content: center;
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
        .action-btn {
            min-width: 100px;
            height: 40px;
            padding: 0.8rem 2.8rem;
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h2><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h2>
            <div class="admin-actions">
                <a href="add_event.php" class="btn"><i class="fas fa-plus"></i> Add Event</a>
            </div>
        </div>
        <div class="form-container">
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
                    <?php while($row = mysqli_fetch_assoc($res)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo date('F j, Y', strtotime($row['event_date'])); ?></td>
                            <td><?php echo htmlspecialchars($row['location']); ?></td>
                            <td><?php echo htmlspecialchars($row['category']); ?></td>
                            <td class="action-buttons">
                                <a href="edit_event.php?id=<?php echo $row['id']; ?>" class="action-btn edit-btn">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="dashboard.php" style="display:inline;">
                                    <input type="hidden" name="event_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="delete_event" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this event?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <div style="margin-top:2rem;text-align:right;">
            <a href="logout.php" class="btn btn-outline"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
</body>
</html> 