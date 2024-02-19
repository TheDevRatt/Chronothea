<?php
// Code for checking if username exists.
require "includes/config.php";

// Connection to the Database
require "includes/library.php";
$pdo = connectDB();

if (isset($_GET['username'])) {
    $username = $_GET['username'];
    
    // Check if the username already exists in the database
    $stmt = $pdo->prepare("SELECT COUNT(username) FROM A3_3420_Users WHERE username = ?");
    $stmt->execute([$username]);
    $result = $stmt->fetchColumn();

    if ($result > 0) {
        // Username is already taken
        echo json_encode(['status' => 'taken']);
    } else {
        // Username is available
        echo json_encode(['status' => 'available']);
    }
}
?>