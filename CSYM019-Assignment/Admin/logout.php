<?php
// starting my session to end it
userSessionInit();

// clearing all my session data
$_SESSION = array();

// ending my session completely
session_destroy();

// sending user back to login
header("location: login.php");
exit;
?> 