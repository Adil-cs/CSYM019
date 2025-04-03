<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "../config/database.php";

$title = $description = $event_date = $event_time = $location = $category = $image_url = "";
$title_err = $description_err = $date_err = $time_err = $location_err = $category_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate title
    if(empty(trim($_POST["title"]))){
        $title_err = "Please enter a title.";
    } else{
        $title = trim($_POST["title"]);
    }
    
    // Validate description
    if(empty(trim($_POST["description"]))){
        $description_err = "Please enter a description.";
    } else{
        $description = trim($_POST["description"]);
    }
    
    // Validate date
    if(empty(trim($_POST["event_date"]))){
        $date_err = "Please enter a date.";
    } else{
        $event_date = trim($_POST["event_date"]);
    }
    
    // Validate time
    if(empty(trim($_POST["event_time"]))){
        $time_err = "Please enter a time.";
    } else{
        $event_time = trim($_POST["event_time"]);
    }
    
    // Validate location
    if(empty(trim($_POST["location"]))){
        $location_err = "Please enter a location.";
    } else{
        $location = trim($_POST["location"]);
    }
    
    // Validate category
    if(empty(trim($_POST["category"]))){
        $category_err = "Please enter a category.";
    } else{
        $category = trim($_POST["category"]);
    }
    
    // Handle image upload
    if(isset($_FILES["image"])){
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check !== false) {
            if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_url = "uploads/" . basename($_FILES["image"]["name"]);
            }
        }
    }
    
    // Check input errors before inserting in database
    if(empty($title_err) && empty($description_err) && empty($date_err) && empty($time_err) && empty($location_err) && empty($category_err)){
        $sql = "INSERT INTO events (title, description, event_date, event_time, location, category, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "sssssss", $param_title, $param_description, $param_date, $param_time, $param_location, $param_category, $param_image);
            
            $param_title = $title;
            $param_description = $description;
            $param_date = $event_date;
            $param_time = $event_time;
            $param_location = $location;
            $param_category = $category;
            $param_image = $image_url;
            
            if(mysqli_stmt_execute($stmt)){
                header("location: dashboard.php");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
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
    <title>Add New Event</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
            <h1>Add New Event</h1>
            <div class="admin-actions">
                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </header>

        <div class="form-container">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control <?php echo (!empty($title_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $title; ?>">
                    <span class="invalid-feedback"><?php echo $title_err; ?></span>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control <?php echo (!empty($description_err)) ? 'is-invalid' : ''; ?>"><?php echo $description; ?></textarea>
                    <span class="invalid-feedback"><?php echo $description_err; ?></span>
                </div>
                
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="event_date" class="form-control <?php echo (!empty($date_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $event_date; ?>">
                    <span class="invalid-feedback"><?php echo $date_err; ?></span>
                </div>
                
                <div class="form-group">
                    <label>Time</label>
                    <input type="time" name="event_time" class="form-control <?php echo (!empty($time_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $event_time; ?>">
                    <span class="invalid-feedback"><?php echo $time_err; ?></span>
                </div>
                
                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" class="form-control <?php echo (!empty($location_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $location; ?>">
                    <span class="invalid-feedback"><?php echo $location_err; ?></span>
                </div>
                
                <div class="form-group">
                    <label>Category</label>
                    <select name="category" class="form-control <?php echo (!empty($category_err)) ? 'is-invalid' : ''; ?>">
                        <option value="">Select Category</option>
                        <option value="Workshop">Workshop</option>
                        <option value="Conference">Conference</option>
                        <option value="Seminar">Seminar</option>
                        <option value="Social">Social</option>
                    </select>
                    <span class="invalid-feedback"><?php echo $category_err; ?></span>
                </div>
                
                <div class="form-group">
                    <label>Event Image</label>
                    <input type="file" name="image" class="form-control">
                </div>
                
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Add Event">
                </div>
            </form>
        </div>
    </div>
</body>
</html> 