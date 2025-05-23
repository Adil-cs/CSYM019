<?php
require_once "../config/utils.php";
require_once "../config/database.php";

// starting my user session for tracking login state
userSessionInit();

// if user is already logged in, send them to dashboard
if(isset($_SESSION["isLoggedIn"]) && $_SESSION["isLoggedIn"] === true){
    header("location: dashboard.php");
    exit;
}

// setting up my form variables
$userLoginName = $userLoginPass = "";
$nameErrorMsg = $passErrorMsg = $loginErrorMsg = "";

// handling the form when submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // checking username field
    if(empty(trim($_POST["username"]))) {
        $nameErrorMsg = "Hey, you forgot to enter your username!";
    } else {
        $userLoginName = trim($_POST["username"]);
    }

    // checking password field
    if(empty(trim($_POST["password"]))) {
        $passErrorMsg = "Don't forget your password!";
    } else {
        $userLoginPass = trim($_POST["password"]);
    }

    // if no errors, try to log in
    if(empty($nameErrorMsg) && empty($passErrorMsg)){
        $checkUserQuery = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($userCheck = mysqli_prepare($myDatabaseLink, $checkUserQuery)){
            // setting up the username parameter
            mysqli_stmt_bind_param($userCheck, "s", $tempUsername);
            $tempUsername = $userLoginName;

            // running the query
            if(mysqli_stmt_execute($userCheck)){
                // saving the results
                mysqli_stmt_store_result($userCheck);

                // checking if we found the user
                if(mysqli_stmt_num_rows($userCheck) == 1){
                    // getting the user details
                    mysqli_stmt_bind_result($userCheck, $userID, $userName, $storedPassword);
                    
                    if(mysqli_stmt_fetch($userCheck)){
                        // checking if password matches
                        if($userLoginPass === $storedPassword){
                            // setting up the session
                            $_SESSION["isLoggedIn"] = true;
                            $_SESSION["userID"] = $userID;
                            $_SESSION["userName"] = $userName;
                            
                            // sending to dashboard
                            header("location: dashboard.php");
                            exit;
                        } else {
                            $loginErrorMsg = "Oops! Wrong username or password.";
                        }
                    }
                } else {
                    $loginErrorMsg = "Oops! Wrong username or password.";
                }
            } else {
                echo "Something went wrong. Please try again!";
            }

            // cleaning up
            mysqli_stmt_close($userCheck);
        }
    }
    
    // closing database connection
    mysqli_close($myDatabaseLink);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Login - Community Events</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Main Navigation -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="/public/index.php" class="navbar-brand">
                <i class="fas fa-calendar-alt"></i> Community Events
            </a>
            <div class="nav-links">
                <a href="/public/index.php" class="nav-item">
                    <i class="fas fa-home"></i> Return to Home
                </a>
            </div>
        </div>
    </nav>

    <!-- Login Form Container -->
    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-header">
                <h2></i>Login</h2>
                <p>Enter your credentials to access the admin dashboard</p>
            </div>

            <?php 
            if(!empty($loginErrorMsg)){
                echo '<div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> ' . $loginErrorMsg . '
                </div>';
            }        
            ?>

            <!-- Login Form -->
            <form action="login.php" method="POST" class="login-form">
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user"></i> Username
                    </label>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           class="form-control <?php echo (!empty($nameErrorMsg)) ? 'is-invalid' : ''; ?>" 
                           value="<?php echo $userLoginName; ?>" 
                           placeholder="Enter your username"
                           required>
                    <span class="invalid-feedback">
                        <i></i> <?php echo $nameErrorMsg; ?>
                    </span>
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control <?php echo (!empty($passErrorMsg)) ? 'is-invalid' : ''; ?>" 
                           placeholder="Enter your password"
                           required>
                    <span class="invalid-feedback">
                        <i></i> <?php echo $passErrorMsg; ?>
                    </span>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>

            <div class="form-footer">
                <a href="/public/index.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Back to Homepage
                </a>
            </div>
        </div>
    </div>

    <script>
        // Add any necessary JavaScript here
        document.addEventListener('DOMContentLoaded', function() {
            // Clear form on page load
            document.querySelector('.login-form').reset();
        });
    </script>
</body>
</html> 