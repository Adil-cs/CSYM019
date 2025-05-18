<?php
// Initialize the session
session_start();

// Check if user is already logged in
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: dashboard.php");
    exit;
}

require_once "../config/database.php";

$user = $pass = "";
$user_err = $pass_err = $login_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Check username
    if(empty(trim($_POST["username"]))) {
        $user_err = "Please enter username.";
    } else {
        $user = trim($_POST["username"]);
    }
    // Check password
    if(empty(trim($_POST["password"]))) {
        $pass_err = "Please enter your password.";
    } else {
        $pass = trim($_POST["password"]);
    }
    // Try login
    if(empty($user_err) && empty($pass_err)){
        $q = "SELECT id, username, password FROM users WHERE username = ?";
        if($stmt = mysqli_prepare($conn, $q)){
            mysqli_stmt_bind_param($stmt, "s", $param_user);
            $param_user = $user;
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    mysqli_stmt_bind_result($stmt, $id, $user, $stored_pass);
                    if(mysqli_stmt_fetch($stmt)){
                        if($pass === $stored_pass){
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $user;
                            
                            // Redirect user to dashboard page
                            header("location: dashboard.php");
                            exit;
                        } else {
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else {
                    $login_err = "Invalid username or password.";
                }
            } else {
                echo "Something went wrong. Try again.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            background: #000;
        }
        .login-wrapper {
            min-height: 90vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            max-width: 500px;
            min-width: 400px;
            min-height: 450px;
            margin: auto;
            padding: 3rem 2.5rem;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 0 30px rgba(0,0,0,0.15);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header h2 {
            color:turquoise;
            margin-bottom: 0.5rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
            font-weight: 600;
        }
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        .form-control:focus {
            border-color: #3498db;
            outline: none;
        }
        .form-control.is-invalid {
            border-color: #e74c3c;
        }
        .invalid-feedback {
            color: #e74c3c;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .btn {
            width: 100%;
            padding: 0.75rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-primary {
            background:turquoise;
            color: white;
        }
        .btn-primary:hover {
            background: #2980b9;
        }
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="/public/index.php" class="navbar-brand">
                <img src="/public/assets/assets_task_01jvj0svqff7gsd86t80gqd2hj_1747582816_img_0.webp" alt="App Logo" style="height:3.5rem;width:auto;vertical-align:middle;margin-right:0.5rem;display:inline-block;"> Community Events
            </a>
            <div class="nav-links">
                <a href="/public/index.php"><i class="fas fa-home"></i> Home</a>
            </div>
        </div>
    </nav>
    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-header">
                <h2> Admin Login</h2>
                <p>Please fill in your credentials to login</p>
            </div>
            <?php 
            if(!empty($login_err)){
                echo '<div class="alert alert-danger">' . $login_err . '</div>';
            }        
            ?>
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn">
                    Login
                </button>
            </form>
            <div class="form-footer">
                <a href="/public/index.php">Back to Home</a>
            </div>
        </div>
    </div>
</body>
</html> 