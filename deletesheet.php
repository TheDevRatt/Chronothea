<?php
// Code for Processing Deleting a Sheet.
require "includes/config.php";

// Database Connection
require "includes/library.php";
$pdo = connectDB();

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['id'])) {
    $sheet_id = $_POST['id'];

    // Check if there are any associated signups
    $query = "SELECT COUNT(*) AS num_signups FROM A3_3420_Signups WHERE event_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$sheet_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $num_signups = $result['num_signups'];

    if ($num_signups > 0) {
      // If there are signups, delete them first
      try {
        $query = "DELETE FROM A3_3420_Signups WHERE event_id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$sheet_id]);
      } catch (PDOException $e) {
        die($e->getMessage());
      }
    }

    // Now delete the sheet from the table
    try {
      $query = "DELETE FROM A3_3420_Events WHERE id = ?";
      $stmt = $pdo->prepare($query);
      $success = $stmt->execute([$sheet_id]);

      if ($success) {
        header("Location: index.php");
        exit();
      } else {
        die("There was a problem deleting the sheet.");
      }
    } catch (PDOException $e) {
      die($e->getMessage());
    }
  } else {
    die("Sheet ID is not set in the POST data.");
  }
}
?>