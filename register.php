<?php

 include("includes/header.html");
 
 if ($_SERVER['REQUEST_METHOD'] == 'POST')
 {
     require ('requires/mysqli_connect.php');
 
    $errors = array();
 
     if (empty($_POST['username'])) { $errors[0] = '<label>You forgot to enter your username.</label>'; }
     else                           { $u = mysqli_real_escape_string($dbc, trim($_POST['username'])); }
 
     if (empty($_POST['password'])) { $errors[1] = '<label>You forgot to enter your password.</label>';}
     else                           { $p = mysqli_real_escape_string($dbc, trim($_POST['password'])); }
 
     if (empty($_POST['confirm'])) { $errors[2] = '<label>You forgot to enter your confirm password.</label>';}
     else                          { $c = mysqli_real_escape_string($dbc, trim($_POST['confirm'])); }
 
     if (empty($_POST['email'])) { $errors[3] = '<label>You forgot to enter your email.</label>'; }
    else                        { $e = mysqli_real_escape_string($dbc, trim($_POST['email'])); }

     if (empty($errors))
      {
{
	     $q = "CALL InsertUser('$u', '$p', '$e', @p_Result);SELECT @p_Result AS res;";
             $r = @mysqli_multi_query ($dbc, $q);
            
             if($r)
{
		mysqli_next_result($dbc);
                 $rs = mysqli_store_result($dbc);
                 $result = mysqli_fetch_object($rs)->res;
                 if($result == -1)
                 {
                     echo '<h1>Error</h1>
                    <p class="error">The Username introduced already exists.</p>'; 
                 }
                 else if($result == -2)
                 {
                     echo '<h1>Error</h1>
                    <p class="error">The Email introduced already exists.</p>';                     
                 }
                 else
                 {
                      echo '<h1>Thank you!</h1>
                     <p>You are now registered.</p><br /><p><a href="Login.php">Go to Login page</a></p>';	                   
                 }
	}
              else
              {
echo '<h1>System Error</h1>
                 <p class="error">You could not be registered due to a system error. We apologize for any inconvenience.</p>'; 
  
                  echo '<p>'.mysqli_error($dbc).'<br /><br />Query: '.$q.'</p>';
              }

 mysqli_close($dbc);

exit();
}
else
{
$errors[4] = "<label>Your password and confirm password do not match!.</label>";
        }  
}
}

?>
<div class="h-100 row align-items-center">
     <div class="col">
         <div class="user-form">
             <?php if(isset($errors[4])) { echo $errors[4]; }?>
             <form action="Register.php" method="POST">
                 <p>Username<br />
                <input type="text" name="username" placeholder="Type your Username"/> <?php if(isset($errors[0])) { echo $errors[0]; }?>
                 </p>
                 <p>Password<br />
                 <input type="password" name="password" placeholder="Type your Password"/> <?php if(isset($errors[1])) { echo $errors[1]; }?>
                 </p>
                 <p>Confirm Password<br />
                 <input type="password" name="confirm" placeholder="Confirm your Password"/> <?php if(isset($errors[2])) { echo $errors[2]; }?>
                 </p>
                 <p>Email<br />
                 <input type="email" name="email" placeholder="Type your Email"/> <?php if(isset($errors[3])) { echo $errors[3]; }?>
                 </p>
                <input type="submit" value="GO!" />
             </form>
             <br />
             <a href="Login.php">Volver al menú de LogIn</a>
         </div>
     </div>
  </div>
  
  <?php
include("includes/footer.html");
 ?>