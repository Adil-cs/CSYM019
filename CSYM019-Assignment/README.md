# Community Events Management System

A web application for managing and showcasing community events, built with PHP, MySQL, HTML, CSS, and JavaScript.

## Features

- Admin interface for managing events
- Public event listing page
- Search and filter functionality
- Responsive design
- Secure admin authentication
- Image upload support

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- mod_rewrite enabled (for Apache)

## Installation

1. Clone the repository:
```bash
git clone [repository-url]
```

2. Create a MySQL database and import the schema:
```bash
mysql -u [username] -p [database_name] < database/event_management.sql
```

3. Configure the database connection:
   - Open `config/database.php`
   - Update the database credentials:
     ```php
     define('DB_SERVER', 'localhost');
     define('DB_USERNAME', 'your_username');
     define('DB_PASSWORD', 'your_password');
     define('DB_NAME', 'event_management');
     ```

4. Set up the web server:
   - Point your web server's document root to the project directory
   - Ensure the `uploads` directory is writable:
     ```bash
     chmod 777 uploads
     ```

5. Access the application:
   - Public site: `http://localhost/`
   - Admin login: `http://localhost/admin/login.php`
   - Default admin credentials:
     - Username: admin
     - Password: admin123

## Directory Structure

```
├── admin/              # Admin interface files
├── config/             # Configuration files
├── css/                # Stylesheets
├── database/           # Database schema
├── uploads/            # Uploaded images
├── index.php           # Public events page
└── README.md           # This file
```

## Usage

### Admin Interface

1. Log in using the admin credentials
2. Add new events using the "Add New Event" button
3. Edit or delete existing events from the dashboard
4. Upload event images when creating/editing events

### Public Site

1. View all upcoming events
2. Use the search box to find specific events
3. Filter events by category using the dropdown
4. Click on events to view more details

## Security

- All user inputs are sanitized
- Prepared statements are used for database queries
- Passwords are hashed using PHP's password_hash()
- Session management for admin authentication
- File upload restrictions

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details. 