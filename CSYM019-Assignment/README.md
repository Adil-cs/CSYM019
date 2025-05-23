# Community Events Management System

A web-based application for managing and promoting community events. This system allows administrators to create, edit, and manage events while providing a user-friendly interface for community members to view and register for events.

## Features

- **Public Features**
  - View all events
  - Filter upcoming events
  - Event registration
  - Social media sharing
  - Event details with images
  - Responsive design for all devices

- **Admin Features**
  - Secure login system
  - Dashboard for event management
  - Create new events
  - Edit existing events
  - Delete events
  - Image upload for events
  - Event categorization

## Technical Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Additional Libraries**:
  - Font Awesome 6.0.0 (Icons)
  - Animate.css 4.1.1 (Animations)

## Project Structure

```
├── Admin/                 # Admin panel files
│   ├── add_event.php     # Create new events
│   ├── dashboard.php     # Admin dashboard
│   ├── edit_event.php    # Edit existing events
│   ├── login.php         # Admin login
│   └── logout.php        # Admin logout
├── config/               # Configuration files
│   ├── database.php      # Database connection
│   └── utils.php         # Utility functions
├── css/                  # Stylesheets
│   └── style.css         # Main stylesheet
├── public/               # Public-facing files
│   ├── about.php         # About page
│   ├── event_details.php # Event details page
│   ├── help.php          # Help page
│   ├── index.php         # Homepage
│   └── upcoming.php      # Upcoming events page
├── uploads/              # Uploaded files
│   └── events/           # Event images
├── docker/               # Docker configuration
│   ├── Dockerfile        # PHP container setup
│   └── docker-compose.yml # Docker services
└── README.md            # This file
```

## Setup Instructions

### Using Docker (Recommended)

1. Ensure Docker and Docker Compose are installed on your system
2. Clone the repository
3. Navigate to the project root directory
4. Run the following command:
   ```bash
   docker-compose -f docker/docker-compose.yml up --build
   ```
5. Access the application at `http://localhost:8000`

### Manual Setup

1. Ensure PHP 7.4+ and MySQL 5.7+ are installed
2. Clone the repository
3. Create a MySQL database named `community_events`
4. Import the database schema (if provided)
5. Configure the database connection in `config/database.php`:
   ```php
   $db_server = "localhost";
   $db_username = "your_username";
   $db_password = "your_password";
   $db_name = "community_events";
   ```
6. Set up a web server (Apache/Nginx) pointing to the project directory
7. Ensure the `uploads/events` directory is writable:
   ```bash
   chmod 777 uploads/events
   ```

## Database Configuration

The application uses MySQL with the following tables:

- `events`: Stores event information
  - id (Primary Key)
  - title
  - description
  - event_date
  - location
  - category
  - image_path

- `event_registrations`: Stores event registrations
  - id (Primary Key)
  - event_id (Foreign Key)
  - name
  - email
  - phone

- `users`: Stores admin user information
  - id (Primary Key)
  - username
  - password

## Security Features

- Password hashing for admin accounts
- Prepared statements for all database queries
- Input validation and sanitization
- Session management
- File upload validation
- XSS protection

## File Upload Configuration

- Maximum file size: 5MB
- Allowed file types: JPG, JPEG, PNG, GIF
- Upload directory: `uploads/events/`
- Files are renamed with unique identifiers

## Admin Access

Default admin credentials (change after first login):
- Username: admin
- Password: admin123

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, please contact the development team or create an issue in the repository. 