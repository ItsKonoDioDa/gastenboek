<?php

include "connect.php";
session_start();

ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

  
    if ($password !== $confirmPassword) {
        $error = "Wachtwoorden komen niet overeen";
    } else {
        


    
        if ($conn->connect_error) {
            die("Database Error: " . $conn->connect_error);
        }

        $checkQuery = "SELECT * FROM users WHERE username = '$username'";
        $checkResult = $conn->query($checkQuery);

        if ($checkResult->num_rows > 0) {
            $error = "Gebruikersnaam is al in gebruik";
        } else {
   
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insertQuery = "INSERT INTO users (username, password, usertype) VALUES ('$username', '$hashedPassword', 'user')";

           
            if ($conn->query($insertQuery) === TRUE) {
         
                header("Location: login.php");
                exit();
            } else {
                $error = "Registratie mislukt";
            }
        }

        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registreren</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
<div class="container">
    <h2>Registreren</h2>
    <?php if (isset($error)) { ?>
        <p><?php echo $error; ?></p>
    <?php } ?>
    <form method="post" action="register.php">
        <label for="username">Gebruikersnaam:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Wachtwoord:</label>
        <input type="password" id="password" name="password" required>

        <label for="confirm_password">Bevestig wachtwoord:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <input type="submit" value="Registreren">
        <div>Heb je al een account? <a href="login.php">Log hier in</a></div>
    </form>
</div>
</body>
</html>
