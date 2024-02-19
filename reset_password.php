<?php 
    // Code for Resetting Password Here
    require "includes/config.php";

    // Database Connection
    require "includes/library.php";
    $pdo = connectDB();

    $errors = [];
    $token = "";

    // We get the token from the link or from the form
    if(isset($_GET['token'])) {
        $token = $_GET['token'];
    } elseif(isset($_POST['token'])) {
        $token = $_POST['token'];
    } else {
        $errors['token'] = 'No token provided.';
    }

    // verify the token
    $stmt = $pdo->prepare('SELECT * FROM A3_3420_Password_Resets WHERE token = ? AND expires >= NOW()');
    $stmt->execute([$token]);
    $reset = $stmt->fetch();

    if($reset) {
        $user_id = $reset['user_id'];
    } else {
        $errors['token'] = 'Invalid or expired token.';
    }

    if(isset($_POST['submit'])) {
        // Reset the password
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm-password'];

        if($password !== $confirm_password) {
            $errors['password'] = 'Passwords do not match.';
        }

        if(empty($errors)) {
            // Update the users password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE A3_3420_Users SET password_hash = ? WHERE id = ?');
            $stmt->execute([$hashed_password, $user_id]);

            // Delete the token
            $stmt = $pdo->prepare('DELETE FROM A3_3420_Password_Resets WHERE user_id = ?');
            $stmt->execute([$user_id]);

            header('Location: login.php');
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<!--The head tag, contains the meta information along with the title describing the page.-->
<head>
  <?php
  $PAGE_TITLE = "Reset Password";
  include "includes/metadata.php";
  ?>
</head>

<!--The body tag, contains the body of the html file-->

<body>
  <!-- HEADER -->
  <?php include "includes/header.php"?>

    <!--The main tag, contains the majority of the form contents for creating an account.-->
    <main class="main-content">
      <h1>Reset Password</h1>
      <p>
        Please enter a new password for your account.
      </p>

      <!--Form uses the post method for data integrity, requiring username password and a checkbox to remember the login the next time.-->
      <form action="<?= htmlentities($_SERVER['PHP_SELF']) ?>" method="post" class="whole-form">
        <input type="hidden" name="token" value="<?= htmlentities($token) ?>">
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" class="form-control <?php echo isset($errors['password']) ? 'input-error' : '' ?>" required pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" />
            <?php if(isset($errors['password'])): ?>
                <span class="error-text"><?php echo $errors['password']; ?></span>
            <?php endif; ?>
            <div class="password-requirements">
            Password must have:
            <ul>
                <li>At least 8 characters</li>
                <li>Both uppercase and lowercase characters</li>
                <li>At least one special character</li>
            </ul>
            </div>
        </div>

        <div class="form-group">
            <label for="confirm-password">Confirm Password:</label>
            <input type="password" id="confirm-password" name="confirm-password" class="form-control <?php echo isset($errors['confirm-password']) ? 'input-error' : '' ?>" required pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" />
            <?php if(isset($errors['confirm-password'])): ?>
                <span class="error-text"><?php echo $errors['confirm-password']; ?></span>
            <?php endif; ?>
        </div>

        <!--Submit button that sends out everything in the form.-->
        <input type="submit" value="Confirm" name="submit" class="btn" />
      </form>
    </main>

  <!-- FOOTER -->
  <?php include "includes/footer.php"?>
</body>

</html>