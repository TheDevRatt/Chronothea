<?php 
    // Session Configuration
    require "includes/config.php";

    // Unset all the session variables
    $_SESSION = array();

    $_SESSION['logged_in'] = false;

    // Destroy the session
    session_destroy();

    

    // I was going to unset the cookies below but I'm choosing not to incase the user wishes
    // to login sometime in the future and wants the form to autofill the username. This is just code
    // I found from some of the PHP forums.

    // if(isset($_SERVER['HTTP_COOKIE'])) {
    //     $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    //     foreach($cookies as $cookie) {
    //         $parts = explode('=', $cookie);
    //         $name = trim($parts[0]);
    //         setcookie($name, '', time()-1000);
    //         setcookie($name, '', time()-1000, '/');
    //     }
    // }

    // Redirect to login page
    header("Location: login.php");
    exit;
?>