<?php
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected $conn;

    protected function setUp(): void
    {
        $host = '127.0.0.1';
        $dbname = 'community_events_test';
        $username = 'root';
        $password = 'root';
        $port = 3307;

        $this->conn = new mysqli($host, $username, $password, $dbname, $port);
        if ($this->conn->connect_error) {
            $this->fail("Connection failed: " . $this->conn->connect_error);
        }
    }

    protected function tearDown(): void
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }
} 