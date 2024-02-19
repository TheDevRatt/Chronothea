<?php 
  // Code for processing slot details here.
  require "includes/config.php";

  // Connect to the Database
  require "includes/library.php";
  $pdo = connectDB();

  // Check if user is logged in
  $logged_in = isset($_SESSION['user_id']);

  $already_signedup = false;
  $sheet_id = null;

  $user_id = null;
  if($logged_in) {
    $user_id = $_SESSION['user_id'];
  }

  // Process viewing of full sheet details if button was pressed
  if ($logged_in && $_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['view'])) {
      $sheet_id = $_POST['sheet_id'];

      // Redirect the user to the full SlotDetails page
      header("Location: slotdetails.php?id=" . urlencode($sheet_id));
      exit();
      }
    }
  

  // Get ID from URL
  if ($logged_in && isset($_GET['id'])) {
    $sheet_id = $_GET['id'];

    // Premptively check if the user has signed up for this event
    $query = "SELECT * FROM A3_3420_Signups WHERE user_id = ? AND event_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id, $sheet_id]);
    $signup = $stmt->fetch();
    if($signup) {
      $already_signedup = true;
    }

    // Retrieve the sheet details from the events table
    $query = "SELECT * FROM A3_3420_Events WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$sheet_id]);
    $sheet = $stmt->fetch();

    if($sheet) {
      $title = $sheet['title'];
      $description = $sheet['description'];
      if($logged_in && $signup) {
        $signup_date = date('l, F j, Y - g:i A', strtotime($signup['signup_timestamp']));
      }

      $creator_id = $sheet['creator_id'];
      $query = "SELECT name FROM A3_3420_Users WHERE id = ?";
      $stmt = $pdo->prepare($query);
      $stmt->execute([$creator_id]);
      $user = $stmt->fetch();

      $creator = $user['name'];
    }
  } else {
    $sheet_id = $_GET['id'];

    // Retrieve the sheet details from the events table
    $query = "SELECT * FROM A3_3420_Events WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$sheet_id]);
    $sheet = $stmt->fetch();

    if($sheet) {
      $title = $sheet['title'];
      $description = $sheet['description'];
      if($logged_in && $signup) {
        $signup_date = date('l, F j, Y - g:i A', strtotime($signup['signup_timestamp']));
      }

      $creator_id = $sheet['creator_id'];
      $query = "SELECT name FROM A3_3420_Users WHERE id = ?";
      $stmt = $pdo->prepare($query);
      $stmt->execute([$creator_id]);
      $user = $stmt->fetch();

      $creator = $user['name'];
    }
  }

?>

<!DOCTYPE html>
<html lang="en">

  <head>
    <?php
    $PAGE_TITLE = "Sheet Details";
    include "includes/metadata.php";
    ?>
  </head>

  <body>

    <main>
        <h1><?php echo htmlspecialchars($title); ?></h1>

        <!--This is literally just sheet details but with key elements removed as its made for logged in users to view their sign up sheet, they aren't the creators.-->
        <div class="sheet-details">
          <div id="title">
            <p><strong>Title:</strong> <?php echo htmlspecialchars($title); ?></p>
          </div>

          <div id="creator">
            <p><strong>Creator:</strong> <?php echo htmlspecialchars($creator); ?></p>
          </div>

          <div id="description">
            <p>
              <strong>Description:</strong> <?php echo nl2br(htmlspecialchars($description)); ?>
            </p>
          </div>

          <?php if($logged_in && isset($signup_date)): ?>
            <div id="sign-up-date">
              <p>
                <strong>Date Signed Up:</strong> <?php echo htmlspecialchars($signup_date); ?> UTC.
              </p>
            </div>
          <?php endif; ?>
        </div>

        <!--Cancel button added in case user wants to leave this particular sheet.-->
        <?php if ($logged_in): ?>
          <div>
            <form action="<?= htmlentities($_SERVER['PHP_SELF']) ?>" method="post" onsubmit="return window.parent.handleSubmit(event)">
              <input type="hidden" name="sheet_id" value="<?php echo htmlspecialchars($sheet_id); ?>" />
            </form>
          </div>
        <?php endif; ?>
    </main>
  </body>

</html>