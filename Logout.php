<?php

session_start();

require("requires/login_functions.php");

if(isLoggedIn())
{
    $_SESSION = array();
    session_destroy();
    setcookie ('PHPSESSID', '', time()-3600, '/', '', 0, 0);
    redirect_user("Login.php");
    exit();
}

include("includes/header.html");
echo '<div class="row">
        <div class="col text-center">
            <h1>なぜあなたはここにいるのですか？</h1>
            <h3><a href="Login.php">ログインページにもどってください</a></h3>
        </div>
      </div>';
include("includes/footer.html");
?>