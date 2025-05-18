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
$title = $desc = $date = $loc = $cat = $img_path = "";
$title_err = $desc_err = $date_err = $loc_err = $cat_err = $img_err = "";

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
        $desc_err = "Please enter a description.";     
    } else{
        $desc = trim($_POST["description"]);
    }
    
    // Validate date
    if(empty(trim($_POST["date"]))){
        $date_err = "Please enter a date.";     
    } else{
        $date = trim($_POST["date"]);
    }
    
    // Validate location
    if(empty(trim($_POST["location"]))){
        $loc_err = "Please enter a location.";     
    } else{
        $loc = trim($_POST["location"]);
    }
    
    // Validate category
    if(empty(trim($_POST["category"]))){
        $cat_err = "Please select a category.";     
    } else{
        $cat = trim($_POST["category"]);
    }

    // Handle image upload
    $upload_dir = "../uploads/events/";
    if(isset($_FILES["image"]) && $_FILES["image"]["error"] == 0){
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
        $filename = $_FILES["image"]["name"];
        $filetype = $_FILES["image"]["type"];
        $filesize = $_FILES["image"]["size"];
    
        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if(!array_key_exists($ext, $allowed)) {
            $img_err = "Invalid file format.";
        }
    
        // Verify file size - 5MB maximum
        $maxsize = 5 * 1024 * 1024;
        if($filesize > $maxsize) {
            $img_err = "File too large.";
        }
    
        if(empty($img_err)){
            // Generate unique filename
            $newname = uniqid() . '.' . $ext;
            $target = $upload_dir . $newname;
            
            // Ensure upload directory exists and has proper permissions
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            if(move_uploaded_file($_FILES["image"]["tmp_name"], $target)){
                $img_path = "uploads/events/" . $newname;
            } else {
                $img_err = "Upload failed.";
            }
        }
    }
    
    // Check input errors before updating the database
    if(empty($title_err) && empty($desc_err) && empty($date_err) && empty($loc_err) && empty($cat_err) && empty($img_err)){
        // Prepare an update statement
        if($img_path) {
            $q = "UPDATE events SET title=?, description=?, event_date=?, location=?, category=?, image_path=? WHERE id=?";
        } else {
            $q = "UPDATE events SET title=?, description=?, event_date=?, location=?, category=? WHERE id=?";
        }
         
        if($stmt = mysqli_prepare($conn, $q)){
            if($img_path) {
                mysqli_stmt_bind_param($stmt, "ssssssi", $title, $desc, $date, $loc, $cat, $img_path, $id);
            } else {
                mysqli_stmt_bind_param($stmt, "sssssi", $title, $desc, $date, $loc, $cat, $id);
            }
            
            if(mysqli_stmt_execute($stmt)){
                echo '<script>alert("Event updated!"); window.location.href = "dashboard.php";</script>';
                exit;
            } else{
                echo '<script>alert("Error updating event.");</script>';
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
        $q = "SELECT * FROM events WHERE id = ?";
        if($stmt = mysqli_prepare($conn, $q)){
            mysqli_stmt_bind_param($stmt, "i", $id);
            
            if(mysqli_stmt_execute($stmt)){
                $res = mysqli_stmt_get_result($stmt);
    
                if(mysqli_num_rows($res) == 1){
                    $row = mysqli_fetch_array($res, MYSQLI_ASSOC);
                    
                    $title = $row["title"];
                    $desc = $row["description"];
                    $date = $row["event_date"];
                    $loc = $row["location"];
                    $cat = $row["category"];
                    $img_path = $row["image_path"];
                } else{
                    header("location: error.php");
                    exit();
                }
                
            } else{
                echo "Something went wrong.";
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
                
                <div class="form-group <?php echo (!empty($desc_err)) ? 'has-error' : ''; ?>">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="5"><?php echo $desc; ?></textarea>
                    <span class="invalid-feedback"><?php echo $desc_err; ?></span>
                </div>
                
                <div class="form-group <?php echo (!empty($date_err)) ? 'has-error' : ''; ?>">
                    <label>Event Date</label>
                    <input type="date" name="date" class="date-input" value="<?php echo $date; ?>">
                    <span class="invalid-feedback"><?php echo $date_err; ?></span>
                </div>
                
                <div class="form-group <?php echo (!empty($loc_err)) ? 'has-error' : ''; ?>">
                    <label>Location</label>
                    <input type="text" name="location" class="form-control" value="<?php echo $loc; ?>">
                    <span class="invalid-feedback"><?php echo $loc_err; ?></span>
                </div>
                
                <div class="form-group <?php echo (!empty($cat_err)) ? 'has-error' : ''; ?>">
                    <label>Category</label>
                    <select name="category" class="category-select">
                        <option value="">Select a category</option>
                        <option value="Conference" <?php echo ($cat == "Conference") ? 'selected' : ''; ?>>Conference</option>
                        <option value="Workshop" <?php echo ($cat == "Workshop") ? 'selected' : ''; ?>>Workshop</option>
                        <option value="Seminar" <?php echo ($cat == "Seminar") ? 'selected' : ''; ?>>Seminar</option>
                        <option value="Networking" <?php echo ($cat == "Networking") ? 'selected' : ''; ?>>Networking</option>
                        <option value="Other" <?php echo ($cat == "Other") ? 'selected' : ''; ?>>Other</option>
                    </select>
                    <span class="invalid-feedback"><?php echo $cat_err; ?></span>
                </div>
                
                <div class="form-group <?php echo (!empty($img_err)) ? 'has-error' : ''; ?>">
                    <label>Event Image</label>
                    <?php if($img_path): ?>
                        <div class="current-image">
                            <p>Current Image:</p>
                            <img src="../<?php echo htmlspecialchars($img_path); ?>" alt="Current event image">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(this)">
                    <div class="image-preview" id="imagePreview">
                        <p>New Image Preview:</p>
                        <img id="preview" src="#" alt="Image preview">
                    </div>
                    <span class="invalid-feedback"><?php echo $img_err; ?></span>
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