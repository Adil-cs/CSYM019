<?php
require_once 'TestCase.php';

class AuthTest extends TestCase
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
        $this->conn->query("DROP TABLE IF EXISTS users");
        parent::tearDown();
    }

    private function createTestTables()
    {
        // Create users table
        $this->conn->query("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }

    public function testUserRegistration()
    {
        $username = "testuser";
        $password = "testpass";
        $email = "test@example.com";
        
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (username, password, email) VALUES ('$username', '$hashed_password', '$email')";
        $result = $this->conn->query($sql);
        $this->assertTrue($result);
    }

    public function testUserLogin()
    {
        // First register a user
        $this->testUserRegistration();
        
        $username = "testuser";
        $password = "testpass";
        
        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = $this->conn->query($sql);
        $user = $result->fetch_assoc();
        
        $this->assertTrue(password_verify($password, $user['password']));
    }

    public function testDuplicateUsername()
    {
        // First register a user
        $this->testUserRegistration();
        
        // Try to register the same username again
        $username = "testuser";
        $password = "testpass";
        $email = "test2@example.com";
        
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (username, password, email) VALUES ('$username', '$hashed_password', '$email')";
        $result = $this->conn->query($sql);
        
        // Should fail due to duplicate username
        $this->assertFalse($result);
    }
} 