<?php

use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    private $conn;

    protected function setUp(): void
    {
        require_once __DIR__ . '/../config/database_test.php';
        $this->conn = $GLOBALS['conn'];
        
        // Create test tables
        $this->createTestTables();
    }

    protected function tearDown(): void
    {
        // Clean up test data
        $this->conn->query("DROP TABLE IF EXISTS events");
        $this->conn->query("DROP TABLE IF EXISTS event_registrations");
        $this->conn->close();
    }

    private function createTestTables()
    {
        // Create events table
        $this->conn->query("CREATE TABLE IF NOT EXISTS events (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            event_date DATE NOT NULL,
            location VARCHAR(255) NOT NULL,
            category VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // Create event_registrations table
        $this->conn->query("CREATE TABLE IF NOT EXISTS event_registrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            event_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (event_id) REFERENCES events(id)
        )");
    }

    public function testCreateEvent()
    {
        $title = "Test Event";
        $description = "Test Description";
        $event_date = "2024-05-01";
        $location = "Test Location";
        $category = "Test Category";

        $stmt = $this->conn->prepare("INSERT INTO events (title, description, event_date, location, category) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $title, $description, $event_date, $location, $category);
        $result = $stmt->execute();
        
        $this->assertTrue($result);
        
        // Verify the event was created
        $result = $this->conn->query("SELECT * FROM events WHERE title = '$title'");
        $event = $result->fetch_assoc();
        
        $this->assertEquals($title, $event['title']);
        $this->assertEquals($description, $event['description']);
        $this->assertEquals($event_date, $event['event_date']);
        $this->assertEquals($location, $event['location']);
        $this->assertEquals($category, $event['category']);
    }

    public function testUpdateEvent()
    {
        // First create an event
        $title = "Original Title";
        $description = "Original Description";
        $event_date = "2024-05-01";
        $location = "Original Location";
        $category = "Original Category";

        $stmt = $this->conn->prepare("INSERT INTO events (title, description, event_date, location, category) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $title, $description, $event_date, $location, $category);
        $stmt->execute();
        $event_id = $this->conn->insert_id;

        // Update the event
        $new_title = "Updated Title";
        $new_description = "Updated Description";
        
        $stmt = $this->conn->prepare("UPDATE events SET title = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssi", $new_title, $new_description, $event_id);
        $result = $stmt->execute();
        
        $this->assertTrue($result);
        
        // Verify the update
        $result = $this->conn->query("SELECT * FROM events WHERE id = $event_id");
        $event = $result->fetch_assoc();
        
        $this->assertEquals($new_title, $event['title']);
        $this->assertEquals($new_description, $event['description']);
    }

    public function testDeleteEvent()
    {
        // First create an event
        $title = "Event to Delete";
        $description = "Description";
        $event_date = "2024-05-01";
        $location = "Location";
        $category = "Category";

        $stmt = $this->conn->prepare("INSERT INTO events (title, description, event_date, location, category) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $title, $description, $event_date, $location, $category);
        $stmt->execute();
        $event_id = $this->conn->insert_id;

        // Delete the event
        $stmt = $this->conn->prepare("DELETE FROM events WHERE id = ?");
        $stmt->bind_param("i", $event_id);
        $result = $stmt->execute();
        
        $this->assertTrue($result);
        
        // Verify the event was deleted
        $result = $this->conn->query("SELECT * FROM events WHERE id = $event_id");
        $this->assertEquals(0, $result->num_rows);
    }

    public function testEventRegistration()
    {
        // First create an event
        $title = "Event for Registration";
        $description = "Description";
        $event_date = "2024-05-01";
        $location = "Location";
        $category = "Category";

        $stmt = $this->conn->prepare("INSERT INTO events (title, description, event_date, location, category) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $title, $description, $event_date, $location, $category);
        $stmt->execute();
        $event_id = $this->conn->insert_id;

        // Register for the event
        $name = "Test User";
        $email = "test@example.com";
        $phone = "1234567890";

        $stmt = $this->conn->prepare("INSERT INTO event_registrations (event_id, name, email, phone) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $event_id, $name, $email, $phone);
        $result = $stmt->execute();
        
        $this->assertTrue($result);
        
        // Verify the registration
        $result = $this->conn->query("SELECT * FROM event_registrations WHERE event_id = $event_id");
        $registration = $result->fetch_assoc();
        
        $this->assertEquals($name, $registration['name']);
        $this->assertEquals($email, $registration['email']);
        $this->assertEquals($phone, $registration['phone']);
    }
} 