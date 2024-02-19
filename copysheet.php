<?php 
  // Code for Processing Copying a Sheet.
  require "includes/config.php";

  // Database Connection
  require "includes/library.php";
  $pdo = connectDB();

  $errors = [];

  if($_SERVER["REQUEST_METHOD"] == "GET") {
    if(isset($_GET['id'])) {
      $sheet_id = $_GET['id'];

      // Get the sheet details
      $query = "SELECT * FROM A3_3420_Events WHERE id = ?";
      $stmt = $pdo->prepare($query);
      $stmt->execute([$sheet_id]);
      $sheet = $stmt->fetch();

      if($sheet) {
        // Create a copy
        $data = [
          'creator_id' => $_SESSION['user_id'],
          'title' => $sheet['title'],
          'description' => $sheet['description'],
          'start_event_time' => $sheet['start_event_time'],
          'end_event_time' => $sheet['end_event_time'],
          'total_signups' => $sheet['total_signups'],
          'is_searchable' => $sheet['is_searchable'],
          'remaining_signups' => $sheet['remaining_signups'],
          'meeting_time_start' => $sheet['meeting_time_start'],
          'meeting_time_end' => $sheet['meeting_time_end'],
          'is_weekly' => $sheet['is_weekly'],
          'location' => $sheet['location']
        ];
        
        $fields = array_keys($data);
        $values = array_values($data);
        $placeholders = array_fill(0, count($data), '?');

        $sql = sprintf(
          'INSERT INTO A3_3420_Events (%s) VALUES (%s)',
          implode(', ', $fields),
          implode(', ', $placeholders)
        );

        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute($values);

        if($success) {
          header("Location: index.php");
          exit();
        } else {
          die("There was a problem copying the sheet.");
        }
      } else {
        die("Sheet with given ID does not exist");
      }
    } else {
      die("Sheet ID is not set in the GET data.");
    }
  }
?>