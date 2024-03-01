<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ensure all variables are defined and have values
    if (isset($_POST["naam"]) && isset($_POST["bericht"]) && isset($_SESSION['username'])) {
        $naam = $_POST["naam"];
        $bericht = $_POST["bericht"];

        include 'connect.php';

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Prepare the SQL statement
        $sql = "INSERT INTO berichten (naam, bericht, gebruiker) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        // Bind parameters to the statement
        $stmt->bind_param("sss", $naam, $bericht, $_SESSION['username']);

        // Execute the statement
        if ($stmt->execute()) {
            header("Location: index.php"); // Redirect to index page after successful insertion
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        // Close the statement and database connection
        $stmt->close();
        $conn->close();
    } else {
        // Handle cases where required variables are not set
        echo "One or more required variables are not set.";
    }
} else {
    // Redirect to index.php if accessed directly without POST method
    header("Location: index.php");
    exit();
}
?>
