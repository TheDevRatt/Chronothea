<?php 
  // Code for processing Login Form.
  require "includes/config.php";

  // Check if the user had previously checked remember me.
  require "includes/library.php";

  // Connect to database
  $pdo = connectDB();

  // Check the user ID
  if(isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];

    // Validate the inputs
    $stmt = $pdo->prepare("SELECT * FROM A3_3420_Users WHERE id = ?");
    $stmt->execute([$user_id]);
  
    $user = $stmt->fetch();
  
    if($user) {
      $_SESSION['username'] = $user['username'];
      $_SESSION['logged_in'] = true;
    }
  }

  // Retrieve form inputs
  $username = $_POST['username'] ?? "";
  $password = $_POST['password'] ?? "";

  // Sanitize the inputs
  $username = htmlspecialchars($username);

  $errors = [];

  // Begin processing the form
  if(isset($_POST['submit'])) {

    // Error Checkings
    if(empty($username)) {
      $errors['username'] = "Username is required.";
    }

    if(empty($password)) {
      $errors['password'] = "Password is required";
    }

    // If no errors, proceed.
    if(empty($errors)) {
      // Validate the inputs
      $stmt = $pdo->prepare("SELECT * FROM A3_3420_Users WHERE username = ?");
      $stmt->execute([$username]);
    
      $user = $stmt->fetch();
    
      if($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['logged_in'] = true;

        // Remember username
        setcookie('username', $user['username'], time() + (60 * 60 * 24 * 30), '/');

        // Remember Me
        if(isset($_POST['remember'])) {
          setcookie('user_id', $user['id'], time() + (60 * 60 * 24 * 30), '/');
        }
    
        header("Location: index.php");
        exit();
      } else {
        $errors['login'] = "Incorrect username or password.";
      }
    }
  }
  
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  $PAGE_TITLE = "Login";
  include "includes/metadata.php";
  ?>
</head>

<body>
  <!-- HEADER -->
  <?php include "includes/header.php"?>

    <!--The main tag, contains the majority of the form contents for creating an account.-->
    <main>
      <h1>Login</h1>
      <p>
        Login or create an account to view your sign-up sheets at anytime and with greater detail!
      </p>

      <!-- Login Error -->
      <?php if(isset($errors['login'])): ?>
        <div class="error">
            <p><?php echo $errors['login']; ?></p>
        </div>
      <?php endif; ?>

      <!--Form uses the post method for data integrity, requiring username password and a checkbox to remember the login the next time.-->
      <form action="./login.php" method="post" autocomplete="off">
        <!--The div for the username input field-->
        <div class="form-group">
          <label for="username">Username:</label>
          <input type="text" id="username" name="username" value="<?php echo $_COOKIE['username'] ?? ''; ?>" placeholder="JohnDoe123" class="form-control <?php echo isset($errors['username']) ? 'input-error' : '' ?>" required />
          <?php if(isset($errors['username'])): ?>
              <span class="error-text"><?php echo $errors['username']; ?></span>
          <?php endif; ?>
        </div>

        <!--The div for the password input field-->
        <div class="form-group">
          <label for="password">Password:</label>
          <input type="password" id="password" name="password" class="form-control <?php echo isset($errors['password']) ? 'input-error' : '' ?>" required />
          <?php if(isset($errors['password'])): ?>
              <span class="error-text"><?php echo $errors['password']; ?></span>
          <?php endif; ?>
        </div>

        <!--The div for the remember me checkbox-->
        <div class="form-check">
          <input type="checkbox" id="remember" name="remember" value="remember" class="form-check-input" />
          <label for="remember" class="form-check-label">Remember Me For 30 Days</label>

          <!--Link for forgot password-->
          <a href="./forgot.php" class="forgot-password">Forgot Password?</a>
        </div>

        <!--Submit button that sends out everything in the form.-->
        <input type="submit" value="Login" name="submit" class="btn" />
      </form>
    </main>

  <!-- FOOTER -->
  <?php include "includes/footer.php"?>
</body>

</html>