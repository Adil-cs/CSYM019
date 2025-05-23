<?php
session_start();
// Only admin can access
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
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

    <header>
        <h1>Welcome, Admin</h1>
        <nav>
            <a href="../public/index.php">Home</a>
            <a href="logout.php" class="btn">Logout</a>
        </nav>
    </header>

    <section class="admin-form">
        <h2>Manage Events</h2>
        <p>Admin panel content goes here...</p>
    </section>

</body>
</html>
