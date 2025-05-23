<?php
require_once 'TestCase.php';

class EventTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test tables
        $this->createTestTables();
    }

    protected function tearDown(): void
    {
        // Clean up test data
        $this->conn->query("DROP TABLE IF EXISTS events");
        $this->conn->query("DROP TABLE IF EXISTS event_registrations");
        parent::tearDown();
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
        $sql = "INSERT INTO events (title, description, event_date, location, category) VALUES ('Test Event', 'Test Description', '2024-06-01', 'Test Location', 'Test Category')";
        $result = $this->conn->query($sql);
        $this->assertTrue($result);
    }

    public function testUpdateEvent()
    {
        // First create an event
        $this->testCreateEvent();
        
        // Get the last inserted ID
        $eventId = $this->conn->insert_id;
        
        // Update the event
        $sql = "UPDATE events SET title = 'Updated Event' WHERE id = $eventId";
        $result = $this->conn->query($sql);
        $this->assertTrue($result);
    }

    public function testDeleteEvent()
    {
        // First create an event
        $this->testCreateEvent();
        
        // Get the last inserted ID
        $eventId = $this->conn->insert_id;
        
        // Delete the event
        $sql = "DELETE FROM events WHERE id = $eventId";
        $result = $this->conn->query($sql);
        $this->assertTrue($result);
    }

    public function testEventRegistration()
    {
        // First create an event
        $this->testCreateEvent();
        
        // Get the last inserted ID
        $eventId = $this->conn->insert_id;
        
        // Register a user for the event
        $sql = "INSERT INTO event_registrations (event_id, name, email, phone) VALUES ($eventId, 'Test User', 'test@example.com', '1234567890')";
        $result = $this->conn->query($sql);
        $this->assertTrue($result);
    }
} 