<?php
session_start();

include 'connect.php';

// Check if user is not logged in, redirect to login.php
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Handle ban/unban logic
if ($_SESSION['usertype'] == 'admin' && isset($_POST['user_id']) && isset($_POST['ban_status'])) {
    $user_id = $_POST['user_id'];
    $ban_status = $_POST['ban_status'];

    // Update the 'banned' column in the 'users' table
    $stmt = $conn->prepare("UPDATE users SET banned = ? WHERE username = ?");
    $stmt->bind_param("is", $ban_status, $user_id);
    $stmt->execute();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Gastenboek</title>
    <style>
        .message-container {
            margin-bottom: 10px;
        }
        .message-container p {
            display: inline-block;
            margin: 0;
        }
        .delete-form,
        .ban-form {
            display: inline-block;
            margin-left: 10px;
        }
    </style>
</head>
<body>
   
    <h1>Gastenboek</h1>
    <h1> <a href="logout.php">Logout</a></h1>

    <!-- Formulier om een bericht achter te laten -->
    <form action="process.php" method="post">
        <label for="naam">Naam:</label>
        <input type="text" name="naam" required>

        <label for="bericht">Bericht:</label>
        <textarea name="bericht" rows="4" required></textarea>

        <button type="submit">Plaats bericht</button>
    </form>
    

    <!-- Weergave van eerdere berichten -->
    <div id="messages-container">
   
    </div>

    <script>
    // Pass PHP session data to JavaScript
    var username = "<?php echo $_SESSION['username']; ?>";
    var usertype = "<?php echo $_SESSION['usertype']; ?>";

    function fetchMessages() {
        fetch('fetch_messages.php')
            .then(response => response.json())
            .then(messages => {
                let html = '';
                messages.forEach(message => {
                    html += `<div class='message-container'>`;
                    html += `<p><strong>${message.naam}:</strong> ${message.bericht}<br><small>${message.datum}<br>${message.username}</small></p>`;
                    if (username == message.gebruiker || usertype == 'admin') {
                        html += `<form class='delete-form' action='delete.php' method='post'>`;
                        html += `<input type='hidden' name='bericht_id' value='${message.id}'>`;
                        html += `<button type='submit'>Verwijder</button>`;
                        html += `</form>`;
                    }
                    if (usertype == 'admin') {
                        let banButtonText = (message.banned == 1) ? "Unban" : "Ban";
                        html += `<form class='ban-form' action='index.php' method='post'>`;
                        html += `<input type='hidden' name='user_id' value='${message.gebruiker}'>`;
                        html += `<input type='hidden' name='ban_status' value='${message.banned == 1 ? 0 : 1}'>`;
                        html += `<button type='submit'>${banButtonText}</button>`;
                        html += `</form>`;
                    }
                    html += `</div>`;
                });
                document.querySelector('#messages-container').innerHTML = html;

           
                document.querySelectorAll('.ban-form').forEach(form => {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault();

                        
                        fetch('index.php', {
                            method: 'POST',
                            body: new FormData(form)
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                           
                            fetchMessages();
                        })
                        .catch(error => console.error('Error:', error));
                    });
                });
            });
    }

    // Fetch messages every second
    setInterval(fetchMessages, 1000);

    // Check ban status every 5 seconds
setInterval(checkBanStatus, 5000);

function checkBanStatus() {
    fetch('banstatus.php')
        .then(response => response.json())
        .then(data => {
            if (data.banned == 1) {
                window.location.href = 'logout.php';
            }
        })
        .catch(error => console.error('Error:', error));
}
    </script>
</body>
</html>