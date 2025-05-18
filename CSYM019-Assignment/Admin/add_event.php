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

$title = $desc = $date = $loc = $cat = "";
$title_err = $desc_err = $date_err = $loc_err = $cat_err = $img_err = "";
$upload_dir = "/var/www/html/uploads/events/";

// Create upload directory if it doesn't exist
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Ensure upload directory has proper permissions
chmod($upload_dir, 0777);

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate fields
    $title = trim($_POST["title"]);
    $desc = trim($_POST["description"]);
    $date = trim($_POST["date"]);
    $loc = trim($_POST["location"]);
    $cat = trim($_POST["category"]);
    if(empty($title)) $title_err = "Please enter a title.";
    if(empty($desc)) $desc_err = "Please enter a description.";
    if(empty($date)) $date_err = "Please enter a date.";
    if(empty($loc)) $loc_err = "Please enter a location.";
    if(empty($cat)) $cat_err = "Please select a category.";
    // Image upload
    $img_path = "";
    if(isset($_FILES["image"]) && $_FILES["image"]["error"] == 0){
        $allowed = array("jpg"=>"image/jpg","jpeg"=>"image/jpeg","gif"=>"image/gif","png"=>"image/png");
        $filename = $_FILES["image"]["name"];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $size = $_FILES["image"]["size"];
        if(!array_key_exists($ext, $allowed)) $img_err = "Invalid file format.";
        if($size > 5*1024*1024) $img_err = "File too large.";
        if(empty($img_err)){
            $newname = uniqid().".".$ext;
            $target = $upload_dir.$newname;
            if(move_uploaded_file($_FILES["image"]["tmp_name"], $target)){
                $img_path = "uploads/events/".$newname;
            } else {
                $img_err = "Upload failed.";
            }
        }
    }
    // Insert if no errors
    if(empty($title_err) && empty($desc_err) && empty($date_err) && empty($loc_err) && empty($cat_err) && empty($img_err)){
        $q = "INSERT INTO events (title, description, event_date, location, category, image_path) VALUES (?, ?, ?, ?, ?, ?)";
        if($stmt = mysqli_prepare($conn, $q)){
            mysqli_stmt_bind_param($stmt, "ssssss", $title, $desc, $date, $loc, $cat, $img_path);
            if(mysqli_stmt_execute($stmt)){
                echo '<script>alert("Event added!"); window.location.href = "dashboard.php";</script>';
                exit;
            } else {
                echo '<script>alert("Error adding event.");</script>';
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $msg = "";
        if($title_err) $msg .= $title_err."\n";
        if($desc_err) $msg .= $desc_err."\n";
        if($date_err) $msg .= $date_err."\n";
        if($loc_err) $msg .= $loc_err."\n";
        if($cat_err) $msg .= $cat_err."\n";
        if($img_err) $msg .= $img_err."\n";
        echo '<script>alert("'.$msg.'");</script>';
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
            background: #2c3e50;
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
                <textarea name="description" class="form-control <?php echo (!empty($desc_err)) ? 'is-invalid' : ''; ?>" rows="5" placeholder="Enter event description"><?php echo $desc; ?></textarea>
                <span class="invalid-feedback"><?php echo $desc_err; ?></span>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-calendar"></i> Event Date</label>
                <input type="date" name="date" class="form-control <?php echo (!empty($date_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $date; ?>">
                <span class="invalid-feedback"><?php echo $date_err; ?></span>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-map-marker-alt"></i> Location</label>
                <input type="text" name="location" class="form-control <?php echo (!empty($loc_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $loc; ?>" placeholder="Enter event location">
                <span class="invalid-feedback"><?php echo $loc_err; ?></span>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-tags"></i> Category</label>
                <select name="category" class="form-control <?php echo (!empty($cat_err)) ? 'is-invalid' : ''; ?>">
                    <option value="">Select a category</option>
                    <option value="sports" <?php echo ($cat == "sports") ? 'selected' : ''; ?>>Sports</option>
                    <option value="music" <?php echo ($cat == "music") ? 'selected' : ''; ?>>Music</option>
                    <option value="art" <?php echo ($cat == "art") ? 'selected' : ''; ?>>Art</option>
                    <option value="food" <?php echo ($cat == "food") ? 'selected' : ''; ?>>Food</option>
                    <option value="education" <?php echo ($cat == "education") ? 'selected' : ''; ?>>Education</option>
                </select>
                <span class="invalid-feedback"><?php echo $cat_err; ?></span>
            </div>

            <div class="form-group">
                <label><i class="fas fa-image"></i> Event Image</label>
                <div class="file-input-wrapper">
                    <button type="button" class="file-input-button">
                        <i class="fas fa-upload"></i> Choose Image
                    </button>
                    <input type="file" name="image" id="image" accept="image/*" class="form-control <?php echo (!empty($img_err)) ? 'is-invalid' : ''; ?>">
                </div>
                <div class="image-preview" id="imagePreview">
                    <img src="" alt="Preview" id="previewImage">
                </div>
                <span class="invalid-feedback"><?php echo $img_err; ?></span>
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