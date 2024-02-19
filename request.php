<?php
  require "includes/config.php";

  // Database Connection
  require "includes/library.php";
  $pdo = connectDB();
  
  // Include the MAIL package.
  require_once 'Mail.php';

  // Errors Array
  $errors = [];

  $linkResetMessage = '';

  // Form submission
  if(isset($_POST['submit'])) {
    // Sanitize the user inputs.
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);

    if(empty($name)) {
      $errors['name'] = "Name field is required.";
    }

    if(empty($email)) {
      $errors['email'] = "Email field is required.";
    }

    // Error checking for valid emails
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors['email'] = "Invalid email format.";
    }

    // If no errors, try to find the user
    if(empty($errors)) {
      // Search in the database for the name and email.
      $stmt = $pdo->prepare('SELECT user_name, user_email FROM A3_3420_Signups WHERE user_name = ? AND user_email = ?');
      $stmt->execute([$name, $email]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if(!$user){
        $errors['user'] = 'No user found with this name and email.';
      } else {
        // Get the events for this user.
        $stmt = $pdo->prepare('SELECT event_id FROM A3_3420_Signups WHERE user_email = ?');
        $stmt->execute([$email]);
        $userEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Send the email using PEAR
        $from = "Link Retrieval System <noreply@loki.trentu.ca>";
        $to = $email;
        $subject = "We've gathered your sign-up links.";
        $body = "Hello,\n\nWe have collected the links for you!\n";

        // Append event links to the body
        foreach($userEvents as $event) {
          $body .= "\nhttps://loki.trentu.ca/~matthewmakary/3420/assn/assn3/slotdetails.php?id=" . $event['event_id'];
        }

        $host = "smtp.trentu.ca";
        $headers = array ('From' => $from, 'To' => $to, 'Subject' => $subject);

        $smtp = Mail::factory('smtp', array ('host' => $host));
        $mail = $smtp->send($to, $headers, $body);

        if (PEAR::isError($mail)) {
          $linkResetMessage = "<p>" . $mail->getMessage() . "</p>";
        } else {
          $linkResetMessage = "<p>Your links have been sent to your email!</p>";
        }
      }
    }
  }
?>


<!DOCTYPE html>
<html lang="en">

<!--The head tag, contains the meta information along with the title describing the page.-->
<head>
  <?php
  $PAGE_TITLE = "Retrieve Links";
  include "includes/metadata.php";
  ?>
</head>

<!--The body tag, contains the body of the html file-->
<body>
  <!-- HEADER -->
  <?php include "includes/header.php"?>

    <!--The main tag, contains the majority of the form contents for creating an account.-->
    <main>
      <h1>Lost Your Links?</h1>
      <p>
        Fill out the form with your name and email and we'll send you all the links for the sheets you've signed up for.
      </p>

      <!--A simple form element that allows a guest user to retrieve links that they've signed up for.-->
      <form action="request.php" method="post">
        <div class="form-group">
          <label for="name">Name:</label>
          <input type="text" id="name" name="name" placeholder="John Doe" class="form-control" required />
          <?php if (isset($errors['name'])): ?>
          <span class="error"><?php echo $errors['name']; ?></span>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label for="email" class="input-label">Email:</label>
          <input type="email" id="email" name="email" placeholder="frodo@bagend.shire" class="form-control" required />
          <small>The email used to sign-up your sheets.</small>
          <?php if (isset($errors['email'])): ?>
          <span class="error"><?php echo $errors['email']; ?></span>
          <?php endif; ?>
        </div>

        <?php echo $linkResetMessage; ?>

        <div id="confirm">
          <input type="submit" name="submit" value="Retrieve Links" class="btn">
        </div>
      </form>
    </main>

  <!-- FOOTER -->
  <?php include "includes/footer.php"?>
</body>

</html>