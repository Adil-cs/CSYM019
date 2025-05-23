<?php
require_once "../config/utils.php";
require_once "../config/database.php";

// starting my admin session
userSessionInit();

// making sure only logged in admins can edit events
if(!isset($_SESSION["isLoggedIn"]) || $_SESSION["isLoggedIn"] !== true){
    header("location: login.php");
    exit;
}

// setting up my form variables
$eventName = $eventDetails = $eventWhen = $eventWhere = $eventType = $currentPicture = "";
$nameErrorMsg = $detailsErrorMsg = $whenErrorMsg = $whereErrorMsg = $typeErrorMsg = $pictureErrorMsg = "";

// handling the form when submitted
if(isset($_POST["id"]) && !empty($_POST["id"])){
    // getting the event ID
    $eventToEdit = $_POST["id"];
    
    // checking the event name
    if(empty(trim($_POST["title"]))){
        $nameErrorMsg = "Hey! Don't forget to give your event a name!";
    } else{
        $eventName = trim($_POST["title"]);
    }
    
    // checking the event description
    if(empty(trim($_POST["description"]))){
        $detailsErrorMsg = "Tell us more about your event!";     
    } else{
        $eventDetails = trim($_POST["description"]);
    }
    
    // checking the event date
    if(empty(trim($_POST["date"]))){
        $whenErrorMsg = "When is this happening?";     
    } else{
        $eventWhen = trim($_POST["date"]);
    }
    
    // checking the event location
    if(empty(trim($_POST["location"]))){
        $whereErrorMsg = "Where's the party at?";     
    } else{
        $eventWhere = trim($_POST["location"]);
    }
    
    // checking the event type
    if(empty(trim($_POST["category"]))){
        $typeErrorMsg = "What kind of event is this?";     
    } else{
        $eventType = trim($_POST["category"]);
    }

    // handling the event picture
    $myPictureFolder = "../uploads/events/";
    if(isset($_FILES["image"]) && $_FILES["image"]["error"] == 0){
        $allowedPictureTypes = array(
            "jpg" => "image/jpg", 
            "jpeg" => "image/jpeg", 
            "gif" => "image/gif", 
            "png" => "image/png"
        );
        
        $originalPictureName = $_FILES["image"]["name"];
        $pictureType = $_FILES["image"]["type"];
        $pictureSize = $_FILES["image"]["size"];
    
        // checking if the picture is valid
        $pictureExtension = pathinfo($originalPictureName, PATHINFO_EXTENSION);
        if(!array_key_exists($pictureExtension, $allowedPictureTypes)) {
            $pictureErrorMsg = "Sorry, that's not a valid picture format!";
        }
    
        // checking picture size (max 5MB)
        $maxPictureSize = 5 * 1024 * 1024;
        if($pictureSize > $maxPictureSize) {
            $pictureErrorMsg = "That picture is too big! Keep it under 5MB.";
        }
    
        if(empty($pictureErrorMsg)){
            // creating a unique name for the picture
            $uniquePictureName = uniqid() . '.' . $pictureExtension;
            $finalPicturePath = $myPictureFolder . $uniquePictureName;
            
            // creating the folder if it doesn't exist
            if (!is_dir($myPictureFolder)) {
                mkdir($myPictureFolder, 0777, true);
            }
            
            if(move_uploaded_file($_FILES["image"]["tmp_name"], $finalPicturePath)){
                $currentPicture = "uploads/events/" . $uniquePictureName;
            } else {
                $pictureErrorMsg = "Oops! Couldn't save your picture.";
            }
        }
    }
    
    // saving changes if everything is good
    if(empty($nameErrorMsg) && empty($detailsErrorMsg) && empty($whenErrorMsg) && 
       empty($whereErrorMsg) && empty($typeErrorMsg) && empty($pictureErrorMsg)){
        
        // preparing the update query based on whether there's a new picture
        if($currentPicture) {
            $updateEventQuery = "UPDATE events SET title=?, description=?, event_date=?, 
                           location=?, category=?, image_path=? WHERE id=?";
        } else {
            $updateEventQuery = "UPDATE events SET title=?, description=?, event_date=?, 
                           location=?, category=? WHERE id=?";
        }
         
        if($saveStatement = mysqli_prepare($myDatabaseLink, $updateEventQuery)){
            if($currentPicture) {
                mysqli_stmt_bind_param($saveStatement, "ssssssi", 
                    $eventName, $eventDetails, $eventWhen, $eventWhere, 
                    $eventType, $currentPicture, $eventToEdit);
            } else {
                mysqli_stmt_bind_param($saveStatement, "sssssi", 
                    $eventName, $eventDetails, $eventWhen, $eventWhere, 
                    $eventType, $eventToEdit);
            }
            
            if(mysqli_stmt_execute($saveStatement)){
                echo '<script>alert("Great! Your event was updated successfully!"); window.location.href = "dashboard.php";</script>';
                exit;
            } else{
                echo '<script>alert("Hmm, something went wrong while updating your event.");</script>';
            }

            mysqli_stmt_close($saveStatement);
        }
    }
    
    mysqli_close($myDatabaseLink);
} else {
    // getting event details for editing
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        $eventToEdit = trim($_GET["id"]);
        
        // getting the event info
        $getEventQuery = "SELECT * FROM events WHERE id = ?";
        if($fetchStatement = mysqli_prepare($myDatabaseLink, $getEventQuery)){
            mysqli_stmt_bind_param($fetchStatement, "i", $eventToEdit);
            
            if(mysqli_stmt_execute($fetchStatement)){
                $eventResults = mysqli_stmt_get_result($fetchStatement);
    
                if(mysqli_num_rows($eventResults) == 1){
                    $eventInfo = mysqli_fetch_array($eventResults, MYSQLI_ASSOC);
                    
                    $eventName = $eventInfo["title"];
                    $eventDetails = $eventInfo["description"];
                    $eventWhen = $eventInfo["event_date"];
                    $eventWhere = $eventInfo["location"];
                    $eventType = $eventInfo["category"];
                    $currentPicture = $eventInfo["image_path"];
                } else{
                    header("location: error.php");
                    exit();
                }
                
            } else{
                echo "Oops! Something went wrong while getting your event details.";
            }
        }
        
        mysqli_stmt_close($fetchStatement);
        mysqli_close($myDatabaseLink);
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
    <title>Change Event Details - Community Events</title>
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
                <a href="dashboard.php" class="nav-item">
                    <i class="fas fa-tachometer-alt"></i> My Dashboard
                </a>
                <a href="logout.php" class="nav-item">
                    <i class="fas fa-sign-out-alt"></i> Sign Out
                </a>
            </div>
        </div>
    </nav>

    <!-- my event editing form -->
    <div class="event-form">
        <div class="form-header">
            <h2><i class="fas fa-edit"></i> Change Event Details</h2>
            <p>Update your event information below</p>
        </div>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $eventToEdit; ?>"/>
            
            <div class="form-group">
                <label><i class="fas fa-heading"></i> What's Your Event Called?</label>
                <input type="text" name="title" class="form-control <?php echo (!empty($nameErrorMsg)) ? 'is-invalid' : ''; ?>" 
                       value="<?php echo $eventName; ?>" placeholder="Give your event a catchy name">
                <span class="invalid-feedback"><?php echo $nameErrorMsg; ?></span>
            </div>    
            
            <div class="form-group">
                <label><i class="fas fa-align-left"></i> Tell Us About It</label>
                <textarea name="description" class="form-control <?php echo (!empty($detailsErrorMsg)) ? 'is-invalid' : ''; ?>" 
                          rows="5" placeholder="What's happening at your event?"><?php echo $eventDetails; ?></textarea>
                <span class="invalid-feedback"><?php echo $detailsErrorMsg; ?></span>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-calendar"></i> When's It Happening?</label>
                <input type="date" name="date" class="form-control <?php echo (!empty($whenErrorMsg)) ? 'is-invalid' : ''; ?>" 
                       value="<?php echo $eventWhen; ?>">
                <span class="invalid-feedback"><?php echo $whenErrorMsg; ?></span>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-map-marker-alt"></i> Where's The Party?</label>
                <input type="text" name="location" class="form-control <?php echo (!empty($whereErrorMsg)) ? 'is-invalid' : ''; ?>" 
                       value="<?php echo $eventWhere; ?>" placeholder="Where should people go?">
                <span class="invalid-feedback"><?php echo $whereErrorMsg; ?></span>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-tags"></i> What Kind of Event?</label>
                <select name="category" class="form-control <?php echo (!empty($typeErrorMsg)) ? 'is-invalid' : ''; ?>">
                    <option value="">Pick a category</option>
                    <option value="sports" <?php echo ($eventType == "sports") ? 'selected' : ''; ?>>Sports & Games</option>
                    <option value="music" <?php echo ($eventType == "music") ? 'selected' : ''; ?>>Music & Entertainment</option>
                    <option value="art" <?php echo ($eventType == "art") ? 'selected' : ''; ?>>Art & Culture</option>
                    <option value="food" <?php echo ($eventType == "food") ? 'selected' : ''; ?>>Food & Drinks</option>
                    <option value="education" <?php echo ($eventType == "education") ? 'selected' : ''; ?>>Learning & Education</option>
                </select>
                <span class="invalid-feedback"><?php echo $typeErrorMsg; ?></span>
            </div>

            <div class="form-group">
                <label><i class="fas fa-image"></i> Change Event Picture</label>
                <?php if($currentPicture): ?>
                    <div class="current-image">
                        <img src="../<?php echo $currentPicture; ?>" alt="Current Event Picture" style="max-width: 200px;">
                        <p>Current Picture</p>
                    </div>
                <?php endif; ?>
                <div class="file-input-wrapper">
                    <button type="button" class="file-input-button">
                        <i class="fas fa-upload"></i> Pick a New Picture
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
                    <i class="fas fa-save"></i> Save Changes
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