<?php

 function redirect_user ($page = 'Game.php')
 {
     $url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
 
     $url = rtrim($url, '/\\');
 
     $url .= '/'.$page;
 
     header("Location: $url");
    exit();
 }
 
 function check_login($dbc, $username = '', $pass = '')
 {
     $errors = array();
 
     //CHECK USERNAME
     if (empty($username)) { $errors[] = 'You forgot to enter your email address.'; }
     else                  { $u = mysqli_real_escape_string($dbc, trim($username)); }
 
     //CHECK PASSWORD
     if (empty($pass)) { $errors[] = 'You forgot to enter your password.'; }
     else              { $p = mysqli_real_escape_string($dbc, trim($pass)); }
 
     if (empty($errors))
     {
         $q = "SELECT UserID, Username FROM users WHERE Username='$u' AND Password=SHA1('$p')";		
         $r = @mysqli_query ($dbc, $q);
 
         if (mysqli_num_rows($r) === 1)
        {
             $row = mysqli_fetch_array ($r, MYSQLI_ASSOC);
 
             return array(true, $row);
        }
        else
         {
             $errors[] = 'The username or the password entered do not match!.';
         }
     }
 
     return array(false, $errors);
 } 
 ?>