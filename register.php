<?php
    include("includes/header.html");
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        require ('requires/mysqli_connect.php');
        
        $errors = array();
        
        if (empty($_POST['username'])) { $errors[] = 'You forgot to enter your username.'; }
        else                           { $u = mysqli_real_escape_string($dbc, trim($_POST['username'])); }
	
        if (empty($_POST['password'])) { $errors[] = 'You forgot to enter your password.';}
        else                           { $p = mysqli_real_escape_string($dbc, trim($_POST['password'])); }
        
        if (empty($_POST['email'])) { $errors[] = 'You forgot to enter your email.'; }
        else                        { $e = mysqli_real_escape_string($dbc, trim($_POST['email'])); }
        
        if (empty($errors))
        {
	
            $q = "INSERT INTO users (Username, Password, Email) VALUES ('$u', SHA1('$p'), '$e')";		
            $r = @mysqli_query ($dbc, $q);
            if ($r)
            {
                echo '<h1>Thank you!</h1>
                <p>You are now registered.</p><br /><p><a href="Login.php">Go to Login page</a></p>';	
            }
            else
            {
                echo '<h1>System Error</h1>
                <p class="error">You could not be registered due to a system error. We apologize for any inconvenience.</p>'; 
                echo '<p>'.mysqli_error($dbc).'<br /><br />Query: '.$q.'</p>';
            }
            mysqli_close($dbc);
            //include ('includes/footer.html'); 
            exit();
	}
        else
        {
            echo '<h1>Error!</h1><p class="error">The following error(s) occurred:<br />';
            foreach ($errors as $msg) { echo " - $msg<br />\n"; }
            echo '</p><p>Please try again.</p><p><br /></p>';
	}
    }
?>

<div class="panel-login-register">
    <form action="Register.php" method="POST">
       Username<br>
       <input type="text" name="username" placeholder="Type your Username"/>
       <br>
       Password<br>
       <input type="password" name="password" placeholder="Type your Password"/>
       <br>
       Email<br>
       <input type="email" name="email" placeholder="Type your Email"/>
       <br>
       <input type="submit" value="GO!" />
    </form>
    <a href="Login.php">Volver al menú de LogIn</a>
</div>

<?php
    include("includes/footer.html");
?>