<?php 
  // Code for Processing search Form.
  require "includes/config.php";
  require "includes/library.php";
  $pdo = connectDB();

  // Fetch all the events by default
  $query = "SELECT * FROM A3_3420_Events";
  $stmt = $pdo->prepare($query);
  $stmt->execute();
  $results = $stmt->fetchAll();

  if(isset($_POST['submit']) && !empty($_POST['searchbox'])) {
    // If search term is provided, filter the events
    $search_term = "%" . $_POST['searchbox'] . "%";

    $query = "SELECT * FROM A3_3420_Events WHERE title LIKE ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$search_term]);
    $results = $stmt->fetchAll();
  }

?>

<!DOCTYPE html>
<html lang="en">

  <head>
      <?php
      $PAGE_TITLE = "Search";
      include "includes/metadata.php";
      ?>
  </head>

  <body>
    <!-- HEADER -->
    <?php include "includes/header.php"?>

    <!--Search for events, wrapped in a form tag that makes searching convenient.-->
    <main>
      <h1>Search For Events</h1>
      <p>
        Search for events in the provided textbox, results will show underneath.
      </p>

      <form action="<?= htmlentities($_SERVER['PHP_SELF']) ?>" method="post">
          <div class="search">
              <div class="search-bar">
                  <input
                      type="text"
                      id="searchbox"
                      name="searchbox"
                      class="search-input"
                      placeholder="Search for events..."
                  />
                  <input type="submit" value="Search" name="submit" class="search-button" />
              </div>
              <label for="searchbox" class="search-label visually-hidden">Search for events...</label>
          </div>
      </form>

      <!--Table to split up all the available sheets that have been created-->
      <table class="table">
          <?php 
          $counter = 0;
          echo "<tr>";
          foreach ($results as $result) {
            // Convert 24-hour format time into 12-hour format
            $time_start = date("g:i A", strtotime($result['meeting_time_start']));
            $time_end = date("g:i A", strtotime($result['meeting_time_end']));

            // Get the day of the week
            $day_of_week = date("l", strtotime($result['meeting_time_start'])); // this assumes meeting_time_start contains date

            // Check if the event is searchable
            if ($result['is_searchable'] == 1) {
              echo "<td>";
              echo "<div>";
              echo "<h3>".htmlspecialchars($result['title'])."</h3>";

              if ($result['is_weekly'] == 1) {
                // Weekly event: Display date and time
                echo "<p><i class='fa-solid fa-user-group'></i> ".htmlspecialchars($result['remaining_signups'])." of ".htmlspecialchars($result['total_signups'])." slots remaining</p>";
                echo "<p><i class='fa-solid fa-clock'></i> ".$day_of_week.", ".$time_start." - ".$time_end."</p>";
              } else {
                // Non-weekly event: Display start and end days along with time
                $start_date = date("l, F jS", strtotime($result['meeting_time_start']));
                $end_date = date("l, F jS", strtotime($result['meeting_time_end']));
                echo "<p><i class='fa-solid fa-user-group'></i> ".htmlspecialchars($result['remaining_signups'])." of ".htmlspecialchars($result['total_signups'])." slots remaining</p>";
                echo "<p><i class='fa-solid fa-clock'></i> ".$start_date." - ".$end_date.", ".$time_start." - ".$time_end."</p>";
              }

              echo "<p><i class='fa-solid fa-location-dot'></i> ".htmlspecialchars($result['location'])."</p>";
              echo "<form method='get' action='sheetdetails.php'>";
              echo "<input type='hidden' name='id' value='".htmlspecialchars($result['id'])."'>";
              echo "<input type='submit' class='btn-table' value='View Details'>";
              echo "</form>";
              echo "</div>";
              echo "</td>";

              $counter++;
              if ($counter % 4 === 0) {
                echo "</tr><tr>"; // start a new table row after every 4 table cells
              }
            }
          }
          echo "</tr>";
          ?>
        </table>
    </main>

    <!-- FOOTER -->
    <?php include "includes/footer.php"?>
  </body>

</html>