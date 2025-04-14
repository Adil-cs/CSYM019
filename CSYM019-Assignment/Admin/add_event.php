<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "../config/database.php";

// Log database connection status
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$title = $description = $event_date = $location = $category = "";
$title_err = $description_err = $date_err = $location_err = $category_err = $image_err = "";
$upload_dir = "../uploads/events/";

// Create upload directory if it doesn't exist
if (!file_exists($upload_dir)) {
    if (!mkdir($upload_dir, 0777, true)) {
        $image_err = "Failed to create upload directory. Please check permissions.";
        error_log("Failed to create directory: " . $upload_dir);
    }
}

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
    $image_path = ""; // Initialize image_path variable

    if(isset($_FILES["image"]) && $_FILES["image"]["error"] == 0){
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
        $filename = $_FILES["image"]["name"];
        $filetype = $_FILES["image"]["type"];
        $filesize = $_FILES["image"]["size"];
    
        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if(!array_key_exists($ext, $allowed)) {
            $image_err = "Error: Please select a valid file format (JPG, JPEG, PNG, GIF).";
            error_log("Invalid file extension: " . $ext);
        }
    
        // Verify file size - 5MB maximum
        $maxsize = 5 * 1024 * 1024;
        if($filesize > $maxsize) {
            $image_err = "Error: File size is larger than the allowed limit of 5MB.";
            error_log("File too large: " . $filesize . " bytes");
        }
    
        if(empty($image_err)){
            // Generate unique filename
            $new_filename = uniqid() . '.' . $ext;
            $target_path = $upload_dir . $new_filename;
            
            // Ensure upload directory exists and has proper permissions
            if (!is_dir($upload_dir)) {
                if (!mkdir($upload_dir, 0777, true)) {
                    $image_err = "Failed to create upload directory. Please check permissions.";
                    error_log("Failed to create directory: " . $upload_dir);
                }
            }
            
            if(empty($image_err) && move_uploaded_file($_FILES["image"]["tmp_name"], $target_path)){
                $image_path = "uploads/events/" . $new_filename;
                error_log("Image uploaded successfully to: " . $target_path);
                error_log("Image path saved in database: " . $image_path);
                
                // Verify the file was actually moved
                if (!file_exists($target_path)) {
                    $image_err = "Error: File upload failed. Please try again.";
                    error_log("File not found after upload: " . $target_path);
                }
            } else {
                $image_err = "Error uploading file. Please try again.";
                error_log("Upload error: " . $_FILES["image"]["error"]);
                error_log("Target path: " . $target_path);
                error_log("Upload directory exists: " . (file_exists($upload_dir) ? "yes" : "no"));
                error_log("Upload directory writable: " . (is_writable($upload_dir) ? "yes" : "no"));
            }
        }
    }
    
    // Check input errors before inserting in database
    if(empty($title_err) && empty($description_err) && empty($date_err) && empty($location_err) && empty($category_err) && empty($image_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO events (title, description, event_date, location, category, image_path) VALUES (?, ?, ?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "ssssss", $param_title, $param_description, $param_date, $param_location, $param_category, $param_image);
            
            $param_title = $title;
            $param_description = $description;
            $param_date = $event_date;
            $param_location = $location;
            $param_category = $category;
            $param_image = $image_path;
            
            if(mysqli_stmt_execute($stmt)){
                echo '<script>alert("Event added successfully!"); window.location.href = "dashboard.php";</script>';
                exit;
            } else{
                echo '<script>alert("Error adding event. Please try again.");</script>';
            }

            mysqli_stmt_close($stmt);
        }
    } else {
        $error_message = "Please fix the following errors:\n";
        if(!empty($title_err)) $error_message .= "- " . $title_err . "\n";
        if(!empty($description_err)) $error_message .= "- " . $description_err . "\n";
        if(!empty($date_err)) $error_message .= "- " . $date_err . "\n";
        if(!empty($location_err)) $error_message .= "- " . $location_err . "\n";
        if(!empty($category_err)) $error_message .= "- " . $category_err . "\n";
        if(!empty($image_err)) $error_message .= "- " . $image_err . "\n";
        echo '<script>alert("' . $error_message . '");</script>';
    }
    
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event - Community Events</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .event-form {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .form-header h2 {
            color: #2c3e50;
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
        .image-preview {
            margin-top: 1rem;
            max-width: 300px;
            display: none;
        }
        .image-preview img {
            width: 100%;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .btn-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-primary {
            background: #3498db;
            color: white;
        }
        .btn-primary:hover {
            background: #2980b9;
        }
        .btn-secondary {
            background: #95a5a6;
            color: white;
        }
        .btn-secondary:hover {
            background: #7f8c8d;
        }
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }
        .file-input-wrapper input[type=file] {
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
        }
        .file-input-button {
            background: #3498db;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .file-input-button:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="../index.php" class="navbar-brand">
                <i class="fas fa-calendar-alt"></i> Community Events
            </a>
            <div class="nav-links">
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="event-form">
        <div class="form-header">
            <h2><i class="fas fa-plus-circle"></i> Add New Event</h2>
            <p>Fill in the details below to create a new event</p>
        </div>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label><i class="fas fa-heading"></i> Event Title</label>
                <input type="text" name="title" class="form-control <?php echo (!empty($title_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $title; ?>" placeholder="Enter event title">
                <span class="invalid-feedback"><?php echo $title_err; ?></span>
            </div>    
            
            <div class="form-group">
                <label><i class="fas fa-align-left"></i> Description</label>
                <textarea name="description" class="form-control <?php echo (!empty($description_err)) ? 'is-invalid' : ''; ?>" rows="5" placeholder="Enter event description"><?php echo $description; ?></textarea>
                <span class="invalid-feedback"><?php echo $description_err; ?></span>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-calendar"></i> Event Date</label>
                <input type="date" name="date" class="form-control <?php echo (!empty($date_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $event_date; ?>">
                <span class="invalid-feedback"><?php echo $date_err; ?></span>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-map-marker-alt"></i> Location</label>
                <input type="text" name="location" class="form-control <?php echo (!empty($location_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $location; ?>" placeholder="Enter event location">
                <span class="invalid-feedback"><?php echo $location_err; ?></span>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-tags"></i> Category</label>
                <select name="category" class="form-control <?php echo (!empty($category_err)) ? 'is-invalid' : ''; ?>">
                    <option value="">Select a category</option>
                    <option value="sports" <?php echo ($category == "sports") ? 'selected' : ''; ?>>Sports</option>
                    <option value="music" <?php echo ($category == "music") ? 'selected' : ''; ?>>Music</option>
                    <option value="art" <?php echo ($category == "art") ? 'selected' : ''; ?>>Art</option>
                    <option value="food" <?php echo ($category == "food") ? 'selected' : ''; ?>>Food</option>
                    <option value="education" <?php echo ($category == "education") ? 'selected' : ''; ?>>Education</option>
                </select>
                <span class="invalid-feedback"><?php echo $category_err; ?></span>
            </div>

            <div class="form-group">
                <label><i class="fas fa-image"></i> Event Image</label>
                <div class="file-input-wrapper">
                    <button type="button" class="file-input-button">
                        <i class="fas fa-upload"></i> Choose Image
                    </button>
                    <input type="file" name="image" id="image" accept="image/*" class="form-control <?php echo (!empty($image_err)) ? 'is-invalid' : ''; ?>">
                </div>
                <div class="image-preview" id="imagePreview">
                    <img src="" alt="Preview" id="previewImage">
                </div>
                <span class="invalid-feedback"><?php echo $image_err; ?></span>
            </div>
            
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Event
                </button>
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>

    <script>
        // Image preview functionality
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('previewImage');
                    preview.src = e.target.result;
                    document.getElementById('imagePreview').style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html> 