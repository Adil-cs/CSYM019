<?php
session_start();
include 'db_connect.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Using MD5 (use bcrypt for better security)

    // Check in the database
    $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $_SESSION['admin'] = $username; // Store session
        header("Location: admin.php"); // Redirect to admin dashboard
    } else {
        echo "<script>alert('Invalid credentials'); window.location='login.html';</script>";
    }

    $stmt->close();
}
?>
