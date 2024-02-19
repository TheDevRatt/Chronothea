<?php
  require "includes/config.php";

  // Database Connection
  require "includes/library.php";
  $pdo = connectDB();
  
  // Include the MAIL package.
  require_once 'Mail.php';

  // Errors Array
  $errors = [];

  $passwordResetMessage = '';

  // Form submission
  if(isset($_POST['submit'])) {
    // Sanitize the user inputs.
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);

    if(empty($username)) {
      $errors['username'] = "Username field is required.";
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
      // Search in the database for the username and email.
      $stmt = $pdo->prepare('SELECT * FROM A3_3420_Users WHERE username = ? AND email = ?');
      $stmt->execute([$username, $email]);
      $user = $stmt->fetch();

      if($user) {
        // Delete any existing tokens
        $stmt = $pdo->prepare('DELETE FROM A3_3420_Password_Resets WHERE user_id = ?');
        $stmt->execute([$user['id']]);

        // Generate a token
        $token = bin2hex(random_bytes(50));
        $expires = date("Y-m-d H:i:s", strtotime('+1 hour')); // Token expires after 1 hour

        // We store the token into a dedicated password reset database
        $stmt = $pdo->prepare('INSERT INTO A3_3420_Password_Resets (user_id, token, expires) VALUES (?, ?, ?)');
        $stmt->execute([$user['id'], $token, $expires]);

        // Send an email to the user with the specific token.
        $resetLink = "https://loki.trentu.ca/~matthewmakary/3420/assn/assn3/reset_password.php?token=$token";

        // Send the email using PEAR
        $from = "Password System Reset <noreply@loki.trentu.ca>";
        $to = $user['email']; // User's Email
        $subject = "Password Reset";
        $body = "Hello,\n\nWe received a request to reset your password. Click the link below to reset your password:\n$resetLink\n\nIf this was not you, you can safely ignore this email.";
        $host = "smtp.trentu.ca";
        $headers = array ('From' => $from, 'To' => $to, 'Subject' => $subject);

        $smtp = Mail::factory('smtp', array ('host' => $host));
        $mail = $smtp->send($to, $headers, $body);

        if (PEAR::isError($mail)) {
          $passwordResetMessage = "<p>" . $mail->getMessage() . "</p>";
        } else {
          $passwordResetMessage = "<p>Password reset link has been sent to your email!</p>";
        }
      } else {
        $passwordResetMessage = "<p>If your account exists, you will receive an email with further instructions.</p>";
      }
    }
  }
?>

<!DOCTYPE html>
<html lang="en">

<!--The head tag, contains the meta information along with the title describing the page.-->
<head>
  <?php
  $PAGE_TITLE = "Forgot Password";
  include "includes/metadata.php";
  ?>
</head>

<!--The body tag, contains the body of the html file-->

<body>
  <!-- HEADER -->
  <?php include "includes/header.php"?>

    <!--The main tag, contains the majority of the form contents for creating an account.-->
    <main class="main-content">
      <h1>Forgot Your Password?</h1>
      <p>
        No Worries! If your username and email are within our databases, we'll send you a password reset link to get you back to your school sign-ups!
      </p>

      <!--Form uses the post method for data integrity, requiring username password and a checkbox to remember the login the next time.-->
      <form action="<?= htmlentities($_SERVER['PHP_SELF']) ?>" method="post" class="whole-form">
        <div class="form-group">
          <label for="username">Username:</label>
          <input type="text" id="username" name="username" placeholder="JohnDoe123" class="form-control" required />
          <?php if (isset($errors['username'])): ?>
          <span class="error"><?php echo $errors['username']; ?></span>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label for="email" class="input-label">Email:</label>
          <input type="email" id="email" name="email" placeholder="frodo@bagend.shire" class="form-control <?php echo isset($errors['email']) ? 'input-error' : '' ?>" required />
          <small>Remember that we will never request your password via email.</small>
          <?php if (isset($errors['email'])): ?>
          <span class="error"><?php echo $errors['email']; ?></span>
          <?php endif; ?>
        </div>

        <?php echo $passwordResetMessage; ?>

        <!--Submit button that sends out everything in the form.-->
        <input type="submit" value="Confirm" name="submit" class="btn" />
      </form>
    </main>

  <!-- FOOTER -->
  <?php include "includes/footer.php"?>
</body>

</html>