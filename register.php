<?php

include("includes/header.html"); //Include the header

//If the user submits the form do all register things
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    require ('requires/mysqli_connect.php'); //Connect to the database

    $errors = array(); //Initialize an empty array for the possible errors

    //Check if the username is empty, if it is add an error message to the errors array
    if (empty($_POST['username']))
    {
        $errors[0] = '<div class="input-group"><label for="username" class="input-group-addon"><i class="fas fa-user fa-2x"></i></label><input class="form-control is-invalid" type="text" name="username" id="username" placeholder="Type your Username" /><div class="invalid-feedback"><label for="username">You forgot to enter your username.</label></div></div>';
    }
    else { $u = mysqli_real_escape_string($dbc, trim($_POST['username'])); }
    
    //Check if the password is empty, if it is add an error message to the errors array
    if (empty($_POST['password']))
    {
        $errors[1] = '<div class="input-group"><label for="password" class="input-group-addon"><i class="fas fa-lock-open fa-2x"></i></label><input class="form-control is-invalid" type="password" name="password" id="password" placeholder="Type your Password" /><div class="invalid-feedback"><label for="password">You forgot to enter your password.</label></div></div>';
    }
    else { $p = mysqli_real_escape_string($dbc, trim($_POST['password'])); }
    
    //Check if the confirm password is empty, if it is add an error message to the errors array
    if (empty($_POST['confirm']))
    {
        $errors[2] = '<div class="input-group"><label for="confirm" class="input-group-addon"><i class="fas fa-lock fa-2x"></i></label><input class="form-control is-invalid" type="password" name="confirm" id="confirm" placeholder="Confirm your password" /><div class="invalid-feedback"><label for="confirm">You forgot to confirm your password.</label></div></div>';
    }
    else { $c = mysqli_real_escape_string($dbc, trim($_POST['confirm'])); }
    
    //Check if the email is empty, if it is add an error message to the errors array
    if (empty($_POST['email']))
    {
        $errors[3] = '<div class="input-group"><label for="email" class="input-group-addon"><i class="fas fa-envelope fa-2x"></i></label><input class="form-control is-invalid" type="email" name="email" id="email" placeholder="Type your email" /><div class="invalid-feedback"><label for="email">You forgot to enter your email.</label></div></div>';
    }
    else { $e = mysqli_real_escape_string($dbc, trim($_POST['email'])); }
    

    //If there were no errors, start the register process
    if (empty($errors))
    {
        //The password and confirm password are exactly the same?
        if($p === $c)
        {
            //This query has two instructions in it, the first one calls an stored procedure with an output value, the second one catches this value with a SELECT statement
            $q = "CALL InsertUser('$u', '$p', '$e', @p_Result);SELECT @p_Result AS res;";
            $r = @mysqli_multi_query ($dbc, $q); //Run a multy query because the query has two instructions in it
            
            //Check if it ran without errors
            if($r)
            {
                mysqli_next_result($dbc);
                $rs = mysqli_store_result($dbc);
                $result = mysqli_fetch_object($rs)->res;
                if($result == -1)
                {
                    //Result = -1 means that the Username introduced was already in the database, show an error message
                    echo '<h1>Error</h1>
                    <p class="error">The Username introduced already exists.</p>'; 
                }
                else if($result == -2)
                {
                    //Result = -2 means that the email introduced was already in the database, show an error message
                    echo '<h1>Error</h1>
                    <p class="error">The Email introduced already exists.</p>';                     
                }
                else
                {
                    //If we are here that means the registration was successful
                    echo '<h1>Thank you!</h1>
                    <p>You are now registered.</p><br /><p><a href="Login.php">Go to Login page</a></p>';                
                }
            }
            else
            {
                //If it did not run correctly, show an error message
                echo '<h1>System Error</h1>
                <p class="error">You could not be registered due to a system error. We apologize for any inconvenience.</p>'; 

                echo '<p>'.mysqli_error($dbc).'<br /><br />Query: '.$q.'</p>';
            }
            
            mysqli_close($dbc); //Close the database connection

            include ('includes/footer.html'); //Include the footer
            exit(); //Exit the script
        }
        else
        {
            //If password and confirm password are not the same, add an error message to the errors array
            $errors[4] = '<div class="alert alert-danger text-center" role="alert"><strong>Your Password and Confirm Password don\'t match!</strong></div>';
        }
    }


//Little function that returns the value of the key of the POST array if it is set, sticky forms functionality
//This function is for a better read and encapsulation of the code
function getPostValue($name)
{
    if(isset($_POST[$name])) { return $_POST[$name]; }
    else                     { return ''; }
}

?>

<div class="h-100 row align-items-center justify-content-center">
    <div class="col-6">
        <form class="form-control" action="Register.php" method="POST">
            <?php if(isset($errors[4])) { echo $errors[4]; }?> <!-- If the value of errors[4] is set, show the error message-->
            <div class="form-group">
                <label for="username"><b>Username</b></label>
                <!-- If the value of errors[0] is set, show the bootstrap input with the invalid feedback,
                     if not, show a normal bootstrap form input with the sticky form function-->
                <?php
                if(isset($errors[0])) { echo $errors[0]; }
                else                  { echo '<div class="input-group"><label for="username" class="input-group-addon"><i class="fas fa-user fa-2x"></i></label><input class="form-control" type="text" name="username" id="username" placeholder="Type your Username" value="'.getPostValue('username').'"/></div>'; } 
                ?>
            </div>
            <div class="form-group">
                <label for="password"><b>Password</b></label>
                <!-- If the value of errors[1] is set, show the bootstrap input with the invalid feedback,
                     if not, show a normal bootstrap form input with the sticky form function-->
                <?php
                if(isset($errors[1])) { echo $errors[1]; }
                else                  { echo '<div class="input-group"><label for="password" class="input-group-addon"><i class="fas fa-lock-open fa-2x"></i></label><input class="form-control" type="password" name="password" id="password" placeholder="Type your Password" value="'.getPostValue('password').'"/></div>'; } 
                ?>
            </div>
            <div class="form-group">
            <label for="confirm"><b>Confirm password</b></label>
                <!-- If the value of errors[2] is set, show the bootstrap input with the invalid feedback,
                     if not, show a normal bootstrap form input with the sticky form function-->
                <?php
                if(isset($errors[2])) { echo $errors[2]; }
                else                  { echo '<div class="input-group"><label for="confirm" class="input-group-addon"><i class="fas fa-lock fa-2x"></i></label><input class="form-control" type="password" name="confirm" id="confirm" placeholder="Confirm the Password" value="'.getPostValue('confirm').'"/></div>'; } 
                ?>
            </div>
            <div class="form-group">
            <label for="email"><b>Email</b></label>
                <!-- If the value of errors[3] is set, show the bootstrap input with the invalid feedback,
                     if not, show a normal bootstrap form input with the sticky form function-->
                <?php
                if(isset($errors[3])) { echo $errors[3]; }
                else                  { echo '<div class="input-group"><label for="email" class="input-group-addon"><i class="fas fa-envelope fa-2x"></i></label><input class="form-control" type="email" name="email" id="email" placeholder="Type your Email" value="'.getPostValue('email').'"/></div>'; } 
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
    include("includes/footer.html"); // Include the footer
?>