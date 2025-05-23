<?php
// my local database settings for connecting to mysql
$myDatabaseServer = getenv('DB_HOST') ?: 'db';
$myDatabaseUsername = getenv('DB_USER') ?: 'root';
$myDatabasePassword = getenv('DB_PASSWORD') ?: 'root';
$myDatabaseName = getenv('DB_NAME') ?: 'event_management';

// trying to establish connection with my database
$myDatabaseLink = mysqli_connect($myDatabaseServer, $myDatabaseUsername, $myDatabasePassword, $myDatabaseName);

// if connection fails, show what went wrong
if($myDatabaseLink === false){
    die("Oops! Couldn't connect to database. " . mysqli_connect_error());
} 