<?php
$servername = "localhost";
$username = "root"; // Default for local development
$password = ""; // Default for XAMPP (set password in production)
$dbname = "event_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
