<?php

session_start(); //Start the SESSION

require("requires/login_functions.php"); //Include the login functions, if it does not work do not continue executing code

//If the user is logged in, do the logout process
if(isLoggedIn())
{
    $_SESSION = array(); //Clear the SESSION array
    session_destroy(); //Destroy the SESSION
    setcookie ('PHPSESSID', '', time()-3600, '/', '', 0, 0); //Destroy the PHPSESSID cookie
    redirect_user(); //Redirect the user to de Login page, remember that the default page is Login.php
    exit(); //Exit the script in case the redirect did not work
}

//If the user was not logged in show a message in japanese just for fun with a link that redirects to the Login page
//I will let you discover what it means :P
include("includes/header.html"); //Include the header
echo '<div class="row">
        <div class="col text-center">
            <h1>なぜあなたはここにいるのですか？</h1>
            <h3><a href="Login.php">ログインページにもどってください</a></h3>
        </div>
      </div>';
include("includes/footer.html"); //Include the footer
?>