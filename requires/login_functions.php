<?php

//Function that redirects de user to another page, by default is Login.php
function redirect_user ($page = 'Login.php')
{
    //Make the url pointing to the page that we need to redirect the use
    $url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);

    $url = rtrim($url, '/\\');

    $url .= '/'.$page;

    header("Location: $url"); //Redirect the user to that url
    exit(); //Exit the script
}

//Function that checks if the login passes or not
function check_login($dbc, $username = '', $pass = '')
{
    $errors = array(); //Initialize an empty array for the possible errors

    //Check if the username is empty, if it is add an error message to the errors array
    if (empty($username))
    {
        $errors[0] = '<div class="input-group"><label for="username" class="input-group-addon"><i class="fas fa-user fa-2x"></i></label><input class="form-control is-invalid" type="text" name="username" id="username" placeholder="Type your Username" /><div class="invalid-feedback"><label for="username">You forgot to enter your username.</label></div></div>';
    }
    else { $u = mysqli_real_escape_string($dbc, trim($username)); }

    //Check if the password is empty, if it is add an error message to the errors array
    if (empty($pass))
    {
        $errors[1] = '<div class="input-group"><label for="password" class="input-group-addon"><i class="fas fa-lock fa-2x"></i></label><input class="form-control is-invalid" type="password" name="password" id="password" placeholder="Type your Password" /><div class="invalid-feedback"><label for="password">You forgot to enter your password.</label></div></div>';
    }
    else { $p = mysqli_real_escape_string($dbc, trim($pass)); }
    
    //If there were no errors, start the register process
    if (empty($errors))
    {
        //Query with a simple select that gets the UserID and the Username of the user that has the Username and the Password introduced
        $q = "SELECT UserID, Username FROM users WHERE Username='$u' AND Password=SHA1('$p')";		
        $r = @mysqli_query ($dbc, $q);
        
        //If we find that user in the database, the SELECT will return one row that has the UserID and the Username in it,
        //so the Username and the Password introduces by the user were correct
        if (mysqli_num_rows($r) === 1)
        {
            $row = mysqli_fetch_array ($r, MYSQLI_ASSOC); //Put the row into a variable with the format of an associative array

            return array(true, $row); //Return an array with a true and the row array
        }
        else
        {
            //If we did no find that user in the database, show an error message telling that the Username OR the Password were incorrect
            $errors[2] = '<div class="alert alert-danger text-center" role="alert"><strong>The Username or the Password entered are not correct!</strong></div>';
        }
    }

    return array(false, $errors); //If something went wrong return an array with a false and the errors array
}

//Simple function that checks if the user is logged in for security purposes
//Because we need this check in multiple places is better to encapsulate this in one function,
//and it will make the code more readable
function isLoggedIn()
{
    return ((isset($_SESSION['user_id']) && isset($_SESSION['username']) && isset($_SESSION['agent'])) && $_SESSION['agent'] === md5($_SERVER['HTTP_USER_AGENT']));
}
 ?>