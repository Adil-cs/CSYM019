<?php

use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
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
        $this->conn->query("DROP TABLE IF EXISTS users");
        $this->conn->close();
    }

    private function createTestTables()
    {
        // Create users table
        $this->conn->query("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }

    public function testUserRegistration()
    {
        $username = "testuser";
        $password = "testpass123";

        $stmt = $this->conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password);
        $result = $stmt->execute();
        
        $this->assertTrue($result);
        
        // Verify the user was created
        $result = $this->conn->query("SELECT * FROM users WHERE username = '$username'");
        $user = $result->fetch_assoc();
        
        $this->assertEquals($username, $user['username']);
        $this->assertEquals($password, $user['password']);
    }

    public function testUserLogin()
    {
        // First create a test user
        $username = "testuser";
        $password = "testpass123";

        $stmt = $this->conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();

        // Try to login with correct credentials
        $stmt = $this->conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $this->assertNotFalse($user);
        $this->assertEquals($username, $user['username']);
        $this->assertEquals($password, $user['password']);

        // Try to login with incorrect password
        $wrong_password = "wrongpass";
        $this->assertNotEquals($wrong_password, $user['password']);
    }

    public function testDuplicateUsername()
    {
        $username = "testuser";
        $password = "testpass123";

        // First insert
        $stmt = $this->conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password);
        $result = $stmt->execute();
        $this->assertTrue($result);

        // Try to insert the same username again
        $stmt = $this->conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password);
        $result = $stmt->execute();
        $this->assertFalse($result);
    }
} 