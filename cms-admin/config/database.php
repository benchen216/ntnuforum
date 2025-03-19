<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'ntnu');
define('DB_PASS', 'ntnu7734');
define('DB_NAME', 'website');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
