<?php
require_once "../config/utils.php";
require_once "../config/database.php";

// showing all errors while i'm working on this
error_reporting(E_ALL);
ini_set('display_errors', 1);

// starting my admin session
userSessionInit();

// checking if admin is logged in
if(!isset($_SESSION["isLoggedIn"]) || $_SESSION["isLoggedIn"] !== true){
    header("location: login.php");
    exit;
}

// checking if my database is connected
if (!$myDatabaseLink) {
    die("Oops! Can't connect to my database: " . mysqli_connect_error());
}

// setting up my form variables
$newEventName = $newEventDetails = $newEventWhen = $newEventWhere = $newEventType = "";
$nameErrorMsg = $detailsErrorMsg = $whenErrorMsg = $whereErrorMsg = $typeErrorMsg = $pictureErrorMsg = "";
$myPictureFolder = "/var/www/html/uploads/events/";

// creating folder for pictures if it doesn't exist
if (!file_exists($myPictureFolder)) {
    mkdir($myPictureFolder, 0777, true);
}
chmod($myPictureFolder, 0777);

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // getting form data
    $newEventName = trim($_POST["title"]);
    $newEventDetails = trim($_POST["description"]);
    $newEventWhen = trim($_POST["date"]);
    $newEventWhere = trim($_POST["location"]);
    $newEventType = trim($_POST["category"]);

    // checking if all required fields are filled
    if(empty($newEventName)) $nameErrorMsg = "Hey! Don't forget to give your event a name!";
    if(empty($newEventDetails)) $detailsErrorMsg = "Tell us more about your event!";
    if(empty($newEventWhen)) $whenErrorMsg = "When is this happening?";
    if(empty($newEventWhere)) $whereErrorMsg = "Where's the party at?";
    if(empty($newEventType)) $typeErrorMsg = "What kind of event is this?";

    // handling the event picture
    $savedPicturePath = "";
    if(isset($_FILES["image"]) && $_FILES["image"]["error"] == 0){
        $allowedPictureTypes = array(
            "jpg" => "image/jpg",
            "jpeg" => "image/jpeg",
            "gif" => "image/gif",
            "png" => "image/png"
        );
        
        $originalPictureName = $_FILES["image"]["name"];
        $pictureExtension = pathinfo($originalPictureName, PATHINFO_EXTENSION);
        $pictureSize = $_FILES["image"]["size"];

        // checking if the picture is valid
        if(!array_key_exists($pictureExtension, $allowedPictureTypes)) {
            $pictureErrorMsg = "Sorry, that's not a valid picture format!";
        }
        if($pictureSize > 5*1024*1024) {
            $pictureErrorMsg = "That picture is too big! Keep it under 5MB.";
        }

        // saving the picture if it's valid
        if(empty($pictureErrorMsg)){
            $uniquePictureName = uniqid().".".$pictureExtension;
            $finalPicturePath = $myPictureFolder.$uniquePictureName;
            
            if(move_uploaded_file($_FILES["image"]["tmp_name"], $finalPicturePath)){
                $savedPicturePath = "uploads/events/".$uniquePictureName;
            } else {
                $pictureErrorMsg = "Oops! Couldn't save your picture.";
            }
        }
    }

    // saving the event if everything is good
    if(empty($nameErrorMsg) && empty($detailsErrorMsg) && empty($whenErrorMsg) && 
       empty($whereErrorMsg) && empty($typeErrorMsg) && empty($pictureErrorMsg)){
        
        $saveEventQuery = "INSERT INTO events (title, description, event_date, location, category, image_path) 
                       VALUES (?, ?, ?, ?, ?, ?)";
        
        if($saveStatement = mysqli_prepare($myDatabaseLink, $saveEventQuery)){
            mysqli_stmt_bind_param($saveStatement, "ssssss", 
                $newEventName, $newEventDetails, $newEventWhen, $newEventWhere, 
                $newEventType, $savedPicturePath);
            
            if(mysqli_stmt_execute($saveStatement)){
                echo '<script>alert("Yay! Your event was created successfully!"); window.location.href = "dashboard.php";</script>';
                exit;
            } else {
                echo '<script>alert("Hmm, something went wrong while saving your event.");</script>';
            }
            mysqli_stmt_close($saveStatement);
        }
    } else {
        // collecting all error messages
        $allErrorMessages = "";
        if($nameErrorMsg) $allErrorMessages .= $nameErrorMsg."\n";
        if($detailsErrorMsg) $allErrorMessages .= $detailsErrorMsg."\n";
        if($whenErrorMsg) $allErrorMessages .= $whenErrorMsg."\n";
        if($whereErrorMsg) $allErrorMessages .= $whereErrorMsg."\n";
        if($typeErrorMsg) $allErrorMessages .= $typeErrorMsg."\n";
        if($pictureErrorMsg) $allErrorMessages .= $pictureErrorMsg."\n";
        echo '<script>alert("'.$allErrorMessages.'");</script>';
    }
    mysqli_close($myDatabaseLink);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Event - Community Events</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- my navigation menu -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="../index.php" class="navbar-brand">
                <i class="fas fa-calendar-alt"></i> Community Events
            </a>
            <div class="nav-links">
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> My Dashboard</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
            </div>
        </div>
    </nav>

    <!-- my event creation form -->
    <div class="event-form">
        <div class="form-header">
            <h2><i class="fas fa-plus-circle"></i> Create New Event</h2>
            <p>Tell us all about your awesome event!</p>
        </div>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label><i class="fas fa-heading"></i> What's Your Event Called?</label>
                <input type="text" name="title" class="form-control <?php echo (!empty($nameErrorMsg)) ? 'is-invalid' : ''; ?>" 
                       value="<?php echo $newEventName; ?>" placeholder="Give your event a catchy name">
                <span class="invalid-feedback"><?php echo $nameErrorMsg; ?></span>
            </div>    
            
            <div class="form-group">
                <label><i class="fas fa-align-left"></i> Tell Us About It</label>
                <textarea name="description" class="form-control <?php echo (!empty($detailsErrorMsg)) ? 'is-invalid' : ''; ?>" 
                          rows="5" placeholder="What's happening at your event?"><?php echo $newEventDetails; ?></textarea>
                <span class="invalid-feedback"><?php echo $detailsErrorMsg; ?></span>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-calendar"></i> When's It Happening?</label>
                <input type="date" name="date" class="form-control <?php echo (!empty($whenErrorMsg)) ? 'is-invalid' : ''; ?>" 
                       value="<?php echo $newEventWhen; ?>">
                <span class="invalid-feedback"><?php echo $whenErrorMsg; ?></span>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-map-marker-alt"></i> Where's The Party?</label>
                <input type="text" name="location" class="form-control <?php echo (!empty($whereErrorMsg)) ? 'is-invalid' : ''; ?>" 
                       value="<?php echo $newEventWhere; ?>" placeholder="Where should people go?">
                <span class="invalid-feedback"><?php echo $whereErrorMsg; ?></span>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-tags"></i> What Kind of Event?</label>
                <select name="category" class="form-control <?php echo (!empty($typeErrorMsg)) ? 'is-invalid' : ''; ?>">
                    <option value="">Pick a category</option>
                    <option value="sports" <?php echo ($newEventType == "sports") ? 'selected' : ''; ?>>Sports & Games</option>
                    <option value="music" <?php echo ($newEventType == "music") ? 'selected' : ''; ?>>Music & Entertainment</option>
                    <option value="art" <?php echo ($newEventType == "art") ? 'selected' : ''; ?>>Art & Culture</option>
                    <option value="food" <?php echo ($newEventType == "food") ? 'selected' : ''; ?>>Food & Drinks</option>
                    <option value="education" <?php echo ($newEventType == "education") ? 'selected' : ''; ?>>Learning & Education</option>
                </select>
                <span class="invalid-feedback"><?php echo $typeErrorMsg; ?></span>
            </div>

            <div class="form-group">
                <label><i class="fas fa-image"></i> Add a Cool Picture</label>
                <div class="file-input-wrapper">
                    <button type="button" class="file-input-button">
                        <i class="fas fa-upload"></i> Pick a Picture
                    </button>
                    <input type="file" name="image" id="image" accept="image/*" 
                           class="form-control <?php echo (!empty($pictureErrorMsg)) ? 'is-invalid' : ''; ?>">
                </div>
                <div class="image-preview" id="imagePreview">
                    <img src="" alt="Preview" id="previewImage">
                </div>
                <span class="invalid-feedback"><?php echo $pictureErrorMsg; ?></span>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save My Event
                </button>
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>

    <script>
        // showing picture preview when selected
        document.getElementById('image').addEventListener('change', function(e) {
            const preview = document.getElementById('previewImage');
            const file = e.target.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }

            if(file) {
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html> 