<?php

//If the user submits the form do all login things
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    require ('requires/login_functions.php'); //Require the login functions because if this fails we can not do anything
    require ('requires/mysqli_connect.php'); //Connect to the database

    //Check the login explained in the login functions and assign the return values into two variables
    list ($check, $data) = check_login($dbc, $_POST['username'], $_POST['password']);

    //If we pass the check we can log the user in
    if ($check)
    {	
        session_start(); //Start the SESSION
        
        //Assign the UserID, the Username of the data array and the HTTP_USER_AGENT, more security, to the SESSION array
        $_SESSION['user_id'] = $data['UserID'];
        $_SESSION['username'] = $data['Username'];
        
        $_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);

        redirect_user('MainMenu.php'); //Redirect he user to the main menu
    }
    else
    {
        //If it did not run okay make an errors array with the data array values in it
        $errors = $data;
    }
		
    mysqli_close($dbc); //Close the database connection
}

include("includes/header.html"); //Include the header

?>

<div class="h-100 row align-items-center justify-content-center">
    <div class="col-9 col-md-5 col-lg-4">
        <form class="form-control" action="Login.php" method="POST">
            <?php if(isset($errors[2])) { echo $errors[2]; }?><!-- If the value of errors[2] is set, show the error message-->
            <div class="form-group">
                <label for="username"><b>Username</b></label>
                <!-- If the value of errors[0] is set, show the bootstrap input with the invalid feedback,
                     if not, show a normal bootstrap form input with the sticky form function-->
                <?php
                if(isset($errors[0])) { echo $errors[0]; }
                else                  { echo '<div class="input-group"><label for="username" class="input-group-addon"><i class="fas fa-user fa-2x"></i></label><input class="form-control" type="text" name="username" id="username" placeholder="Type your Username" /></div>'; } 
                ?>
            </div>
            <div class="form-group">
                <label for="password"><b>Password</b></label>
                <!-- If the value of errors[1] is set, show the bootstrap input with the invalid feedback,
                     if not, show a normal bootstrap form input with the sticky form function-->
                <?php
                if(isset($errors[1])) { echo $errors[1]; }
                else                  { echo '<div class="input-group"><label for="password" class="input-group-addon"><i class="fas fa-lock fa-2x"></i></label><input class="form-control" type="password" name="password" id="password" placeholder="Type your Password" /></div>'; } 
                ?>
            </div>
            <div class="row">
                <div class="col-xs-12 col-lg-6">
                    <input class="btn btn-def btn-block" type="submit" value="GO!" />
                </div>
                <div class="col-xs-12 col-lg-6">
                    <div class="row">
                        <div class="col-6 col-lg-12">
                            <a href="Register.php">New Account</a>
                        </div>
                        <div class="col-6 col-md-12">
                            <a href="#">Forgot password?</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
    include("includes/footer.html"); //Include the footer
?>