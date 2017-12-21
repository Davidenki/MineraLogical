 <?php include("includes/header.html");
     
     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
 
 	require ('requires/login_functions.php');
 	require ('requires/mysqli_connect.php');
 		
 	list ($check, $data) = check_login($dbc, $_POST['username'], $_POST['password']);
 	
 	if ($check)
         {	
             session_start();
             $_SESSION['user_id'] = $data['UserID'];
             $_SESSION['username'] = $data['Username'];
 
             $_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);
 
             redirect_user('Game.php');		
 	}
         else
         {
             $errors = $data;
 	}
 		
 	mysqli_close($dbc); // Close the database connection.
 }
 ?>
 <div class="panel-login-register">
     <form action="Login.php" method="POST">
        Username<br>
        <input type="text" name="username" placeholder="Type your Username"/>
        <br>
        Password<br>
        <input type="password" name="password" placeholder="Type your Password"/>
        <br>
        <input type="submit" value="GO!" />
      </form>
     <p><a href="Register.php">Create New Account</a></p>
  </div>

  <?php
     include("includes/footer.html");
 ?>