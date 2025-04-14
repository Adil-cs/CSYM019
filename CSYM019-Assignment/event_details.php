<?php
require_once "config/database.php";

// Get event ID from URL
$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch event details
$sql = "SELECT e.*, COUNT(r.id) as registered_count 
        FROM events e 
        LEFT JOIN event_registrations r ON e.id = r.event_id 
        WHERE e.id = ? 
        GROUP BY e.id";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $event_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$event = mysqli_fetch_assoc($result);

if (!$event) {
    header("Location: index.php");
    exit();
}

// Handle registration form submission
$registration_success = false;
$registration_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    
    // Validate input
    if (empty($name) || empty($email)) {
        $registration_error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $registration_error = "Please enter a valid email address.";
    } else {
        // Check if already registered
        $check_sql = "SELECT id FROM event_registrations WHERE event_id = ? AND user_email = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "is", $event_id, $email);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);
        
        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $registration_error = "You are already registered for this event.";
        } else {
            // Register user
            $register_sql = "INSERT INTO event_registrations (event_id, user_name, user_email) VALUES (?, ?, ?)";
            $register_stmt = mysqli_prepare($conn, $register_sql);
            mysqli_stmt_bind_param($register_stmt, "iss", $event_id, $name, $email);
            
            if (mysqli_stmt_execute($register_stmt)) {
                $registration_success = true;
                // Refresh event data to update registration count
                mysqli_stmt_execute($stmt);
                $event = mysqli_fetch_assoc($result);
            } else {
                $registration_error = "Registration failed. Please try again.";
            }
        }
    }
}

// Generate social sharing URLs
$current_url = urlencode("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
$event_title = urlencode($event['title']);
$whatsapp_url = "https://wa.me/?text=" . $event_title . "%20" . $current_url;
$facebook_url = "https://www.facebook.com/sharer/sharer.php?u=" . $current_url;
$linkedin_url = "https://www.linkedin.com/shareArticle?mini=true&url=" . $current_url . "&title=" . $event_title;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event['title']); ?> - Event Details</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .event-header {
            position: relative;
            margin-bottom: 2rem;
        }

        .event-cover {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .event-cover-placeholder {
            width: 100%;
            height: 400px;
            background-color: #f8f9fa;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }

        .event-cover-placeholder i {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .event-details {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }

        .event-title {
            font-size: 2.5rem;
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        .event-meta {
            display: flex;
            gap: 2rem;
            margin-bottom: 2rem;
            color: #6c757d;
        }

        .event-meta i {
            margin-right: 0.5rem;
            color: #3498db;
        }

        .event-description {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #34495e;
            margin-bottom: 2rem;
        }

        .register-btn {
            display: inline-block;
            padding: 1rem 2rem;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .register-btn:hover {
            background: #2980b9;
        }

        .social-share {
            margin-top: 2rem;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .social-share h3 {
            margin-top: 0;
            color: #2c3e50;
        }

        .share-buttons {
            display: flex;
            gap: 1rem;
        }

        .share-button {
            padding: 0.5rem 1rem;
            border-radius: 4px;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .whatsapp {
            background-color: #25D366;
        }

        .facebook {
            background-color: #1877F2;
        }

        .linkedin {
            background-color: #0077B5;
        }

        .share-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .event-cover {
                height: 300px;
            }
            
            .event-cover-placeholder {
                height: 300px;
            }
            
            .event-title {
                font-size: 2rem;
            }
            
            .event-meta {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="index.php" class="navbar-brand">
                <i class="fas fa-calendar-alt"></i> Community Events
            </a>
            <div class="nav-links">
                <a href="index.php"><i class="fas fa-home"></i> All Events</a>
                <a href="upcoming.php"><i class="fas fa-list"></i> Upcoming Events</a>
                <a href="admin/login.php"><i class="fas fa-user-shield"></i> Admin</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="event-header">
            <?php if(!empty($event['image_path'])): ?>
                <?php
                $image_path = $event['image_path'];
                $full_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $image_path;
                if(file_exists($full_path)) {
                    echo '<img src="' . htmlspecialchars($image_path) . '" alt="' . htmlspecialchars($event['title']) . '" class="event-cover">';
                } else {
                    echo '<div class="event-cover-placeholder">';
                    echo '<i class="fas fa-calendar-alt"></i>';
                    echo '<div class="image-error">Image not found at: ' . htmlspecialchars($full_path) . '</div>';
                    echo '</div>';
                    error_log("Image not found at: " . $full_path);
                }
                ?>
            <?php else: ?>
                <div class="event-cover-placeholder">
                    <i class="fas fa-calendar-alt"></i>
                    <div>No image available</div>
                </div>
            <?php endif; ?>
        </div>

        <div class="event-details animate__animated animate__fadeIn">
            <h1 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h1>
            
            <div class="event-meta">
                <div><i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($event['event_date'])); ?></div>
                <div><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['location']); ?></div>
                <div><i class="fas fa-tag"></i> <?php echo htmlspecialchars($event['category']); ?></div>
                <div><i class="fas fa-users"></i> <?php echo $event['registered_count']; ?> Registered</div>
            </div>

            <div class="event-description">
                <?php echo nl2br(htmlspecialchars($event['description'])); ?>
            </div>

            <?php if($registration_success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Registration successful! We'll see you at the event.
                </div>
            <?php endif; ?>

            <?php if(!empty($registration_error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $registration_error; ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $event_id); ?>" method="post" class="registration-form">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <button type="submit" name="register" class="btn">
                    <i class="fas fa-user-plus"></i> Register for Event
                </button>
            </form>

            <div class="social-share">
                <h3>Share this event</h3>
                <div class="share-buttons">
                    <a href="<?php echo $whatsapp_url; ?>" class="share-button whatsapp" target="_blank">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                    <a href="<?php echo $facebook_url; ?>" class="share-button facebook" target="_blank">
                        <i class="fab fa-facebook"></i> Facebook
                    </a>
                    <a href="<?php echo $linkedin_url; ?>" class="share-button linkedin" target="_blank">
                        <i class="fab fa-linkedin"></i> LinkedIn
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.event-header, .event-details, .registration-form');
            elements.forEach((element, index) => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(20px)';
                element.style.transition = 'all 0.5s ease';
                
                setTimeout(() => {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, index * 200);
            });
        });
    </script>
</body>
</html> 