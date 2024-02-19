<?php 
  // Code for Processing Create Account Form.
  require "includes/config.php";

  // Connection to the Database
  require "includes/library.php";
  $pdo = connectDB();

  $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
  $already_signedup = false;

  // Check if user is logged in
  $logged_in = isset($_SESSION['user_id']);

  $already_signedup = false;

  // Get the ID from the URL
  if (isset($_GET['id'])) {
    $sheet_id = $_GET['id'];

    // Check if the user has already signed up
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
      $visibility = $sheet['is_searchable'] == 1 ? "This event is searchable." : "This event is not searchable.";
      $availability = $sheet['is_searchable'] == 1 ? "Open" : "Only those with the link may sign up for a slot";
      $open_date = date('l, F j, Y - g:i A', strtotime($sheet['start_event_time']));
      $close_date = date('l, F j, Y - g:i A', strtotime($sheet['end_event_time']));
      $number_of_slots = $sheet['total_signups'];
      $claimed_slots = $sheet['remaining_signups'];

      $creator_id = $sheet['creator_id'];
      $query = "SELECT name FROM A3_3420_Users WHERE id = ?";
      $stmt = $pdo->prepare($query);
      $stmt->execute([$creator_id]);
      $user = $stmt->fetch();

      $creator = $user['name'];

      // Determine if the creator is the one looking at the sheet.
      $is_creator = ($user_id == $creator_id);

      // Fetch signups
      $signups = [];
      if($is_creator) {
        $query = "SELECT signup_timestamp, user_name, user_email FROM A3_3420_Signups WHERE event_id = ? ORDER BY signup_timestamp";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$sheet_id]);
        $signups = $stmt->fetchAll(PDO::FETCH_ASSOC);
      }
    }
  }

  if (isset($_POST['signup']) && isset($user_id)) {
    $sheet_id = $_POST['id'];

    $query = "SELECT name, email FROM A3_3420_Users WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
  
    $query = "INSERT INTO A3_3420_Signups (user_id, user_name, user_email, event_id, signup_timestamp) VALUES (?, ?, ?, ?, ?)";
    $signup_timestamp = date("Y-m-d H:i:s");
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id, $user['name'], $user['email'], $sheet_id, $signup_timestamp]);
  
    $query = "UPDATE A3_3420_Events SET remaining_signups = remaining_signups - 1 WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$sheet_id]);
  
    header("Location: {$_SERVER['PHP_SELF']}?id=$sheet_id");
    exit();
  } else if (isset($_POST['signup']) && isset($_POST['email']) && isset($_POST['id'])) {
    $sheet_id = $_POST['id'];
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $name = htmlspecialchars($_POST['name']);

    // Insert the email into A3_3420_Signups with null user_id and user_name
    $query = "INSERT INTO A3_3420_Signups (user_id, user_name, user_email, event_id, signup_timestamp) VALUES (?, ?, ?, ?, ?)";
    $signup_timestamp = date("Y-m-d H:i:s");
    $stmt = $pdo->prepare($query);
    $stmt->execute([null, $name, $email, $sheet_id, $signup_timestamp]);

    $query = "UPDATE A3_3420_Events SET remaining_signups = remaining_signups - 1 WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$sheet_id]);

    // You can also redirect to the same page if needed, forcing it to "refresh" and display updated data
    header("Location: {$_SERVER['PHP_SELF']}?id=$sheet_id");
    exit();
  }

  // Process cancellation if cancel button was pressed
  if ($logged_in && $_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['cancel'])) {
      $sheet_id = $_POST['id'];

      // Delete the signup from A3_3420_Signups
      $query = "DELETE FROM A3_3420_Signups WHERE user_id = ? AND event_id = ?";
      $stmt = $pdo->prepare($query);
      $stmt->execute([$user_id, $sheet_id]);

      // Increase the remaining_signups by 1 in A3_3420_Events
      $query = "UPDATE A3_3420_Events SET remaining_signups = remaining_signups + 1 WHERE id = ?";
      $stmt = $pdo->prepare($query);
      $stmt->execute([$sheet_id]);

      var_dump($sheet_id);

      header("Location: {$_SERVER['PHP_SELF']}?id=$sheet_id");
      exit;
      }
    }
?>

<!DOCTYPE html>
<html lang="en">
<!--The head tag, contains the meta information along with the title describing the page.-->

<head>
  <?php
  $PAGE_TITLE = "Sheet Details";
  include "includes/metadata.php";
  ?>
</head>

<!--The body tag, contains the body of the html file-->

<body>
  <!-- HEADER -->
  <?php include "includes/header.php"?>

  <main>
    <h1><?= htmlspecialchars($title) ?></h1>

    <?php if ($is_creator): ?>
      <p>View and modify the properties of this sign-up sheet.</p>
      <div class="icons">
        <a href="editsheet.php?id=<?= $sheet_id ?>">
          <button class="icon-button"><i class="fa-solid fa-pencil"></i></button>
        </a>
        
        <form method="GET" action="copysheet.php">
          <input type="hidden" name="id" value="<?= $sheet_id ?>">
          <button type="submit" class="icon-button"><i class="fa-solid fa-copy"></i></button>
        </form>
        
        <form method="POST" action="deletesheet.php" onsubmit="showConfirmation('delete this signup sheet', this); return false;">
          <input type="hidden" name="id" value="<?= $sheet_id ?>">
          <button type="submit" class="icon-button"><i class="fa-solid fa-trash"></i></button>
        </form>
      </div>
    <?php endif; ?>
    
    <h2>Sheet Details</h2>

    <!--Sheet details div for organization, created individual divs with unique ids for each part of the sheet details so that
        when pulling data from a database in the future it'll be easy to edit the contents within each.-->
    <div class="sheet-details">
      <div id="title">
        <p><strong>Title:</strong><?= htmlspecialchars($title) ?></p>
      </div>

      <div id="creator">
        <p><strong>Creator:</strong><?= htmlspecialchars($creator) ?></p>
      </div>

      <div id="event-description">
        <p>
          <strong>Description:</strong><?= htmlspecialchars($description) ?>
        </p>
      </div>

      <div id="creation-date">
        <p>
          <strong>Date Created:</strong> Monday, May 1st, 2023 at 10:37 AM
        </p>
      </div>

      <div id="visiblity">
        <p><strong>Visibility:</strong><?= htmlspecialchars($visibility) ?></p>
      </div>

      <div id="availability">
        <p><strong>Sign-up availability:</strong> <?= htmlspecialchars($availability) ?></p>
      </div>

      <div id="open-date">
        <p>
          <strong>Applications Open:</strong><?= htmlspecialchars($open_date) ?>
        </p>
      </div>

      <div id="close-date">
        <p>
          <strong>Applications Close:</strong><?= htmlspecialchars($close_date) ?>
        </p>
      </div>

    </div>

    <h2>Slots</h2>

    <!--Created a slots div for organization along with two individually seperate divs for
        number of slots and claimed slots along with the slot table, therefore when data is being pulled 
        from a database, it'll be easy to know what goes were.-->
    <div class="sheet-details">
      <div id="number-of-slots">
        <p><strong>Number Of Slots:</strong><?= htmlspecialchars($number_of_slots) ?></p>
      </div>

      <div id="claimed-slots">
        <p><strong>Remaining Slots:</strong><?= htmlspecialchars($claimed_slots) ?></p>
      </div>

      <?php if ($claimed_slots > 0): ?>
        <?php if (!isset($user_id)): ?>
          <form method="POST" action="<?= htmlentities($_SERVER['PHP_SELF']) ?>">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" placeholder="John Doe" class="form-control <?php echo isset($errors['name']) ? 'input-error' : '' ?>"  required />

            <label for="email" class="input-label">Email:</label>
            <input type="email" id="email" name="email" placeholder="frodo@bagend.shire" class="form-control <?php echo isset($errors['email']) ? 'input-error' : '' ?>" required />
            <small>We'll never share your email with anyone else.</small>
            <input type="hidden" name="id" value="<?= $sheet_id ?>">
            <input class="btn" type="submit" value="Sign Up" name="signup" />
          </form>

        <?php else: ?>
          <?php if (!$is_creator): ?>
            <?php if ($already_signedup): ?>
              <p>You've successfully signed up for this event.</p>
              <form action="<?= htmlentities($_SERVER['PHP_SELF']) ?>" method="POST" onsubmit="showConfirmation('delete this signup sheet', this, function() { form.submit(); }); return false;">
                <input type="hidden" name="id" value="<?= $sheet_id ?>" />
                <button type="submit" id="cancel" name="cancel" class="btn">Cancel Sign-Up</button>
              </form>
            <?php else: ?>
            <form method="POST" action="<?= htmlentities($_SERVER['PHP_SELF']) ?>">
              <input type="hidden" name="id" value="<?= $sheet_id ?>">
              <input class="btn" type="submit" value="Sign Up" name="signup" />
            </form>
            <?php endif; ?>
          <?php endif; ?>
        <?php endif; ?>
      <?php else: ?>
        <p>This sign-up has been filled up, please check later.</p>
      <?php endif; ?>

      <?php if ($is_creator): ?>
        <div class="sheet-table">
          <table>
            <tr>
              <th>Signup Time</th>
              <th>Name</th>
              <th>Email Address</th>
            </tr>
            <?php foreach ($signups as $signup): ?>
            <tr>
              <td><?= date('l, F j, Y - g:i A', strtotime($signup['signup_timestamp'])) ?></td>
              <td><?= htmlspecialchars($signup['user_name']) ?></td>
              <td><?= htmlspecialchars($signup['user_email']) ?></td>
            </tr>
            <?php endforeach; ?>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </main>
    <!-- FOOTER -->
    <?php include "includes/footer.php"?>
</body>

</html>