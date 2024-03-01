<?php
session_start();

include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['bericht_id'])) {
    $bericht_id = $_POST['bericht_id'];

    // Check if the user is an admin or the owner of the message
    if ($_SESSION['usertype'] == 'admin' || ($_SESSION['usertype'] != 'admin' && isset($_SESSION['username']))) {
        // Prepare and execute the SQL query to delete the message
        $sql = "DELETE FROM berichten WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $bericht_id);

        if ($stmt->execute()) {
            header("Location: index.php");
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "You do not have permission to delete this message.";
    }
} else {
    header("Location: index.php");
    exit();
}
?>
