<?php 
  // Code for Processing Create Account Form.
  require "includes/config.php";

  // Database Connection
  require "includes/library.php";
  $pdo = connectDB();

  $errors = [];

  if(!isset($_GET['id']) && !isset($_POST['id'])) {
    die("Sheet ID is not set in the URL or the form.");
  }
  $sheet_id = $_GET['id'] ?? $_POST['id'];

  if(isset($_POST['submit'])) {

    $title = $_POST['title'];
    $description = $_POST['description'];
    $start_event_time = $_POST['start-event-time'];
    $end_event_time = $_POST['end-event-time'] ?? null;
    $meeting_time_start = $_POST['meeting-time-start'];
    $meeting_time_end = $_POST['meeting-time-end'];
    $signups = $_POST['signups'];
    $searchable = isset($_POST['searchable']) ? 1 : 0;
    $weekly = isset($_POST['weekly']) ? 1 : 0;
    $creator_id = $_SESSION['user_id'];
    $location = $_POST['location'];

    // Validate title
    if(empty($_POST['title'])) {
      $errors['title'] = 'Title is required.';
    }

    // Validate description
    if(empty($_POST['description'])) {
        $errors['description'] = 'Description is required.';
    }

    // Validate start time
    if(empty($_POST['start-event-time'])) {
        $errors['start-event-time'] = 'Start event time is required.';
    }

    // Default to 50 if signups is empty
    if(empty($signups)) {
      $signups = 50;
    }

    // Validate total signups
    if(!is_numeric($signups) || $signups < 0) {
      $errors['signups'] = 'Total number of signups must be a number greater than or equal to 0.';
    }

    // Validate Meeting Start Time
    if(empty($_POST['meeting-time-start'])) {
      $errors['meeting-time-start'] = 'Meeting start time is required.';
    }

    // Validate Meeting End Time
    if(empty($_POST['meeting-time-end'])) {
      $errors['meeting-time-end'] = 'Meeting end time is required.';
    }

    // Validate Location
    if(empty($_POST['location'])) {
      $errors['location'] = 'Location is required.';
    }

    // Sanitize the inputs
    $title = htmlspecialchars($title);
    $description = htmlspecialchars($description);
    $start_event_time = htmlspecialchars($start_event_time);
    $end_event_time = htmlspecialchars($end_event_time);
    $signups = htmlspecialchars($signups);
    $meeting_time_start = htmlspecialchars($meeting_time_start);
    $meeting_time_end = htmlspecialchars($meeting_time_end);
    $location = htmlspecialchars($location);


    if(empty($errors)){
      // User data array
      $data = [
        'title' => $title,
        'description' => $description,
        'start_event_time' => $start_event_time,
        'end_event_time' => $end_event_time,
        'total_signups' => $signups,
        'is_searchable' => $searchable,
        'creator_id' => $creator_id,
        'meeting_time_start' => $meeting_time_start,
        'meeting_time_end' => $meeting_time_end,
        'is_weekly' => $weekly,
        'creator_id' => $creator_id,
        'location' => $location
      ];

      $updateSQL = "";
      foreach ($data as $key => $value) {
        $updateSQL .= "`" . $key . "` = ?, ";
      }
      $updateSQL = rtrim($updateSQL, ", ");

      $sql = "UPDATE A3_3420_Events SET " . $updateSQL . " WHERE id = ?";
      $data['id'] = $sheet_id;

      $stmt = $pdo->prepare($sql);
      $stmt->execute(array_values($data));

      // Update the table so the remaining signups is equal to the total signups.
      $query = "UPDATE A3_3420_Events SET remaining_signups = total_signups WHERE id = ?";
      $stmt = $pdo->prepare($query);
      $stmt->execute([$sheet_id]);
    }

  }
?>

<!DOCTYPE html>
<html lang="en">

  <!--The head tag, contains the meta information along with the title describing the page.-->
  <head>
    <?php
    $PAGE_TITLE = "Add Sheet";
    include "includes/metadata.php";
    ?>
  </head>

  <!--The body tag, contains the body of the html file-->
  <body>
    <!-- HEADER -->
    <?php include "includes/header.php"?>

    <!--The main tag, contains the majority of the form contents for creating an account.-->
    <main class="main-content">
      <h1>Edit Your Sign-up Sheet</h1>
      <p>
        Edit the details of your sheet.
      </p>

      <!--A post method for data integrity going to an arbitrary createaccount.php file contains divs for 
        appropriate seperation along with well defined labels and id's to name each section of the form. According to
        the assignment document, name, username, email and password are all required.-->
      <form action="<?= htmlentities($_SERVER['PHP_SELF']) ?>" method="post">
        <div class="form-group">
          <label for="title">Event Title:</label>
          <input
            type="text"
            id="title"
            name="title"
            placeholder="Example Title"
            class="form-control"
            required
          />
          <?php if(isset($errors['title'])): ?>
            <span class="error-text"><?php echo $errors['title']; ?></span>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label for="description">Event Description:</label>
          <textarea
            type="text"
            id="description"
            name="description"
            placeholder="Example Event Description"
            class="form-control"
            required
          ></textarea>
          <?php if(isset($errors['description'])): ?>
            <span class="error-text"><?php echo $errors['description']; ?></span>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label for="location">Location:</label>
          <input
            type="text"
            id="location"
            name="location"
            placeholder="Stohn Hall"
            class="form-control"
            required
          />
          <?php if(isset($errors['location'])): ?>
            <span class="error-text"><?php echo $errors['location']; ?></span>
          <?php endif; ?>
        </div>

        <!--I ideally would like to perform some real-time date validation here but thats not possible within HTML alone.-->

        <div class="form-group">
          <label for="start-event-time">Application Open Time:</label>
          <input
            type="date"
            id="start-event-time"
            name="start-event-time"
            class="form-control"
            required
          />
          <?php if(isset($errors['start-event-time'])): ?>
            <span class="error-text"><?php echo $errors['start-event-time']; ?></span>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label for="end-event-time">Application Closing Time:</label>
          <input
            type="date"
            id="end-event-time"
            name="end-event-time"
            class="form-control"
          />
        </div>

        <div class="form-group">
          <label for="meeting-time-start">Meeting Time Start:</label>
          <input
            type="datetime-local"
            id="meeting-time-start"
            name="meeting-time-start"
            class="form-control"
            required
          />
        </div>

        <div class="form-group">
          <label for="meeting-time-end">Meeting Time End:</label>
          <input
            type="datetime-local"
            id="meeting-time-end"
            name="meeting-time-end"
            class="form-control"
            required
          />
        </div>

        <div class="form-group">
          <label for="signups">Total Number of Signups:</label>
          <input
            type="text"
            id="signups"
            name="signups"
            placeholder="50"
            class="form-control"
          />
          <?php if(isset($errors['signups'])): ?>
            <span class="error-text"><?php echo $errors['signups']; ?></span>
          <?php endif; ?>
          <small>Enter 0 to have unlimited registrations. Default is 0.</small>
        </div>

        <!--Check boxes for dictating if the event should be searchable-->
        <div class="form-check">
          <input
            type="checkbox"
            id="searchable"
            name="searchable"
            value="1"
            class="form-check-input"
          />
          <label for="searchable" class="form-check-label">Make Event Searchable?</label>
        </div>

        <div class="form-check">
          <input
            type="checkbox"
            id="weekly"
            name="weekly"
            value="1"
            class="form-check-input"
          />
          <label for="weekly" class="form-check-label">Make Event Weekly?</label>
        </div>

        <input type="hidden" name="id" value="<?= $sheet_id ?>">
        <!--Submit button sends out everything in the form using the post method.-->
        <input class="btn" type="submit" value="Edit Event" name="submit" />
      </form>
    </main>
    
    <!-- FOOTER -->
    <?php include "includes/footer.php"?>
  </body>
</html>
