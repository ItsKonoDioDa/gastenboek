<?php

session_start();
include "connect.php";




ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // var_dump($_POST);
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];

    

    if ($conn->connect_error) {
        die("Database Error: " . $conn->connect_error);
    }

    
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row['password'];

        if (password_verify($password, $hashedPassword)) {
            if ($row['banned'] == '1') {
                $_SESSION['banned'] = true;
                header("Location: banned.php");
                exit();
            }
            $_SESSION['username'] = $username;
            $_SESSION['usertype'] = $row['usertype'];
            $_SESSION['session_id'] = session_id();

            if ($_SESSION['usertype'] == 'user') {
                header("Location: index.php");
                exit();
            } elseif ($_SESSION['usertype'] == 'admin') {
                header("Location: index.php"); // Redirect admin to index.php
                exit();
            }
        } else {
            $error = "Ongeldige gebruikersnaam of wachtwoord.";
        }
    } else {
        $error = "Ongeldige gebruikersnaam of wachtwoord.";
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Inloggen</title>
    <link rel="stylesheet" href="login.css">
</head>

<body>
    <div class="container">
        <h2>Inloggen</h2>
        <?php if (isset($error)) { ?>
            <p>
                <?php echo $error; ?>
            </p>
        <?php } ?>
        <form method="post" action="login.php">
            <label for="username">Gebruikersnaam:</label>
            <input type="text" id="username" name="username" required autocomplete="on">

            <label for="password">Wachtwoord:</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">

            <input type="submit" value="Inloggen">
            <div>Nog geen account? <a href="register.php">Maak een nieuw account aan</a></div>
        </form>
    </div>
</body>

</html>
