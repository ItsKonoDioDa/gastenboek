<?php
$servername = "localhost";
$username = "klas4s22_572922";
$password = "3mjLVs48";
$database = "klas4s22_572922";

// Verbinding maken met de database
$conn = new mysqli($servername, $username, $password, $database);

// Controleer de verbinding
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

