<?php
include 'connect.php';

$result = $conn->query("SELECT berichten.*, users.banned, users.username
                    FROM berichten 
                    JOIN users ON berichten.gebruiker = users.username 
                    ORDER BY berichten.datum DESC");

if (!$result) {
    echo json_encode(['error' => $conn->error]);
    exit();
}

$messages = array();
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);