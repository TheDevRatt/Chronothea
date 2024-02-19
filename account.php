<?php 
    // Code for processing Login Form.
    require "includes/config.php";

    // Check if the user had previously checked remember me.
    require "includes/library.php";

    // Connect to database
    $pdo = connectDB();

    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT * FROM A3_3420_Users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Initialize an array to hold any error message from form submissions.
    $errors = array();

    // Handle the POST method for form submission.
    if($_SERVER["REQUEST_METHOD"] == "POST") {

        if($_POST['submit'] == 'Edit Account'){
        
            // Get the form input
            $name = $_POST['name'];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm-password'];
            $subscribeemail = isset($_POST['subscribeemail']) ? 1 : 0;
            $subscribepromo = isset($_POST['subscribepromo']) ? 1 : 0;

            // Sanitize the main inputs.
            $name = htmlspecialchars($name);
            $username = htmlspecialchars($username);
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);

            // Validate the input
            if (empty($name) || empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
                $errors['emptyfields'] = "Please fill in all the fields.";
            }

            // Error checking for valid emails
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Invalid email format.";
            }
        
            // Error checking for correct password layout
            if(strlen($password) < 8 || !preg_match("/[a-z]/i", $password) || !preg_match("/\d/", $password) || !preg_match("/[@$!%*?&]/", $password)) {
                $errors['password'] = "Invalid password. It must contain at least 8 characters, one letter, one number, and one special character.";
            }
        
            // Error checking for an appropriate alphanumeric username with no spaces
            if(!preg_match("/^[a-zA-Z0-9]+$/", $username)) {
                $errors['username'] = "Username can only contain alphanumeric characters and no spaces.";
            }
        
            // Error checking if the password and confirm password match
            if($password !== $confirm_password) {
                $errors['confirm-password'] = "Password confirmation does not match.";
            }

            // If there are no errors, update the user info in the database.
            if (count($errors) === 0) {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $sql = "UPDATE A3_3420_Users SET name = ?, username = ?, email = ?, password_hash = ?, receive_notifications = ?, receive_promotions = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$name, $username, $email, $passwordHash, $subscribeemail, $subscribepromo, $user_id]);
            }

            } elseif($_POST['submit'] == 'Delete Account'){
                $sql = "DELETE FROM A3_3420_Users WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$user_id]);

                // Clear session
                $_SESSION = array();
                session_destroy();

                // Redirect to login
                header("Location: login.php");
                exit();
            }

            if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] != UPLOAD_ERR_NO_FILE && !empty($_FILES['profile_image']['tmp_name'])) {
                $file = $_FILES['profile_image'];

                // get the file properties
                $fileName = $file['name'];
                $fileTmpName = $file['tmp_name'];
                $fileSize = $file['size'];
                $fileError = $file['error'];
                $fileType = $file['type'];

                // handle file errors
                if($fileError !== 0) {
                    // handle the error
                    die("There was an error uploading your file.");
                }
                
                // check if the uploaded file is an image
                if(!in_array(mime_content_type($file['tmp_name']), ['image/jpeg', 'image/png', 'image/gif'])) {
                    $errors['profile_image'] = "Invalid file type. Please upload a jpeg, png, or gif image.";
                }
                
                // check the size of the uploaded file
                if($file['size'] > 8000000) { // limit size to ~8MB
                    $errors['profile_image'] = "The uploaded file is too large. Please upload a file smaller than 8MB.";
                }

                $fileExtension = explode('.', $fileName);
                $fileActualExtension = strtolower(end($fileExtension));
                $newFileName = uniqid() . '.' . $fileActualExtension;

                $targetPath = '../../../www_data/'.$newFileName;

                if(!move_uploaded_file($fileTmpName, $targetPath)) {
                    die("There was an error moving the file.");
                }

                $query = "UPDATE A3_3420_Users SET profile_image = ? WHERE id = ?";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$newFileName, $user_id]);

                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
            }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  $PAGE_TITLE = "Your Account";
  include "includes/metadata.php";
  ?>
</head>

<body>
  <!-- HEADER -->
  <?php include "includes/header.php"?>

    <!--The main tag, contains the majority of the form contents for creating an account.-->
    <main class="main-content">
      <h1>Your Account</h1>
      <p>
        Here you can edit, view, or delete your account. You can also change your notification settings here.
      </p>

      <!-- Login Error -->
      <?php if(isset($errors['login'])): ?>
        <div class="error">
            <p><?php echo $errors['login']; ?></p>
        </div>
      <?php endif; ?>

      <!--Form uses the post method for data integrity, requiring username password and a checkbox to remember the login the next time.-->
      <form action="<?= htmlentities($_SERVER['PHP_SELF']) ?>" method="post" enctype="multipart/form-data" autocomplete="off" class="whole-form" >

      <div class="form-group" id="profile-container">
        <label for="file-upload">
            <?php 
                $imagePath = "../../../www_data/".$user['profile_image'];

                if (file_exists($imagePath)) {
                    echo "<img src='$imagePath' alt='Profile Picture' id='profile-pic' />";
                } else {
                    echo "<p>No profile picture found.</p>";
                }
            ?>
            <div id="image-overlay">
                <p>Edit</p>
            </div>
        </label>
            <input type="file" id="file-upload" name="profile_image" accept="image/*" style="display: none;">
        </div>

        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" class="form-control <?php echo isset($errors['name']) ? 'input-error' : '' ?>" />
            <?php if(isset($errors['name'])): ?>
            <span class="error-text"><?php echo $errors['name']; ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
          <label for="username">Username:</label>
          <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" placeholder="JohnDoe123" class="form-control <?php echo isset($errors['username']) ? 'input-error' : '' ?>" />
          <?php if(isset($errors['username'])): ?>
              <span class="error-text"><?php echo $errors['username']; ?></span>
          <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email" class="input-label">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="form-control <?php echo isset($errors['email']) ? 'input-error' : '' ?>" />
            <?php if(isset($errors['email'])): ?>
            <span class="error-text"><?php echo $errors['email']; ?></span>
            <?php endif; ?>
        </div>

        <!--The div for the password input field-->
        <div class="form-group">
          <label for="password">Password:</label>
          <input type="password" id="password" name="password" autocomplete="off" class="form-control <?php echo isset($errors['password']) ? 'input-error' : '' ?>" />
          <?php if(isset($errors['password'])): ?>
              <span class="error-text"><?php echo $errors['password']; ?></span>
          <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="confirm-password">Confirm Password:</label>
            <input type="password" id="confirm-password" name="confirm-password" autocomplete="off" class="form-control <?php echo isset($errors['confirm-password']) ? 'input-error' : '' ?>" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" />
            <?php if(isset($errors['confirm-password'])): ?>
            <span class="error-text"><?php echo $errors['confirm-password']; ?></span>
            <?php endif; ?>
        </div>

        <div class="form-check">
            <input type="checkbox" id="subscribeemail" name="subscribeemail" value="subscribeemail" class="form-check-input" <?php echo $user['receive_notifications'] ? 'checked' : ''; ?>/>
            <label for="subscribeemail" class="form-check-label">Receive email notifications</label>
        </div>

        <div class="form-check">
            <input type="checkbox" id="subscribepromo" name="subscribepromo" value="subscribepromo" class="form-check-input" <?php echo $user['receive_promotions'] ? 'checked' : ''; ?>/>
            <label for="subscribepromo" class="form-check-label">Receive promotional email</label>
        </div>

        <!--Submit button that sends out everything in the form.-->
        <input type="submit" value="Edit Account" name="submit" class="btn" />
        <input type="submit" value="Delete Account" name="submit" class="del-btn" id="del-btn"/>
      </form>
    </main>

  <!-- FOOTER -->
  <?php include "includes/footer.php"?>
</body>

</html>