$host = '127.0.0.1';
$dbname = 'community_events_test'; // or whatever your test database is called
$username = 'root';
$password = 'root';
$port = 3307;

try {
    $GLOBALS['conn'] = new mysqli($host, $username, $password, $dbname, $port);
    if ($GLOBALS['conn']->connect_error) {
        throw new Exception("Connection failed: " . $GLOBALS['conn']->connect_error);
    }
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}