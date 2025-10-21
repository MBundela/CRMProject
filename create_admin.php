<?php
include("config.php"); // make sure this connects to your DB

$username = 'admin';
$password = password_hash('admin123', PASSWORD_DEFAULT); // hash password
$email = 'admin@example.com';

$stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
$stmt->execute([$username, $password, $email]);

echo "Admin user created!";
?>
