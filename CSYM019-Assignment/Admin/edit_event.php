<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "../config/database.php";

// Define variables and initialize with empty values
$title = $description = $event_date = $location = $category = $image_path = "";
$title_err = $description_err = $date_err = $location_err = $category_err = $image_err = "";

// Processing form data when form is submitted
if(isset($_POST["id"]) && !empty($_POST["id"])){
    // Get hidden input value
    $id = $_POST["id"];
    
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
    if(empty(trim($_POST["date"]))){
        $date_err = "Please enter a date.";     
    } else{
        $event_date = trim($_POST["date"]);
    }
    
    // Validate location
    if(empty(trim($_POST["location"]))){
        $location_err = "Please enter a location.";     
    } else{
        $location = trim($_POST["location"]);
    }
    
    // Validate category
    if(empty(trim($_POST["category"]))){
        $category_err = "Please select a category.";     
    } else{
        $category = trim($_POST["category"]);
    }

    // Handle image upload
    $upload_dir = "/var/www/html/uploads/events/";
    if(isset($_FILES["image"]) && $_FILES["image"]["error"] == 0){
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
        $filename = $_FILES["image"]["name"];
        $filetype = $_FILES["image"]["type"];
        $filesize = $_FILES["image"]["size"];
    
        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if(!array_key_exists($ext, $allowed)) {
            $image_err = "Error: Please select a valid file format (JPG, JPEG, PNG, GIF).";
        }
    
        // Verify file size - 5MB maximum
        $maxsize = 5 * 1024 * 1024;
        if($filesize > $maxsize) {
            $image_err = "Error: File size is larger than the allowed limit of 5MB.";
        }
    
        if(empty($image_err)){
            // Generate unique filename
            $new_filename = uniqid() . '.' . $ext;
            $target_path = $upload_dir . $new_filename;
            
            // Ensure upload directory exists and has proper permissions
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_path)){
                $image_path = "uploads/events/" . $new_filename;
            } else {
                $image_err = "Error uploading file. Please try again.";
            }
        }
    }
    
    // Check input errors before updating the database
    if(empty($title_err) && empty($description_err) && empty($date_err) && empty($location_err) && empty($category_err) && empty($image_err)){
        // Prepare an update statement
        if($image_path) {
            $sql = "UPDATE events SET title=?, description=?, event_date=?, location=?, category=?, image_path=? WHERE id=?";
        } else {
            $sql = "UPDATE events SET title=?, description=?, event_date=?, location=?, category=? WHERE id=?";
        }
         
        if($stmt = mysqli_prepare($conn, $sql)){
            if($image_path) {
                mysqli_stmt_bind_param($stmt, "ssssssi", $param_title, $param_description, $param_date, $param_location, $param_category, $param_image, $param_id);
                $param_image = $image_path;
            } else {
                mysqli_stmt_bind_param($stmt, "sssssi", $param_title, $param_description, $param_date, $param_location, $param_category, $param_id);
            }
            
            $param_title = $title;
            $param_description = $description;
            $param_date = $event_date;
            $param_location = $location;
            $param_category = $category;
            $param_id = $id;
            
            if(mysqli_stmt_execute($stmt)){
                echo '<script>alert("Event updated successfully!"); window.location.href = "dashboard.php";</script>';
                exit;
            } else{
                echo '<script>alert("Error updating event. Please try again.");</script>';
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    mysqli_close($conn);
} else {
    // Check existence of id parameter before processing further
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        // Get URL parameter
        $id =  trim($_GET["id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM events WHERE id = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "i", $param_id);
            $param_id = $id;
            
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
    
                if(mysqli_num_rows($result) == 1){
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    
                    $title = $row["title"];
                    $description = $row["description"];
                    $event_date = $row["event_date"];
                    $location = $row["location"];
                    $category = $row["category"];
                    $image_path = $row["image_path"];
                } else{
                    header("location: error.php");
                    exit();
                }
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    } else{
        header("location: error.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: black;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #2c3e50;
            color: white;
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header h1 {
            margin: 0;
            font-size: 1.8rem;
        }

        .form-container {
            background-color: #2c3e50;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 2px rgba(52,152,219,0.2);
        }

        .invalid-feedback {
            color: #e74c3c;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #3498db;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .btn-secondary {
            background-color: #95a5a6;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #7f8c8d;
        }

        .current-image {
            margin-top: 1rem;
            max-width: 300px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .image-preview {
            margin-top: 1rem;
            max-width: 300px;
            display: none;
        }

        .image-preview img {
            max-width: 100%;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .category-select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            background-color: white;
        }

        .date-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        @media (max-width: 768px) {
            .form-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-edit"></i> Edit Event</h1>
        </div>
        
        <div class="form-container">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                
                <div class="form-group <?php echo (!empty($title_err)) ? 'has-error' : ''; ?>">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" value="<?php echo $title; ?>">
                    <span class="invalid-feedback"><?php echo $title_err; ?></span>
                </div>
                
                <div class="form-group <?php echo (!empty($description_err)) ? 'has-error' : ''; ?>">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="5"><?php echo $description; ?></textarea>
                    <span class="invalid-feedback"><?php echo $description_err; ?></span>
                </div>
                
                <div class="form-group <?php echo (!empty($date_err)) ? 'has-error' : ''; ?>">
                    <label>Event Date</label>
                    <input type="date" name="date" class="date-input" value="<?php echo $event_date; ?>">
                    <span class="invalid-feedback"><?php echo $date_err; ?></span>
                </div>
                
                <div class="form-group <?php echo (!empty($location_err)) ? 'has-error' : ''; ?>">
                    <label>Location</label>
                    <input type="text" name="location" class="form-control" value="<?php echo $location; ?>">
                    <span class="invalid-feedback"><?php echo $location_err; ?></span>
                </div>
                
                <div class="form-group <?php echo (!empty($category_err)) ? 'has-error' : ''; ?>">
                    <label>Category</label>
                    <select name="category" class="category-select">
                        <option value="">Select a category</option>
                        <option value="Conference" <?php echo ($category == "Conference") ? 'selected' : ''; ?>>Conference</option>
                        <option value="Workshop" <?php echo ($category == "Workshop") ? 'selected' : ''; ?>>Workshop</option>
                        <option value="Seminar" <?php echo ($category == "Seminar") ? 'selected' : ''; ?>>Seminar</option>
                        <option value="Networking" <?php echo ($category == "Networking") ? 'selected' : ''; ?>>Networking</option>
                        <option value="Other" <?php echo ($category == "Other") ? 'selected' : ''; ?>>Other</option>
                    </select>
                    <span class="invalid-feedback"><?php echo $category_err; ?></span>
                </div>
                
                <div class="form-group <?php echo (!empty($image_err)) ? 'has-error' : ''; ?>">
                    <label>Event Image</label>
                    <?php if($image_path): ?>
                        <div class="current-image">
                            <p>Current Image:</p>
                            <img src="../<?php echo htmlspecialchars($image_path); ?>" alt="Current event image">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(this)">
                    <div class="image-preview" id="imagePreview">
                        <p>New Image Preview:</p>
                        <img id="preview" src="#" alt="Image preview">
                    </div>
                    <span class="invalid-feedback"><?php echo $image_err; ?></span>
                </div>
                
                <div class="form-actions">
                    <input type="submit" class="btn btn-primary" value="Update Event">
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('preview');
            const previewContainer = document.getElementById('imagePreview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Add animation to form elements
        document.addEventListener('DOMContentLoaded', function() {
            const formGroups = document.querySelectorAll('.form-group');
            formGroups.forEach((group, index) => {
                group.style.opacity = '0';
                group.style.transform = 'translateY(20px)';
                group.style.transition = 'all 0.5s ease';
                
                setTimeout(() => {
                    group.style.opacity = '1';
                    group.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html> 