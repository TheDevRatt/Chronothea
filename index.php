<?php 
  // Code for processing index here
  require "includes/config.php";

  // Connect to the Database
  require "includes/library.php";
  $pdo = connectDB();

  $user_id = $_SESSION['user_id'];

  // Fetch all the events created by the current user.
  $stmt1 = $pdo->prepare("SELECT * FROM A3_3420_Events WHERE creator_id = ?");
  $stmt1->execute([$user_id]);
  $createdEvents = $stmt1->fetchAll(PDO::FETCH_ASSOC);

  // Fetch all the events the user has signed up for
  $stmt2 = $pdo->prepare("SELECT e.* FROM A3_3420_Events e JOIN A3_3420_Signups s ON e.id = s.event_id WHERE s.user_id = ?");
  $stmt2->execute([$user_id]);
  $signedUpEvents = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  $PAGE_TITLE = "Your Stuff";
  include "includes/metadata.php";
  ?>
</head>

<body>
  <!-- HEADER -->
  <?php include "includes/header.php"?>

    <!--The main tag, contains the majority of the form contents for creating an account.-->
    <main>
      <h1>Your Stuff</h1>
      <p>
        All of the things that concern you. Click on individual events for more
        details.
      </p>

      <!--H1 Tag for Accessibility between locating sign up sheets and slots you've signed up for.-->
      <h1>Sign-up sheets you've created.</h1>
      <?php
        // Display message if no events created
        if (count($createdEvents) == 0) {
          echo '<p>Seems like you don\'t have any sign-up sheets yet! Why not make one?</p>';
        }
      ?>

      <!--Table to split up all the available sheets that have been created-->
      <table class="table">
      <?php 
        $counter = 0;
        echo "<tr>";
        foreach ($createdEvents as $event) {
          $meeting_day = date('l', strtotime($event['meeting_time_start']));
          $start_time = date('H:i:s', strtotime($event['meeting_time_start']));
          $end_time = date('H:i:s', strtotime($event['meeting_time_end']));
        
          $next_meeting_start = new DateTime();
          $next_meeting_start->modify("next {$meeting_day}");
          $next_meeting_start->modify($start_time);

          $next_meeting_end = clone $next_meeting_start;
          $next_meeting_end->modify($end_time);

          $now = new DateTime();
          if ($meeting_day === $now->format('l') && $now < $next_meeting_start) {
            $next_meeting_start->modify("today");
            $next_meeting_end->modify("today");
          }

          $next_meeting_start_str = $next_meeting_start->format('Y-m-d H:i:s');
          $next_meeting_end_str = $next_meeting_end->format('Y-m-d H:i:s');
        
          echo "<td>";
          echo "<div>";
          echo "<h3>".htmlspecialchars($event['title'])."</h3>";
        
          if ($event['is_weekly'] == 1) {
            // Weekly event: Display next meeting time
            echo "<p><i class='fa-solid fa-user-group'></i> ".htmlspecialchars($event['remaining_signups'])." of ".htmlspecialchars($event['total_signups'])." slots claimed</p>";
            echo "<p><i class='fa-solid fa-clock'></i> ".$next_meeting_start->format('l, F jS, g:i A')." - ".$next_meeting_end->format('g:i A')."</p>";
          } else {
            // Non-weekly event: Display start and end days along with time
            $start_date = date("l, F jS", strtotime($event['meeting_time_start']));
            $end_date = date("l, F jS", strtotime($event['meeting_time_end']));
            echo "<p><i class='fa-solid fa-user-group'></i> ".htmlspecialchars($event['remaining_signups'])." of ".htmlspecialchars($event['total_signups'])." slots claimed</p>";
            echo "<p><i class='fa-solid fa-clock'></i> ".$start_date." - ".$end_date.", ".$start_time." - ".$end_time."</p>";
          }
        
          echo "<p><i class='fa-solid fa-location-dot'></i> ".htmlspecialchars($event['location'])."</p>";
          echo "<button onclick='openModal(".htmlspecialchars($event['id']).")' class='btn-table'>View Details</button>";
          echo "</form>";
          echo "</div>";
          echo "</td>";
        
          $counter++;
          if ($counter % 4 === 0) {
            echo "</tr><tr>";
          }
        }
        echo "</tr>";
        ?>
      </table>

      <!--The Signed up for sheets also in an H1 Tag for screen reader accessibility-->
      <h1>Slots you're signed up for</h1>

      <?php
        if (count($signedUpEvents) == 0) {
          echo '<p>You should sign up for something!</p>';
        }
      ?>

      <!--Table for the slots that have been signed up for.-->
      <table class="table">
        <?php 
        $counter = 0;
        echo "<tr>";
        foreach ($signedUpEvents as $event) {
          $meeting_day = date('l', strtotime($event['meeting_time_start']));
          $start_time = date('H:i:s', strtotime($event['meeting_time_start']));
          $end_time = date('H:i:s', strtotime($event['meeting_time_end']));
        
          $next_meeting_start = new DateTime();
          $next_meeting_start->modify("next {$meeting_day}");
          $next_meeting_start->modify($start_time);

          $next_meeting_end = clone $next_meeting_start;
          $next_meeting_end->modify($end_time);

          $now = new DateTime();
          if ($meeting_day === $now->format('l') && $now < $next_meeting_start) {
            $next_meeting_start->modify("today");
            $next_meeting_end->modify("today");
          }

          $next_meeting_start_str = $next_meeting_start->format('Y-m-d H:i:s');
          $next_meeting_end_str = $next_meeting_end->format('Y-m-d H:i:s');
        
          echo "<td>";
          echo "<div>";
          echo "<h3>".htmlspecialchars($event['title'])."</h3>";
        
          if ($event['is_weekly'] == 1) {
            // Weekly event: Display next meeting time
            echo "<p><i class='fa-solid fa-clock'></i> ".$next_meeting_start->format('l, F jS, g:i A')." - ".$next_meeting_end->format('g:i A')."</p>";
          } else {
            // Non-weekly event: Display start and end days along with time
            $start_date = date("l, F jS", strtotime($event['meeting_time_start']));
            $end_date = date("l, F jS", strtotime($event['meeting_time_end']));
            echo "<p><i class='fa-solid fa-clock'></i> ".$start_date." - ".$end_date.", ".$start_time." - ".$end_time."</p>";
          }
        
          echo "<p><i class='fa-solid fa-location-dot'></i> ".htmlspecialchars($event['location'])."</p>";
          echo "<button onclick='openModal(".htmlspecialchars($event['id']).")' class='btn-table'>View Details</button>";
          echo "</div>";
          echo "</td>";
        
          $counter++;
          if ($counter % 4 === 0) {
            echo "</tr><tr>";
          }
        }
        echo "</tr>";
        ?>
      </table>
    </main>

    <div id="modal" class="modal">
      <div class="modal-content">
        <span class="close">&times;</span>
        <iframe id="modalIframe" src="" width="100%" height="100%"></iframe>
      </div>
    </div>
  
  <!-- FOOTER -->
  <?php include "includes/footer.php"?>
</body>

</html>