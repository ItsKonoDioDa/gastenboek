<?php
session_start();

include 'connect.php';

$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT banned FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

echo json_encode(['banned' => $user['banned']]);