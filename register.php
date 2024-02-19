<?php
// Code for Processing Create Account Form.
require "includes/config.php";

if (isset($_POST['submit'])) {

  // Connection to the Database
  require "includes/library.php";
  $pdo = connectDB();

  // Retrieve form inputs
  $name = $_POST['name'];
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $confirmPassword = $_POST['confirm-password'];
  $primaryUseCase = $_POST['usecase'];
  $receiveNotifications = isset($_POST['subscribeemail']) ? 1 : 0;
  $receivePromotions = isset($_POST['subscribepromo']) ? 1 : 0;
  $agreeTerms = isset($_POST['terms']) ? 1 : 0;

  $errors = [];

  // Error Checking for empty fields
  if (empty($name)) {
    $errors['name'] = "Name field is required.";
  }

  if (empty($username)) {
    $errors['username'] = "Username field is required.";
  }

  if (empty($email)) {
    $errors['email'] = "Email field is required.";
  }

  if (empty($password)) {
    $errors['password'] = "Password field is required.";
  }

  // Error checking for valid emails
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = "Invalid email format.";
  }

  // Error checking for correct password layout
  if (strlen($password) < 8 || !preg_match("/[a-z]/i", $password) || !preg_match("/\d/", $password) || !preg_match("/[@$!%*?&]/", $password)) {
    $errors['password'] = "Invalid password. It must contain at least 8 characters, one letter, one number, and one special character.";
  }

  // Error checking for an appropriate alphanumeric username with no spaces
  if (!preg_match("/^[a-zA-Z0-9]+$/", $username)) {
    $errors['username'] = "Username can only contain alphanumeric characters and no spaces.";
  }

  // Error checking if the password and confirm password match
  if ($password !== $confirmPassword) {
    $errors['confirm-password'] = "Password confirmation does not match.";
  }

  // Check if the username already exists
  $stmt = $pdo->prepare("SELECT COUNT(username) FROM A3_3420_Users WHERE username = ?");
  $stmt->execute([$username]);
  $result = $stmt->fetchColumn();

  if ($result > 0) {
    $errors['username'] = "Username is taken.";
  }

  // Check if the email already exists
  $stmt = $pdo->prepare("SELECT COUNT(email) FROM A3_3420_Users WHERE email = ?");
  $stmt->execute([$email]);
  $result = $stmt->fetchColumn();

  if ($result > 0) {
    $errors['email'] = "Email already in use.";
  }

  // Sanitize the main inputs.
  $name = htmlspecialchars($name);
  $username = htmlspecialchars($username);
  $email = filter_var($email, FILTER_SANITIZE_EMAIL);
  $photo = null;
  $uniqueFileName = null;

  // File upload sanitization
  if (isset($_FILES['photo']) && $_FILES['photo']['error'] != UPLOAD_ERR_NO_FILE && !empty($_FILES['photo']['tmp_name'])) {
    $photo = $_FILES['photo'];

    // check if the uploaded file is an image
    if (!in_array(mime_content_type($photo['tmp_name']), ['image/jpeg', 'image/png', 'image/gif'])) {
      $errors['photo'] = "Invalid file type. Please upload a jpeg, png, or gif image.";
    }

    // check the size of the uploaded file
    if ($photo['size'] > 8000000) { // limit size to ~8MB
      $errors['photo'] = "The uploaded file is too large. Please upload a file smaller than 8MB.";
    }

    // Generate a unique file name for the uploaded file
    $extension = pathinfo($photo['name'], PATHINFO_EXTENSION);
    $uniqueFileName = uniqid() . '.' . $extension;

    // Path to the www_data directory
    $targetPath = '../../../www_data/' . $uniqueFileName;

    // Move the file to the target directory
    if (move_uploaded_file($photo['tmp_name'], $targetPath)) {
      // File moved successfully
    } else {
      $errors['photo'] = "There was a problem uploading your file.";
    }
  }

  // Hash the password
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  // if there are no errors we can proceed with inserting to databse.
  if (empty($errors)) {
    try {
      // Insert User Data into Database
      $data = [
        'name' => $name,
        'username' => $username,
        'email' => $email,
        'password_hash' => $hashedPassword,
        'profile_image' => $uniqueFileName,
        'primary_use_case' => $primaryUseCase,
        'receive_notifications' => $receiveNotifications,
        'receive_promotions' => $receivePromotions,
        'terms_of_service_agreement' => $agreeTerms
      ];

      $fields = array_keys($data);
      $values = array_values($data);

      $placeholders = array_fill(0, count($data), '?');

      $sql = sprintf(
        'INSERT INTO A3_3420_Users (%s) VALUES (%s)',
        implode(', ', $fields),
        implode(', ', $placeholders)
      );

      $stmt = $pdo->prepare($sql);
      $stmt->execute($values);

      if ($stmt->rowCount() > 0) {
        // Account created successfully
        header("Location: login.php");
        exit();
      } else {
        // Account creation failed
        $errors['accountCreation'] = "An error occured on our end. We are unable to complete your request at this time.";
      }
    } catch (PDOException $e) {
      if ($e->errorInfo[1] == 1062) {
        $errors['username'] = "This username is already taken.";
      } else {
        echo "PDOException: " . $e->getMessage();
      }
    } catch (Exception $e) {
      echo "Exception: " . $e->getMessage();
    }
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  $PAGE_TITLE = "Create Account";
  include "includes/metadata.php";
  ?>
</head>

<body>
  <!-- HEADER -->
  <?php include "includes/header.php" ?>

  <main>
    <div>
      <h1>Create Account</h1>
      <p>
        Create an account with us or login and have greater control over all your school sign-ups and registrations!
      </p>

      <!-- Account Creation Error Message -->
      <?php if (isset($errors['accountCreation'])) : ?>
        <div class="error">
          <p><?php echo $errors['accountCreation']; ?></p>
        </div>
      <?php endif; ?>

      <form action="<?= htmlentities($_SERVER['PHP_SELF']) ?>" method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="form-group">
          <label for="name">Name:</label>
          <input type="text" id="name" name="name" placeholder="John Doe" class="form-control <?php echo isset($errors['name']) ? 'input-error' : '' ?>" required />
          <?php if (isset($errors['name'])) : ?>
            <span class="error-text"><?php echo $errors['name']; ?></span>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label for="username">Username:</label>
          <input type="text" id="username" name="username" placeholder="ringbearer1" class="form-control <?php echo isset($errors['username']) ? 'input-error' : '' ?>" required />
          <?php if (isset($errors['username'])) : ?>
            <span class="error-text"><?php echo $errors['username']; ?></span>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label for="email" class="input-label">Email:</label>
          <input type="email" id="email" name="email" placeholder="frodo@bagend.shire" class="form-control <?php echo isset($errors['email']) ? 'input-error' : '' ?>" required />
          <?php if (isset($errors['email'])) : ?>
            <span class="error-text"><?php echo $errors['email']; ?></span>
          <?php endif; ?>
          <small>We'll never share your email with anyone else.</small>
        </div>

        <div class="form-group">
          <label for="password">Password: <i class="fa-solid fa-eye" id="passwordToggle"></i></label>
          <input type="password" id="password" name="password" class="form-control <?php echo isset($errors['password']) ? 'input-error' : '' ?>" required pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" />
          <?php if (isset($errors['password'])) : ?>
            <span class="error-text"><?php echo $errors['password']; ?></span>
          <?php endif; ?>
          <div class="password-requirements">
            Password must have:
            <ul id="password-requirements">
              <li id="password-length">At least 8 characters <span></span></li>
              <li id="password-letter">Contains a letter <span></span></li>
              <li id="password-number">Contains a number <span></span></li>
              <li id="password-special">Contains a special character <span></span></li>
            </ul>
            <ul id="password-strength"></ul>
          </div>
        </div>

        <div class="form-group">
          <label for="confirm-password">Confirm Password: <i class="fa-solid fa-eye" id="passwordToggle"></i></label>
          <input type="password" id="confirm-password" name="confirm-password" class="form-control <?php echo isset($errors['confirm-password']) ? 'input-error' : '' ?>" required pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" />
          <?php if (isset($errors['confirm-password'])) : ?>
            <span class="error-text"><?php echo $errors['confirm-password']; ?></span>
          <?php endif; ?>
        </div>

        <div class="file-upload">
          <input type="file" id="photo" name="photo" class="file-upload-input" />
          <label for="photo" class="file-upload-label">Choose photo</label>
          <?php if (isset($errors['photo'])) : ?>
            <span class="error-text"><?php echo $errors['photo']; ?></span>
          <?php endif; ?>
        </div>

        <fieldset class="form-group">
          <legend>Intended Primary Use-Case</legend>
          <div>
            <input type="radio" id="work" name="usecase" value="Work" />
            <label for="work">Work</label>
          </div>

          <div>
            <input type="radio" id="education" name="usecase" value="Education" />
            <label for="education">Education</label>
          </div>

          <div>
            <input type="radio" id="personal" name="usecase" value="Personal" />
            <label for="personal">Personal</label>
          </div>

          <div>
            <input type="radio" id="other" name="usecase" value="Other" />
            <label for="other">Other</label>
          </div>
        </fieldset>

        <div class="form-check">
          <input type="checkbox" id="subscribeemail" name="subscribeemail" value="subscribeemail" class="form-check-input" />
          <label for="subscribeemail" class="form-check-label">Receive email notifications</label>
        </div>

        <div class="form-check">
          <input type="checkbox" id="subscribepromo" name="subscribepromo" value="subscribepromo" class="form-check-input" />
          <label for="subscribepromo" class="form-check-label">Receive promotional email</label>
        </div>

        <div class="checkbox-container">
          <input type="checkbox" id="terms" name="terms" value="terms" class="form-check-input" required />
          <label for="terms" class="form-check-label">
            I have read and agree to the terms of service and privacy policy
          </label>
        </div>

        <input class="btn" type="submit" value="Create Account" name="submit" />
      </form>
    </div>
  </main>

  <!-- FOOTER -->
  <?php include "includes/footer.php" ?>
</body>

</html>