<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "marathon_db";

// Create a connection to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Check whether the connection was successful
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Set the character encoding
$conn->set_charset("utf8mb4");
?>
