<?php
// Get environment variables
$db_host = getenv('DB_HOST') ?: 'db';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASSWORD') ?: 'root';
$db_name = getenv('DB_NAME') ?: 'event_management';

// Attempt to connect to MySQL database
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check connection
if($conn === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
} 