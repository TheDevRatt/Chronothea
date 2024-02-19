<!-- PHP CODE FOR HIDING NAV ELEMENTS -->
<?php
  if(!isset($_SESSION['logged_in'])){
    $_SESSION['logged_in'] = false;
  }
?>

<!-- HEADER -->
<nav>
    <div class="company-header">
      <h1>Chronothea</h1>
      <p><small>Your one stop shop for all your school sign-up's</small></p>
    </div>
    <ul>
      <?php if ($_SESSION['logged_in']) { ?>
        <li><a href="./index.php">Your Stuff</a></li>
        <li><a href="./addsheet.php">Create Event</a></li>
        <li><a href="./search.php">Search Event</a></li>
        <li><a href="./account.php">Account</a></li>
        <li><a href="./logout.php">Logout</a></li>
      <?php } else if (!$_SESSION['logged_in']) { ?>
        <li><a href="./login.php">Login</a></li>
        <li><a href="./register.php">Create Account</a></li>
      <?php } ?>
        <li><a href="./request.php">Retrieve links</a></li>
    </ul>
</nav>