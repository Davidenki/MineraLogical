<?php
 
 include("includes/header.html");
 
 if ($_SERVER['REQUEST_METHOD'] == 'POST')
 {
     require ('requires/mysqli_connect.php');
 
     $errors = array();
 
     if (empty($_POST['username']))
     {
         $errors[0] = '<div class="input-group"><span class="input-group-addon"><i class="fas fa-user fa-2x"></i></span><input class="form-control is-invalid" type="text" name="username" id="username" placeholder="Type your Username" /><div class="invalid-feedback"><label for="username">You forgot to enter your username.</label></div></div>';
     }
     else { $u = mysqli_real_escape_string($dbc, trim($_POST['username'])); }
     
  
      if (empty($_POST['password']))
      {
 $errors[1] = '<div class="input-group"><span class="input-group-addon"><i class="fas fa-lock-open fa-2x"></i></span><input class="form-control is-invalid" type="password" name="password" id="password" placeholder="Type your Password" /><div class="invalid-feedback"><label for="password">You forgot to enter your password.</label></div></div>';
      }
      else { $p = mysqli_real_escape_string($dbc, trim($_POST['password'])); }
      
  
      if (empty($_POST['confirm']))
      {
}
      else { $c = mysqli_real_escape_string($dbc, trim($_POST['confirm'])); }
      
  
      if (empty($_POST['email']))
      {
  $errors[3] = '<div class="input-group"><span class="input-group-addon"><i class="fas fa-envelope fa-2x"></i></span><input class="form-control is-invalid" type="email" name="email" id="email" placeholder="Type your email" /><div class="invalid-feedback"><label for="email">You forgot to enter your email.</label></div></div>';
      }
      else { $e = mysqli_real_escape_string($dbc, trim($_POST['email'])); }

 if (empty($errors))
     {
         if($p === $c)
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
 
             include ('includes/footer.html'); 
             exit();
         }
         else
         {
             $errors[4] = '<div class="alert alert-danger text-center" role="alert"><strong>Your Password and Confirm Password don\'t match!</strong></div>';
         }
     }
 }
 
 function getPostValue($name)
 {
     if(isset($_POST[$name])) { return $_POST[$name]; }
     else                     { return ''; }
 }
 
 ?>
 
 <div class="h-100 row align-items-center justify-content-center">
     <div class="col-6">
         <form class="form-control" action="Register.php" method="POST">
             <?php if(isset($errors[4])) { echo $errors[4]; }?>
             <div class="form-group">
                 <label for="username"><b>Username</b></label>
                 <?php
                 if(isset($errors[0])) { echo $errors[0]; }
                 else                  { echo '<div class="input-group"><span class="input-group-addon"><i class="fas fa-user fa-2x"></i></span><input class="form-control" type="text" name="username" id="username" placeholder="Type your Username" value="'.getPostValue('username').'"/></div>'; } 
                 ?>
             </div>
             <div class="form-group">
                  <label for="password"><b>Password</b></label>
                  <?php
                  if(isset($errors[1])) { echo $errors[1]; }

 else                  { echo '<div class="input-group"><span class="input-group-addon"><i class="fas fa-lock-open fa-2x"></i></span><input class="form-control" type="password" name="password" id="password" placeholder="Type your Password" value="'.getPostValue('password').'"/></div>'; } 
                  ?>
              </div>
              <div class="form-group">
              <label for="confirm"><b>Confirm password</b></label>
                  <?php
                  if(isset($errors[2])) { echo $errors[2]; }
else                  { echo '<div class="input-group"><span class="input-group-addon"><i class="fas fa-lock fa-2x"></i></span><input class="form-control" type="password" name="confirm" id="confirm" placeholder="Confirm the Password" value="'.getPostValue('confirm').'"/></div>'; } 
                  ?>
              </div>
              <div class="form-group">
              <label for="email"><b>Email</b></label>
                  <?php
                  if(isset($errors[3])) { echo $errors[3]; }
else                  { echo '<div class="input-group"><span class="input-group-addon"><i class="fas fa-envelope fa-2x"></i></span><input class="form-control" type="email" name="email" id="email" placeholder="Type your Email" value="'.getPostValue('email').'"/></div>'; } 
                  ?>
              </div>
              <div class="form-group">
                 <input class="btn btn-def btn-block" type="submit" value="GO!" />
             </div>
             <div class="form-group text-center">
                 <a href="Login.php">Volver al menú de LogIn</a>
             </div>
         </form>
     </div>
 </div>
 
 <?php
     include("includes/footer.html");
 ?>