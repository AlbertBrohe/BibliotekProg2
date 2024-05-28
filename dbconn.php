<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'bibliotek';

// Skapa anslutning
$conn = new mysqli($host, $username, $password, $database);

// Kontrollera anslutningen
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
